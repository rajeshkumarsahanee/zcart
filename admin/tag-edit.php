<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
$taxonomy = isset($_REQUEST['taxonomy']) ? filter_var(trim($_REQUEST['taxonomy']), FILTER_SANITIZE_STRING) : "tag";
if (!isset($sys['taxonomies'][$taxonomy])) {
    die("Invalid Taxonomy");
}
//Not authorized to access
if (!isUserHavePermission(MANAGE_POSTS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$msg = "";
//Update Term
if (isset($_POST['name']) && isset($_POST['slug'])) {
    $term['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_STRING);
    $term['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $term['slug'] = filter_var(trim($_POST['slug']), FILTER_SANITIZE_STRING);
    $term['term_group'] = isset($_POST['term_group']) ? filter_var(trim($_POST['term_group']), FILTER_SANITIZE_STRING) : "0";
    $term['taxonomy'] = isset($_POST['taxonomy']) ? filter_var(trim($_POST['taxonomy']), FILTER_SANITIZE_STRING) : "tag";
    $term['description'] = trim($_POST['description']);
    $term['parent'] = isset($_POST['parent']) ? filter_var(trim($_POST['parent']), FILTER_SANITIZE_STRING) : "0";
    $term['count'] = isset($_POST['count']) ? filter_var(trim($_POST['count']), FILTER_SANITIZE_STRING) : "0";
    $term['metas'] = array();

    if ($term['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter name</div>';
    } else if ($term['slug'] == '') {
        $msg = '<div class="alert alert-danger">Please enter slug</div>';
    } else {
        $msg = '<div class="alert alert-success">Updated successfully!</div>';
        if (!updateTerm($term)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

$term = getTerm(trim($_REQUEST['id']));
if (!isset($term['id'])) {
    header("location: terms.php?taxonomy=" . $taxonomy);
}
$terms = getTerms(array(), array("taxonomy" => $taxonomy), 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit <?= $sys['taxonomies'][$taxonomy]['name'] ?> - Admin</title>
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
                        Edit <?= $sys['taxonomies'][$taxonomy]['name'] ?>
                        <small>information</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?= $sys['config']['site_url'] ?>/admin/posts.php">Posts</a></li>                        
                        <li class=""><a href="<?= $sys['config']['site_url'] ?>/admin/tags.php?taxonomy=<?= $taxonomy ?>"><?= $sys['taxonomies'][$taxonomy]['plural'] ?></a></li>
                        <li class="active">Edit <?= $sys['taxonomies'][$taxonomy]['name'] ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">                        
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Edit Category</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">

                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">                                            
                                        <input id="id" type="hidden" name="id" value="<?= $term['id']; ?>"/>
                                        <input type="hidden" name="taxonomy" value="<?= $taxonomy ?>"/>
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="name" value="<?= $term['name'] ?>" class="form-control"/>
                                            The name is how it appears on your site.
                                        </div>
                                        <div class="form-group">
                                            <label>Slug</label>
                                            <input type="text" name="slug" value="<?= $term['slug'] ?>" class="form-control"/>
                                            The "slug" is the URL-friendly version of the name. It is usually all lowercase 
                                            and contains only letters, numbers, and hyphens.
                                        </div>
                                        <?php if ($taxonomy == "category") { ?>
                                            <div class="form-group">
                                                <label>Parent Category</label>
                                                <select name="parent" class="form-control">
                                                    <option value="0" <?= $term['parent'] == 0 ? "selected" : "" ?>>None</option>
                                                    <?php foreach($terms as $t) {
                                                        if($t['id'] == $term['id']) {                                                            
                                                            continue;                                                             
                                                        }
                                                        ?>
                                                    <option value="<?= $t['id'] ?>" <?= $term['parent'] == $t['id'] ? "selected" : "" ?>><?= $t['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                                Categories, unlike tags, can have a hierarchy. You might have a Jazz category, 
                                                and under that have children categories for Bebop and Big Band. Totally optional.
                                            </div>
                                        <?php } ?>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control"><?= $term['description'] ?></textarea>
                                            The description is not prominent by default; however, some themes may show it.
                                        </div>
                                        <?= $msg; ?>
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
        <script>
            
        </script>
    </body>
</html>