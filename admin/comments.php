<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
   header("location: dashboard.php");
   exit();
}

$msg = "";
if(isset($_REQUEST['cid'])) {
    $msg = '<div class="alert alert-success">1 comment moved Trash</div>';
}
//Bulk Update
if(isset($_REQUEST['action']) && isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {
    if (isset($_REQUEST['comments'])) {
        $count = 0;
        $value = "";
        switch($_REQUEST['action']) {
            case "unapprove" :
                $value = "pending";
                $action_msg = "become unapproved";
                break;
            case "approve" : 
                $value = "approved";
                $action_msg = "approved";
                break;
            case "spam" : 
                $value = "spam";
                $action_msg = "marked as spam";
                break;
            case "unspam" : 
                $value = "unspam";
                $action_msg = "removed from spam";
                break;
            case "trash" : 
                $value = "trash";
                $action_msg = "moved to trash";
                break;
            case "untrash" :
                $value = "untrash";
                $action_msg = "restored";
                break;
            case "delete" : 
                $value = "delete";
                $action_msg = "deleted";
        }
        foreach ($_REQUEST['comments'] as $cid) {
            $cid = filter_var(trim($cid), FILTER_SANITIZE_NUMBER_INT);
            $tmpcomment = getComment($cid, array(), true);
            if ($value == "delete" && deleteComment($cid)) {
                $count++;
            } else {
                if (in_array($value, array("spam", "trash"))) {
                    $tmppost = getPost($tmpcomment['post_id'], array('id', 'comment_count'));
                    $new_comment_count = $tmpcomment['status'] <> "trash" ? ($tmppost['comment_count'] - 1) : $tmppost['comment_count'];
                    $tmpcomment['metas']['last_status'] = $tmpcomment['status'];
                    $tmpcomment['status'] = $value;
                    if(updateComment($tmpcomment)) {
                        update(T_POSTS, array("comment_count" => $new_comment_count), array("id" => $tmppost['id']));
                        $count++;
                    }
                } else if (in_array($value, array("unspam", "untrash"))) {
                    $tmpcomment['status'] = isset($tmpcomment['metas']['last_status']) ? $tmpcomment['metas']['last_status'] : "pending";
                    if (updateComment($tmpcomment)) {
                        $tmppost = getPost($tmpcomment['post_id'], array('id', 'comment_count'));
                        update(T_POSTS, array("comment_count" => $tmppost['comment_count'] + 1), array("id" => $tmppost['id']));
                        $count++;
                    }
                } else {
                    $tmpcomment['status'] = $value;
                    if(updateComment($tmpcomment)) {
                        $count++;
                    }
                }
            }
        }
        $msg = '<div class="alert alert-success">' . $count . ' comments ' . $action_msg . '</div>';
    }
}

$filters = array();
$querystring = $status = $type = "";
$post = null;
if(isset($_REQUEST['status'])) {
    $filters['status'] = filter_var(trim($_REQUEST['status']), FILTER_SANITIZE_STRING);
    $status = filter_var(trim($_REQUEST['status']), FILTER_SANITIZE_STRING);
    $querystring .= "&status=" . $_REQUEST['status'];
} else {
    $filters['statuses'] = array("pending", "approved");
}
if(isset($_REQUEST['post_id'])) {
    $filters['post_id'] = filter_var(trim($_REQUEST['post_id']), FILTER_SANITIZE_NUMBER_INT);
    $post = getPost($filters['post_id']);
    $querystring .= "&post_id=" . $_REQUEST['post_id'];
}
if(isset($_REQUEST['comment_type'])) {
    $type = filter_var(trim($_REQUEST['comment_type']), FILTER_SANITIZE_STRING);
    switch ($type) {
        case '' : $filters['type'] = null;
            break;
        case 'comments' : $filters['type'] = "";
            break;
        case 'pings' : $filters['type'] = "ping";
    }
    $querystring .= "&type=" . $_REQUEST['comment_type'];
}
if(isset($_REQUEST['q'])) {
    $filters['q'] = filter_var(trim($_REQUEST['q']), FILTER_SANITIZE_STRING);
    $querystring .= "&q=" . $_REQUEST['q'];
}

/*pagination logic start*/
$items_count = count(getComments(array('id'), $filters, 0, -1));
$items_per_page = isset($config['items_per_page_comment_admin']) ? $config['items_per_page_comment_admin'] : 20;
$max_pages = intval($items_count / $items_per_page + 1);
$current_page = !isset($_REQUEST['paged']) || intval($_REQUEST['paged']) < 1 ? 1 : filter_var(trim($_REQUEST['paged']), FILTER_SANITIZE_NUMBER_INT);
if($current_page > $max_pages) {
    header("location: comments.php?" . $querystring . "&paged=" . $max_pages);
    exit();
}
$offset = $items_per_page * $current_page - $items_per_page;
/*pagination logic end*/
$order_by = isset($_REQUEST['order_by']) && in_array($_REQUEST['order_by'], array("author_name", "post_id", "comment_datetime")) ? filter_var(trim($_REQUEST['order_by']), FILTER_SANITIZE_STRING) : "id";
$order = isset($_REQUEST['order']) && in_array($_REQUEST['order'], array("asc", "desc")) ? filter_var(trim($_REQUEST['order']), FILTER_SANITIZE_STRING) : "DESC";
$comments = getComments(array(), $filters, $offset, $items_per_page, $order_by, $order);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Comments - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link rel="stylesheet" href="<?= $sys['site_url']; ?>/admin/plugins/iCheck/flat/blue.css">
        <style>
             #comments {
                margin-bottom: 0;
            }
            .pending {
                background-color: #fef7f1 !important;
            }
        </style>
    </head>
    <!--
    BODY TAG OPTIONS:
    =================
    Apply one or more of the following classes to get the
    desired effect
    |---------------------------------------------------------|
    | SKINS         | skin-blue                               |
    |               | skin-black                              |
    |               | skin-purple                             |
    |               | skin-yellow                             |
    |               | skin-red                                |
    |               | skin-green                              |
    |---------------------------------------------------------|
    |LAYOUT OPTIONS | fixed                                   |
    |               | layout-boxed                            |
    |               | layout-top-nav                          |
    |               | sidebar-collapse                        |
    |               | sidebar-mini                            |
    |---------------------------------------------------------|
    -->
    <body class="skin-blue sidebar-mini">
        <div class="wrapper">

            <!-- Main Header -->
            <?php include 'header.php'; ?>
            <!-- Left side column. contains the logo and sidebar -->
            <?php include 'left_sidebar.php'; ?>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Comments <?= !empty($post) ? ' on "<a href="post-edit.php?id=' . $post['id'] . '">' . $post['post_title'] . '</a>"' : '' ?>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>                        
                        <li class="active">Comments</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form id="comments-form" method="get">
                        <?= $msg ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="" style="float:left">
                                    <a href="comments.php<?= $post != null ? "?post_id=" . $post['id'] : "" ?>" class="<?= $status == "" ? "current" : "" ?>">All<span class="count">(<span class="all-count"><?= count(getComments(array('id'), array('statuses' => array("approved", "pending"), 'post_id' => ($post != null ? $post['id'] : null)), 0, -1)) ?></span>)</span></a> |
                                    <a href="comments.php?status=pending<?= $post != null ? "&post_id=" . $post['id'] : "" ?>" class="<?= $status == "pending" ? "current" : "" ?>">Pending<span class="count">(<span class="pending-count"><?= count(getComments(array('id'), array('status' => "pending", 'post_id' => ($post != null ? $post['id'] : null)), 0, -1)) ?></span>)</span></a> |
                                    <a href="comments.php?status=approved<?= $post != null ? "&post_id=" . $post['id'] : "" ?>" class="<?= $status == "approved" ? "current" : "" ?>">Approved<span class="count">(<span class="approved-count"><?= count(getComments(array('id'), array('status' => "approved", 'post_id' => ($post != null ? $post['id'] : null)), 0, -1)) ?></span>)</span></a> |
                                    <a href="comments.php?status=spam<?= $post != null ? "&post_id=" . $post['id'] : "" ?>" class="<?= $status == "spam" ? "current" : "" ?>">Spam<span class="count">(<span class="spam-count"><?= count(getComments(array('id'), array('status' => "spam", 'post_id' => ($post != null ? $post['id'] : null)), 0, -1)) ?></span>)</span></a> |
                                    <a href="comments.php?status=trash<?= $post != null ? "&post_id=" . $post['id'] : "" ?>" class="<?= $status == "trash" ? "current" : "" ?>">Trash<span class="count">(<span class="trash-count"><?= count(getComments(array('id'), array('status' => "trash", 'post_id' => ($post != null ? $post['id'] : null)), 0, -1)) ?></span>)</span></a>                            
                                </div>
                                <div class="" style="float:right">
                                    <input type="text" class="form-control" name="q" value="<?= isset($_REQUEST['q']) ? $_REQUEST['q'] : "" ?>" style="width:auto; float:left;padding: 0px 2px;max-height: 30px;">
                                    <input type="submit" style="float:left;" name="search" value="Search Comments" class="btn btn-default btn-sm">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="margin: 3px 0px;">
                                <div class="pull-left actions" style="width: inherit;">
                                    <select name="action" id="bulk-action-selector-top" style="max-width: 150px;float: left;padding: 0px 5px;max-height: 30px;margin-right: 2px;" class="form-control">
                                        <option value="-1">Bulk Actions</option>
                                        <?php if(in_array($status, array("", "approved"))) { ?>
                                        <option value="unapprove">Unapprove</option>
                                        <?php } ?>
                                        <?php if(in_array($status, array("", "pending"))) { ?>
                                        <option value="approve">Approve</option>
                                        <?php } ?>
                                        <?php if(in_array($status, array("", "approved", "pending", "trash"))) { ?>
                                        <option value="spam">Mark as Spam</option>
                                        <?php } ?>
                                        <?php if(in_array($status, array("", "approved", "pending"))) { ?>
                                        <option value="trash">Move to Trash</option>
                                        <?php } ?>
                                        <?php if($status == "spam") { ?>
                                        <option value="unspam">Not Spam</option>
                                        <?php } ?>
                                        <?php if($status == "trash") { ?>
                                        <option value="untrash">Restore</option>
                                        <?php } ?>
                                        <?php if(in_array($status, array("spam", "trash"))) { ?>
                                        <option value="delete">Delete Permanently</option>
                                        <?php } ?>
                                    </select>                            
                                    <input type="submit" id="doaction" class="btn btn-sm btn-default action" value="Apply">
                                </div>
                                <div class="pull-left actions" style="width: inherit;">
                                    <select name="comment_type" id="comment_type" style="max-width: 150px;float: left;padding: 0px 5px;max-height: 30px;margin-right: 2px;" class="form-control">
                                        <option value="">All Comment Types</option>
                                        <option value="comments" <?= $type == "comments" ? "selected" : "" ?>>Comments</option>
                                        <option value="pings" <?= $type == "pings" ? "selected" : "" ?>>Pings</option>
                                    </select>                            
                                    <input type="submit" id="filter" class="btn btn-sm btn-default action" name="filter" value="Filter">
                                </div>
                            </div>
                            <div class="col-md-6" style="margin: 3px 0px;">
                                <div class="" style="float:right">
                                    <span class="displaying-num"><?= $items_count ?> items</span>
                                    <a class="first-page btn btn-default btn-sm btn-flat" href="comments.php?<?= $querystring . '&paged=1' ?>"><i class="fa fa-angle-double-left"></i></a>
                                    <a class="previous-page btn btn-default btn-sm btn-flat" href="comments.php?<?= $querystring . '&paged=' . ($current_page > 1 ? $current_page - 1 : 1) ?>"><i class="fa fa-angle-left"></i></a>
                                    <span class="paging-input"><input class="btn btn-sm btn-flat" style="cursor:auto;max-width: 50px;padding: 4px 10px;" id="current-page-selector" type="text" name="paged" value="<?= $current_page ?>"> of <?= $max_pages ?></span>
                                    <a class="next-page btn btn-default btn-sm btn-flat" href="comments.php?<?= $querystring . '&paged=' . ($current_page < $max_pages ? $current_page + 1 : $max_pages) ?>"><i class="fa fa-angle-right"></i></a>
                                    <a class="last-page btn btn-default btn-sm btn-flat" href="comments.php?<?= $querystring . '&paged=' . $max_pages ?>"><i class="fa fa-angle-double-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="box">                        
                            <div class="table-responsive">  
                                <table id="comments" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="checkall"/></th>
                                            <th><a href="comments.php?order_by=author_name&order=<?= $order_by == "author_name" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Author <?= $order_by == "author_name" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Comment</th>
                                            <th><a href="comments.php?order_by=post_id&order=<?= $order_by == "post_id" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">In Response To <?= $order_by == "post_id" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th><a href="comments.php?order_by=comment_datetime&order=<?= $order_by == "comment_datetime" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Submitted On <?= $order_by == "comment_datetime" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php                       
                                        $i=1;
                                        foreach ($comments as $comment) {    
                                            $p = getPost($comment['post_id']);
                                            ?>                                    
                                            <tr class="<?= $comment['status'] == "pending" ? "pending" : "" ?>">                                       
                                                <th><input type="checkbox" name="comments[]" value="<?= $comment['id'] ?>"/></th>
                                                <td>
                                                    <b><?= $comment['author_name'] ?></b><br/>
                                                    <a href="<?= $comment['author_url'] ?>"><?= $comment['author_url'] ?></a><br/>
                                                    <a href="mailto:<?= $comment['author_email'] ?>"><?= $comment['author_email'] ?></a><br/>
                                                    <a href="comments.php?q=<?= $comment['author_ip'] ?>"><?= $comment['author_ip'] ?></a>
                                                </td>
                                                <td>
                                                    <p><?= $comment['content'] ?></p>
                                                    <div class="row-actions">
                                                        <?php if($comment['status'] == "pending") { ?>
                                                        <a href="<?= $sys['site_url'] . '/requests.php?action=comment-approve&cid=' . $comment['id']; ?>" class="approveit" data-id="<?= $comment['id'] ?>">Approve</a> |
                                                        <?php } ?>
                                                        <?php if($comment['status'] == "approved") { ?>
                                                        <a href="<?= $sys['site_url'] . '/requests.php?action=comment-unapprove&cid=' . $comment['id']; ?>" class="unapproveit" data-id="<?= $comment['id'] ?>">Unapprove</a> |
                                                        <?php } ?>
                                                        <?php if(in_array($comment['status'], array("pending", "approved"))) { ?>
                                                        <a href="<?= $sys['site_url'] . '/admin/comment-edit.php?id=' . $comment['id']; ?>" class="edit">Edit</a> |
                                                        <a href="<?= $sys['site_url'] . '/requests.php?action=comment-spam&cid=' . $comment['id']; ?>" class="spamit" data-id="<?= $comment['id'] ?>" style="color:red">Spam</a> |
                                                        <a href="<?= $sys['site_url'] . '/requests.php?action=comment-trash&cid=' . $comment['id']; ?>" class="trashit" data-id="<?= $comment['id'] ?>" style="color:red">Trash</a>
                                                        <?php } ?>
                                                        <?php if($comment['status'] == "spam") { ?>
                                                        <a href="<?= $sys['site_url'] . '/requests.php?action=comment-unspam&cid=' . $comment['id']; ?>" class="unspamit" data-id="<?= $comment['id'] ?>" style="color:orange;">Not Spam</a> |
                                                        <?php } ?>
                                                        <?php if($comment['status'] == "trash") { ?>
                                                        <a href="<?= $sys['site_url'] . '/requests.php?action=comment-spam&cid=' . $comment['id']; ?>" class="spamit" data-id="<?= $comment['id'] ?>" style="color:red">Spam</a> |
                                                        <a href="<?= $sys['site_url'] . '/requests.php?action=comment-untrash&cid=' . $comment['id']; ?>" class="untrashit" data-id="<?= $comment['id'] ?>" style="color:orange;">Restore</a> |
                                                        <?php } ?>
                                                        <?php if(in_array($comment['status'], array("spam", "trash"))) { ?>
                                                        <a href="<?= $sys['site_url'] . '/requests.php?action=comment-delete&cid=' . $comment['id']; ?>" class="deleteit" data-id="<?= $comment['id'] ?>" style="color:red;">Delete Permanently</a>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="post-edit.php?id=<?= $p['id'] ?>"><b><?= $p['post_title'] ?></b></a><br/>
                                                    <a href="<?= $sys['site_url'] . '/' . $p['post_name'] ?>">View <?= $sys['post_types'][$p['post_type']]['name'] ?></a><br/>
                                                    Comments: <a href="comments.php?post_id=<?= $p['id'] ?>"><?= $p['comment_count'] ?></a>
                                                </td>
                                                <td><?= $comment['comment_datetime'] ?></td>
                                            </tr>                      
                                        <?php } ?>
                                    </tbody> 
                                    <tfoot>
                                       <tr>
                                            <th><input type="checkbox" class="checkall"/></th>
                                            <th><a href="comments.php?order_by=author_name&order=<?= $order_by == "author_name" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Author <?= $order_by == "author_name" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Comment</th>
                                            <th><a href="comments.php?order_by=post_id&order=<?= $order_by == "post_id" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">In Response To <?= $order_by == "post_id" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th><a href="comments.php?order_by=comment_datetime&order=<?= $order_by == "comment_datetime" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Submitted On <?= $order_by == "comment_datetime" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </form>
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>
        <script src="<?= $sys['site_url']; ?>/admin/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript">    
            $('input[type="checkbox"]').iCheck({
              checkboxClass: 'icheckbox_flat-blue',
              radioClass: 'iradio_flat-blue'
            });
            $(".checkall").on("ifChanged", function(e){
                $("input[type='checkbox']").iCheck($(this).is(":checked") ? "check" : "uncheck");
            });
            $(".row-actions").on("click", ".approveit, .unapproveit, .spamit, .unspamit, .trashit, .untrashit, .deleteit", function(e){
                e.preventDefault();
                var url = $(this).attr("href");
                var clicked = $(this).attr("class");
                $.ajax({
                    type: "POST",
                    url: url,
                    data: null,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if(response.code == 0) {
                            switch(clicked) {
                                case "approveit" : 
                                    $(".approved-count").html(parseInt($(".approved-count").html()) + 1);
                                    $(".pending-count").html(parseInt($(".pending-count").html()) - 1);
                                    if("pending" === "<?= $status ?>") {
                                        $(e.target).closest("tr").hide("slow", function(){ $(this).remove(); });
                                    } else {
                                        $(e.target).closest("tr").removeClass("pending");
                                        $(e.target).attr("href", "<?= $sys['site_url'] ?>/requests.php?action=comment-unapprove&cid=" + $(e.target).attr("data-id"));
                                        $(e.target).html("Unapprove").removeClass("approveit").addClass("unapproveit");
                                    }
                                    break;
                                case "unapproveit" : 
                                    $(".approved-count").html(parseInt($(".approved-count").html()) - 1);
                                    $(".pending-count").html(parseInt($(".pending-count").html()) + 1);
                                    if("approved" === "<?= $status ?>") {
                                        $(e.target).closest("tr").hide("slow", function(){ $(this).remove(); });
                                    } else {
                                        $(e.target).closest("tr").addClass("pending");
                                        $(e.target).attr("href", "<?= $sys['site_url'] ?>/requests.php?action=comment-approve&cid=" + $(e.target).attr("data-id"));
                                        $(e.target).html("Approve").removeClass("unapproveit").addClass("approveit");
                                    }
                                    break;
                                case "spamit" : 
                                    $(".spam-count").html(parseInt($(".spam-count").html()) + 1);
                                    //if spamed from trash decrease trash count
                                    if("trash" === "<?= $status ?>") {
                                        $(".trash-count").html(parseInt($(".trash-count").html()) - 1);
                                    } else {
                                        $(".all-count").html(parseInt($(".all-count").html()) - 1);
                                        //if spamed from pending decrease pending count
                                        if($(e.target).closest("tr").hasClass("pending")) {
                                            $(".pending-count").html(parseInt($(".pending-count").html()) - 1);
                                        } else {
                                            $(".approved-count").html(parseInt($(".approved-count").html()) - 1);
                                        }
                                    }
                                    $(e.target).closest("tr").hide("slow", function(){ $(this).remove(); });
                                    break;
                                case "unspamit" : 
                                    $(".spam-count").html(parseInt($(".spam-count").html()) - 1);
                                    $(".all-count").html(parseInt($(".all-count").html()) + 1);
                                    if(response.status === "approved") {
                                        $(".approved-count").html(parseInt($(".approved-count").html()) + 1);
                                    } else {
                                        $(".pending-count").html(parseInt($(".pending-count").html()) + 1);
                                    }
                                    $(e.target).closest("tr").hide("slow", function(){ $(this).remove(); });
                                    break;
                                case "trashit" : 
                                    $(".trash-count").html(parseInt($(".trash-count").html()) + 1);
                                    $(".all-count").html(parseInt($(".all-count").html()) - 1);
                                    if($(e.target).closest("tr").hasClass("pending")) {
                                        $(".pending-count").html(parseInt($(".pending-count").html()) - 1);
                                    } else {
                                        $(".approved-count").html(parseInt($(".approved-count").html()) - 1);
                                    }
                                    $(e.target).closest("tr").hide("slow", function(){ $(this).remove(); });
                                    break;
                                case "untrashit" : 
                                    $(".trash-count").html(parseInt($(".trash-count").html()) - 1);
                                    $(".all-count").html(parseInt($(".all-count").html()) + 1);
                                    if(response.status === "approved") {
                                        $(".approved-count").html(parseInt($(".approved-count").html()) + 1);
                                    } else {
                                        $(".pending-count").html(parseInt($(".pending-count").html()) + 1);
                                    }
                                    $(e.target).closest("tr").hide("slow", function(){ $(this).remove(); });
                                    break;
                                case "deleteit" : 
                                    if("trash" === "<?= $status ?>") {
                                        $(".trash-count").html(parseInt($(".trash-count").html()) - 1);
                                    }
                                    if("spam" === "<?= $status ?>") {
                                        $(".spam-count").html(parseInt($(".spam-count").html()) - 1);
                                    }
                                    $(e.target).closest("tr").hide("slow", function(){ $(this).remove(); });
                                    break;
                            }
                        }
                    }
                });
            });
        </script>   
    </body>
</html>