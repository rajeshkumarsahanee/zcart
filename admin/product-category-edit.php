<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(PRODUCT_CATEGORIES_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

//Edit Category
$updatemsg = "";
if (isset($_POST['category_name']) && isset($_POST['main_category']) && isUserHavePermission(PRODUCT_CATEGORIES_SECTION, getUserLoggedId())) {
    $category['id'] = filter_var(trim($_POST['category_id']), FILTER_SANITIZE_NUMBER_INT);
    $c = getCategory(trim($_REQUEST['id']));
    $category['name'] = filter_var(trim($_POST['category_name']), FILTER_SANITIZE_STRING);
    $category['slug'] = filter_var(trim($_POST['slug']), FILTER_SANITIZE_STRING);
    $category['description'] = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    $category['main_category'] = filter_var(trim($_POST['main_category']), FILTER_SANITIZE_NUMBER_INT);
    $category['display_order'] = filter_var(trim($_POST['display_order']), FILTER_SANITIZE_NUMBER_INT);
    $category['image'] = $_POST['categoryimage'];//!empty($_FILES['categoryimage']['name']) ? uploadCategoryImage("categoryimage") : $c['image'];
    $category['meta_title'] = filter_var(trim($_POST['metatitle']), FILTER_SANITIZE_STRING);
    $category['meta_keywords'] = filter_var(trim($_POST['metakeywords']), FILTER_SANITIZE_STRING);
    $category['meta_description'] = filter_var(trim($_POST['metadescription']), FILTER_SANITIZE_STRING);
    $category['status'] = $_POST['active'];
    $category['filters'] = $_POST['filters'];

    if ($category['name'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter category name</div>';
    } else if ($category['slug'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter slug</div>';
    } else if ($category['main_category'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please select main category</div>';
    } else {
        $updatemsg = '<div class="alert alert-success">Category updated successfully!</div>';
        if (!updateCategory($category)) {
            $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}


$category = getCategory(trim($_REQUEST['id']), array(), true);
$categories = getCategories(array(), array(), 0, -1);
$tmpfilters = array();
foreach ($category['filters'] as $f) {
    $tmpfilters[] = $f['filter_id'];
}
$filters = getFilters(false, array(), 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Product Category - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link rel="stylesheet" href="<?= $sys['site_url'] ?>/admin/plugins/select2/select2.min.css">
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
                        Edit Product Category
                        <small>edit category information</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li>Catalog</li>
                        <li><a href="<?= $sys['site_url'] ?>/admin/product-categories.php">Product Categories</a></li>
                        <li class="active">Edit Category</li>
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
                                        <button type="button" id="activebid" class="btn btn-default btn-sm <?= trim($category['status']) == 'A' ? 'active' : '' ?>">active</button>
                                        <button type="button" id="inactivebid" class="btn btn-default btn-sm <?= trim($category['status']) == 'I' ? 'active' : '' ?>">inactive</button>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form id="cuform" role="form" method="post" action="" enctype="multipart/form-data">
                                        <input id="category_id" type="hidden" name="category_id" value="<?= $category['id'] ?>"/>
                                        <input type="hidden" class="form-control" id="activeid" name="active" value="<?= $category['status'] ?>"/>
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>Category Name</label>
                                            <input id="category_name" type="text" name="category_name" class="form-control" placeholder="Enter Category Name..." value="<?= $category['name'] ?>" required/>
                                        </div>
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>Slug</label>
                                            <input type="text" class="form-control" name="slug" id="slug" placeholder="Slug" value="<?= $category['slug'] ?>">
                                        </div>
                                        <!-- textarea -->
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea id="description" name="description" class="form-control" placeholder="Description"><?= $category['description'] ?></textarea>
                                        </div>
                                        <!-- select -->
                                        <div class="form-group">
                                            <label for="main_category">Main Category</label>
                                            <select id="main_category" name="main_category" class="form-control select2">
                                                <option value="0">Root</option>
                                                <?php foreach ($categories as $cat) { ?>     
                                                    <option value="<?php echo $cat['id'] ?>" <?= $category['main_category'] == $cat['id'] ? 'selected' : '' ?> ><?= $cat['name'] ?></option>
                                                <?php } ?>                    
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="filters">Filters</label>
                                            <select name="filters[]" class="form-control select2" multiple="multiple">
                                                <?php
                                                foreach ($filters as $f) {
                                                    ?>     
                                                    <option value="<?= $f['id'] ?>" <?= in_array($f['id'], $tmpfilters) ? "selected" : '' ?>><?= $f['group_name'] . " > " . $f['name']; ?></option>
                                                    <?php
                                                }
                                                ?>                       
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Display Order</label>
                                            <input type="number" class="form-control" name="display_order" id="display_order" value="<?= $category['display_order'] ?>" placeholder="Display Order">
                                        </div>
                                        <div class="form-group">
                                            <label >Header Image</label>
                                            <img id="categoryimageimg" src="<?= !empty($category['image']) ? $category['image'] : 'http://placehold.it/1200x400' ?>" class="img-responsive"/>
                                            <span id="upload_status"></span>
                                            <input id="categoryimage" type="hidden" name="categoryimage"/>
                                            <input type="file" class="form-control" id="categoryimageinput" placeholder="Category Image">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" class="form-control" name="metatitle" id="metatitle" placeholder="Meta Title" value="<?php echo $category['meta_title']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Keywords</label>
                                            <input type="text" class="form-control" name="metakeywords" id="metakeywords" placeholder="Meta Keywords" value="<?php echo $category['meta_keywords']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea name="metadescription" id="metadescription" class="form-control" placeholder="Meta Description"><?php echo $category['meta_description']; ?></textarea>
                                        </div>
                                        <?php echo $updatemsg; ?>
                                        <div class="box-footer">
                                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                                        </div>
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
        <script src="<?= $sys['site_url'] ?>/admin/plugins/select2/select2.full.min.js"></script> 
        <script>
            $("#activebid").click(function () {
                $("#activeid").val("A");
            });

            $("#inactivebid").click(function () {
                $("#activeid").val("I");
            });

            $(".select2").select2();

            $("#categoryimageinput").change(function (e) {
                e.preventDefault();
                var action = "<?= $sys['site_url']; ?>/requests.php?action=upload-category-image";
                if ($("#categoryimageinput").val() === "") {
                    return;
                }
                $("#upload_status").html("Uploading...");
                var data = new FormData();
                data.append("image", $('input[type=file]')[0].files[0]);
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: data,
                    /*THIS MUST BE DONE FOR FILE UPLOADING*/
                    contentType: false,
                    processData: false,
                }).done(function (data) {
                    $("#upload_status").html(data.msg);
                    if (data.code === '0') {
                        $("#categoryimage").val(data.file_url);
                        $("#categoryimageimg").prop("src", data.file_url);
                        $("#upload_status").html('');
                    }
                }).fail(function (data) {
                    //any message
                });

            });
        </script>
    </body>
</html>