<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
if (!isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
    header("location: users.php");
}

$msg = "";
//Delete Comment
if(isset($_REQUEST['trash']) && isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
    $cid = filter_var(trim($_REQUEST['trash']), FILTER_SANITIZE_NUMBER_INT);
    $c = getComment($cid, array(), true);
    $c['metas']['last_status'] = $c['status'];
    $c['status'] = "trash";
    if(updateComment($c)) {
        $p = getPost($c['post_id'], array('id', 'comment_count'));
        update(T_POSTS, array("comment_count" => $p['comment_count'] - 1), array("id" => $p['id']));
        header("location: comments.php?cid=" . $cid);
        exit();
    }
}
//Update Comment
if (isset($_POST['id']) && isset($_POST['update']) && isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) {
    $id = filter_var(trim($_POST['id']), FILTER_SANITIZE_STRING);
    $comment = getComment($id, array(), true);
    $comment['author_name'] = filter_var(trim($_POST['author_name']), FILTER_SANITIZE_STRING);
    $comment['author_email'] = filter_var(trim($_POST['author_email']), FILTER_SANITIZE_STRING);
    $comment['author_url'] = filter_var(trim($_POST['author_url']), FILTER_SANITIZE_STRING);
    $comment['content'] = filter_var(trim($_POST['content']), FILTER_SANITIZE_STRING);
    $new_status = isset($_POST['status']) ? filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING) : "pending";
    $comment['metas']['last_status'] = $new_status == "spam" ? $comment['status'] : $new_status;
    $comment['status'] = $new_status;
    
    if (updateComment($comment)) {
        if($new_status == "spam") {
            $post = getPost($comment['post_id'], array('id', 'comment_count'));
            update(T_POSTS, array("comment_count" => $post['comment_count'] - 1), array("id" => $post['id']));
        }
        header("location: comments.php");
        exit();
    } else {
        $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

if (!isset($_REQUEST['id']) || trim($_REQUEST['id']) == '') {
    header("location: comments.php");
}
$comment = getComment(trim($_REQUEST['id']));
if (empty($comment)) {
    header("location: comments.php");
}
$post = getPost($comment['post_id']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Comment - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
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
                        Edit Comment
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?= $sys['site_url'] ?>/admin/comments.php">Comments</a></li>
                        <li class="active">Edit Comment</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form method="post" enctype="multipart/form-data">
                        <?php if($comment['status'] == "approved") { ?>
                        <p><strong>Permalink:</strong> <a href="<?= $sys['site_url'] . "/" . $post['post_name'] . "#comment-" . $comment['id'] ?>"><?= $sys['site_url'] . "/" . $post['post_name'] . "#comment-" . $comment['id'] ?></a></p>
                        <?php } ?>
                        <?= $msg ?>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="box box-primary">
                                    <div class="box-body">                                             
                                        <input id="id" type="hidden" name="id" value="<?= $comment['id'] ?>"/>
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>Author</label>
                                            <table class="table no-border">
                                                <tr>
                                                    <td>Name:</td>
                                                    <td><input type="text" name="author_name" value="<?= $comment['author_name'] ?>" class="form-control"/></td>
                                                </tr>
                                                <tr>
                                                    <td>Email:</td>
                                                    <td><input type="email" name="author_email" value="<?= $comment['author_email'] ?>" class="form-control"/></td>
                                                </tr>
                                                <tr>
                                                    <td>Url:</td>
                                                    <td><input type="text" name="author_url" value="<?= $comment['author_url'] ?>" class="form-control"/></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <label>Comment</label>
                                            <textarea id="content" name="content" class="form-control" rows="4" placeholder="Comment"><?= $comment['content'] ?></textarea>
                                        </div>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                            </div>
                            <div class="col-md-3">
                                <div class="box">
                                    <div class="box-body">
                                        <label><strong>Status</strong></label><br/>
                                        <label style="font-weight: normal;"><input type="radio" name="status" value="approved" <?= $comment['status'] == "approved" ? "checked" : "" ?>/> Approved</label><br/>
                                        <label style="font-weight: normal;"><input type="radio" name="status" value="pending" <?= $comment['status'] == "pending" ? "checked" : "" ?>/> Pending</label><br/>
                                        <label style="font-weight: normal;"><input type="radio" name="status" value="spam" <?= $comment['status'] == "spam" ? "checked" : "" ?>/> Spam</label><br/><br/>
                                        <i class="fa fa-calendar"></i> Submitted on: <?= $comment['comment_datetime'] ?><br/><br/>
                                        In response to: <a href="post-edit.php?id=<?= $post['id'] ?>"><b><?= $post['post_title'] ?></b></a>
                                    </div>
                                    <div class="box-footer">
                                        <a href="comment-edit.php?trash=<?= $comment['id'] ?>" class="pull-left" style="color: red">Move to Trash</a>
                                        <input type="submit" class="btn btn-success pull-right" name="update" value="Update"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>   

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>
        <script>

        </script>
    </body>
</html>