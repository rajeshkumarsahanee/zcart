<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(PRODUCT_TAGS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$updatemsg = "";
//Update Filter
if (isset($_POST['name']) && isset($_POST['slug']) && isUserHavePermission(PRODUCT_TAGS_SECTION, getUserLoggedId())) {
    $tag['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $tag['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $tag['slug'] = filter_var(trim($_POST['slug']), FILTER_SANITIZE_STRING);
    $tag['meta_title'] = filter_var(trim($_POST['meta_title']), FILTER_SANITIZE_STRING);
    $tag['meta_keywords'] = filter_var(trim($_POST['meta_keywords']), FILTER_SANITIZE_STRING);
    $tag['meta_description'] = filter_var(trim($_POST['meta_description']), FILTER_SANITIZE_STRING);
    $tag['status'] = "A";

    if ($tag['name'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter name</div>';
    } else {
        $updatemsg = '<div class="alert alert-success">Tag updated successfully!</div>';
        if (!updateTag($tag)) {
            $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

$tag = getTag($_REQUEST['id']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Tag - Admin</title>
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
                        Edit Tag
                        <small>Edit tag</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active"><a href="<?= $sys['config']['site_url'] ?>/admin/product-tags.php">Product Tags</a></li>
                        <li class="active">Edit Tag</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Edit Tag</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">

                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">  
                                        <input type="hidden" name="id" value="<?= $tag['id']; ?>"/>
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" value="<?php echo $tag['name'] ?>" placeholder="Enter Name" required>
                                        </div>                                            
                                        <div class="form-group">
                                            <label>Slug</label>
                                            <input type="text" class="form-control" name="slug" id="display_order" value="<?php echo $tag['slug'] ?>" placeholder="Slug">
                                            Do not use spaces, instead replace spaces with - and make sure the keyword is globally unique.
                                        </div>  
                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" class="form-control" name="meta_title" id="meta_title" placeholder="Meta Title" value="<?= $tag['meta_title']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Keywords</label>
                                            <input type="text" class="form-control" name="meta_keywords" id="meta_keywords" placeholder="Meta Keywords" value="<?= $tag['meta_keywords']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea name="meta_description" id="meta_description" class="form-control" placeholder="Meta Description"><?= $tag['meta_description']; ?></textarea>
                                        </div>
                                        <hr>
                                        <?php echo $updatemsg; ?>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>                 
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>   

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>

    </body>
</html>