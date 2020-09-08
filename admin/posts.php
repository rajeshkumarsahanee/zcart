<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
$post_type = isset($_REQUEST['type']) ? filter_var(trim($_REQUEST['type']), FILTER_SANITIZE_STRING) : "post";
if (!isset($sys['post_types'][$post_type])) {
    die("Invalid Post Type");
}
//Not authorized to access
if (!isUserHavePermission(MANAGE_POSTS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}
//redirects for other post types
if (!in_array($post_type, array("post", "page"))) {
    if ($post_type == "attachment") {
        header("location: media.php");
    }
}
$msg = $deletemsg = $post_author = $post_status = $post_date = $category = "";
//Delete Post
if (isset($_GET['del']) && isUserHavePermission(MANAGE_POSTS_SECTION, getUserLoggedId())) {
    if (deletePost(trim($_REQUEST['del']))) {
        $deletemsg = "<script>alert('Deleted successfully');</script>";
    } else {
        $deletemsg = "<script>alert('Cannot delete');</script>";
    }
}
$filters = array("post_type" => $post_type);
$querystring = "type=" . $post_type;

if(isset($_REQUEST['post_author']) && trim($_REQUEST['post_author']) <> "") {
    $filters['post_author'] = $post_author = filter_var(trim($_REQUEST['post_author']), FILTER_SANITIZE_NUMBER_INT);
    $querystring = "post_author=" . $_REQUEST['post_author'];
}
if(isset($_REQUEST['post_status']) && trim($_REQUEST['post_status']) <> "") {
    $filters['post_status'] = $post_status = filter_var(trim($_REQUEST['post_status']), FILTER_SANITIZE_STRING);
    $querystring = "post_status=" . $_REQUEST['post_status'];
}
if(isset($_REQUEST['post_date']) && trim($_REQUEST['post_date']) <> "") {
    $filters['post_date'] = $post_date = filter_var(trim($_REQUEST['post_date']), FILTER_SANITIZE_STRING);
    $querystring = "post_date=" . $_REQUEST['post_date'];
}
if(isset($_REQUEST['category']) && trim($_REQUEST['category']) <> "") {
    $filters['term_taxonomy_id'] = $category = $post_status = filter_var(trim($_REQUEST['category']), FILTER_SANITIZE_STRING);
    $querystring = "category=" . $_REQUEST['category'];
}
if (isset($_REQUEST['q'])) {
    $filters['q'] = filter_var(trim($_REQUEST['q']), FILTER_SANITIZE_STRING);
    $querystring .= "&q=" . $_REQUEST['q'];
}

/* pagination logic start */
$items_count = count(getPosts(array('id'), $filters, 0, -1));
$items_per_page = isset($config['items_per_page_post_admin']) ? $config['items_per_page_post_admin'] : 20;
$max_pages = intval($items_count / $items_per_page + 1);
$current_page = !isset($_REQUEST['paged']) || intval($_REQUEST['paged']) < 1 ? 1 : filter_var(trim($_REQUEST['paged']), FILTER_SANITIZE_NUMBER_INT);
if ($current_page > $max_pages) {
    header("location: posts.php?" . $querystring . "&paged=" . $max_pages);
    exit();
}
$offset = $items_per_page * $current_page - $items_per_page;
/* pagination logic end */
$order_by = isset($_REQUEST['order_by']) && in_array($_REQUEST['order_by'], array("post_title", "post_date")) ? filter_var(trim($_REQUEST['order_by']), FILTER_SANITIZE_STRING) : "id";
$order = isset($_REQUEST['order']) && in_array($_REQUEST['order'], array("asc", "desc")) ? filter_var(trim($_REQUEST['order']), FILTER_SANITIZE_STRING) : "DESC";
$posts = getPosts(array(), $filters, $offset, $items_per_page, $order_by, $order);
$authors = getUsers(array('id', 'display_name'), array(), 0, -1, "id", "DESC", "id");
$dates = getPostsDates(YEAR_MONTH, $post_type);
$terms = getTerms(array(), array("taxonomy" => "category"), 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $sys['post_types'][$post_type]['plural'] ?> - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link rel="stylesheet" href="<?= $sys['site_url']; ?>/admin/plugins/iCheck/flat/blue.css">
        <style>  
            #posts {
                margin-bottom: 0;
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
                        <?= $sys['post_types'][$post_type]['plural'] ?>
                        <small><a href="post-add.php?type=<?= $post_type ?>" class="btn btn-default btn-sm">Add New</a></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>                        
                        <li class="active"><?= $sys['post_types'][$post_type]['plural'] ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form id="posts-form" method="get">
                        <?= $msg ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="" style="float:left">
                                    <a href="posts.php?type=<?= $post_type ?>" class="<?= $post_author == '' && $post_status == '' ? "current" : "" ?>">All<span class="count">(<span class="all-count"><?= count(getPosts(array('id'), array('post_type' => $post_type, 'statuses' => array("published", "draft", "pending")), 0, -1)) ?></span>)</span></a> |
                                    <a href="posts.php?type=<?= $post_type ?>&post_author=<?= getUserLoggedId() ?>" class="<?= $post_author == getUserLoggedId() ? "current" : "" ?>">Mine<span class="count">(<span class="all-count"><?= count(getPosts(array('id'), array('post_type' => $post_type, 'statuses' => array("published", "draft", "pending"), "post_author" => getUserLoggedId()), 0, -1)) ?></span>)</span></a> |
                                    <a href="posts.php?type=<?= $post_type ?>&post_status=published" class="<?= $post_status == 'published' ? "current" : "" ?>">Published<span class="count">(<span class="all-count"><?= count(getPosts(array('id'), array('post_type' => $post_type, 'post_status' => "published"), 0, -1)) ?></span>)</span></a> |
                                    <a href="posts.php?type=<?= $post_type ?>&post_status=draft" class="<?= $post_status == 'draft' ? "current" : "" ?>">Drafts<span class="count">(<span class="all-count"><?= count(getPosts(array('id'), array('post_type' => $post_type, 'post_status' => "draft"), 0, -1)) ?></span>)</span></a> |
                                    <a href="posts.php?type=<?= $post_type ?>&post_status=pending" class="<?= $post_status == 'pending' ? "current" : "" ?>">Pending<span class="count">(<span class="all-count"><?= count(getPosts(array('id'), array('post_type' => $post_type, 'post_status' => "pending"), 0, -1)) ?></span>)</span></a>                            
                                </div>
                                <div class="" style="float:right">
                                    <input type="hidden" name="type" value="<?= $post_type ?>"/>
                                    <input type="text" class="form-control" name="q" value="<?= isset($_REQUEST['q']) ? $_REQUEST['q'] : "" ?>" style="width:auto; float:left;padding: 0px 2px;max-height: 30px;">
                                    <input type="submit" style="float:left;" name="search" value="Search <?= $sys['post_types'][$post_type]['plural'] ?>" class="btn btn-default btn-sm">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8" style="margin: 3px 0px;">
                                <div class="actions">
                                    <select name="action" id="bulk-action-selector-top" style="max-width: 150px;float: left;padding: 0px 5px;max-height: 30px;margin-right: 2px;" class="form-control">
                                        <option value="-1">Bulk Actions</option>
                                        <option value="edit">Edit</option>
                                        <option value="trash">Move to Trash</option>
                                    </select>                            
                                    <input type="submit" id="doaction" name="dobulkaction" class="btn btn-sm btn-default action" style="float: left; margin-right: 5px;" value="Apply">
                                
                                    <select name="post_date" id="year_month" style="max-width: 150px;float: left;padding: 0px 5px;max-height: 30px;margin-right: 2px;" class="form-control">
                                        <option value="">All Dates</option>
                                        <?php
                                        foreach ($dates as $d) {
                                            $timestamp = strtotime($d . "01");
                                            ?>
                                            <option value="<?= date("Y-m", $timestamp) ?>" <?= $post_date == date("Y-m", $timestamp) ? "selected" : "" ?>><?= date("F Y", $timestamp) ?></option>
                                        <?php } ?>
                                    </select>
                                    <select name="category" id="category" style="max-width: 150px;float: left;padding: 0px 5px;max-height: 30px;margin-right: 2px;" class="form-control">
                                        <option value="">All Categories</option>
                                        <?php foreach($terms as $t) { ?>
                                        <option value="<?= $t['id'] ?>" <?= $category == $t['id'] ? "selected" : "" ?>><?= $t['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="submit" id="filter" name="filter" class="btn btn-sm btn-default action" name="filter" value="Filter">
                                </div>
                            </div>
                            <div class="col-md-4" style="margin: 3px 0px;">
                                <div class="" style="float:right">
                                    <span class="displaying-num"><?= $items_count ?> items</span>
                                    <a class="first-page btn btn-default btn-sm btn-flat" href="posts.php?<?= $querystring . '&paged=1' ?>"><i class="fa fa-angle-double-left"></i></a>
                                    <a class="previous-page btn btn-default btn-sm btn-flat" href="posts.php?<?= $querystring . '&paged=' . ($current_page > 1 ? $current_page - 1 : 1) ?>"><i class="fa fa-angle-left"></i></a>
                                    <span class="paging-input"><input class="btn btn-sm btn-flat" style="cursor:auto;max-width: 50px;padding: 4px 10px;" id="current-page-selector" type="text" name="paged" value="<?= $current_page ?>"> of <?= $max_pages ?></span>
                                    <a class="next-page btn btn-default btn-sm btn-flat" href="posts.php?<?= $querystring . '&paged=' . ($current_page < $max_pages ? $current_page + 1 : $max_pages) ?>"><i class="fa fa-angle-right"></i></a>
                                    <a class="last-page btn btn-default btn-sm btn-flat" href="posts.php?<?= $querystring . '&paged=' . $max_pages ?>"><i class="fa fa-angle-double-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="box">                        
                            <div class="table-responsive">  
                                <table id="posts" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="checkall"/></th>
                                            <th><a href="posts.php?order_by=post_title&order=<?= $order_by == "post_title" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Title <?= $order_by == "post_title" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Author</th>
                                            <th>Categories</th>
                                            <th>Tags</th>
                                            <th><i class="fa fa-bar-chart"></i></th>
                                            <th><i class="fa fa-comment"></i></th>
                                            <th><a href="posts.php?order_by=post_date&order=<?= $order_by == "post_date" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Date <?= $order_by == "post_date" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($posts as $post) {
                                            ?>                                    
                                            <tr>                                       
                                                <th><input type="checkbox" name="post[]" value="<?= $post['ID'] ?>"/></th>
                                                <td>
                                                    <?= $post['post_title'] ?>
                                                    <div class="row-actions">
                                                        <a href="post-edit.php?id=<?= $post['id'] ?>&type=<?= $post_type ?>">Edit</a> |
                                                        <a href="post-edit.php?del=<?= $post['id'] ?>" style="color:red">Trash</a> |
                                                        <a href="<?= $sys['site_url'] . '/' . $post['post_name'] ?>">View</a>
                                                    </div>
                                                </td>
                                                <td><a href="posts.php?post_author=<?= $post['post_author'] ?>"><?= isset($authors[$post['post_author']]) ? $authors[$post['post_author']]['display_name'] : 'Unknown' ?></a></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><?= $post['post_date'] ?></td>
                                            </tr>                      
                                        <?php } ?>
                                    </tbody> 
                                    <tfoot>
                                       <tr>
                                            <th><input type="checkbox" id="checkall"/></th>
                                            <th><a href="posts.php?order_by=post_title&order=<?= $order_by == "post_title" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Title <?= $order_by == "post_title" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Author</th>
                                            <th>Categories</th>
                                            <th>Tags</th>
                                            <th><i class="fa fa-bar-chart"></i></th>
                                            <th><i class="fa fa-comment"></i></th>
                                            <th><a href="posts.php?order_by=post_date&order=<?= $order_by == "post_date" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Date <?= $order_by == "post_date" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
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
        </script>     
        <?= $deletemsg ?>
    </body>
</html>