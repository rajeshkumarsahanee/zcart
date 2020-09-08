<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(PRODUCT_CATEGORIES_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$msg = "";
//Add Category
if (isset($_POST['categoryname']) && isset($_POST['maincategory']) && isUserHavePermission(PRODUCT_CATEGORIES_SECTION, getUserLoggedId())) {
    $category['name'] = filter_var(trim($_POST['categoryname']), FILTER_SANITIZE_STRING);
    $category['slug'] = filter_var(trim($_POST['slug']), FILTER_SANITIZE_STRING);
    $category['description'] = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    $category['main_category'] = filter_var(trim($_POST['maincategory']), FILTER_SANITIZE_NUMBER_INT);
    $category['image'] = $_POST['categoryimage'];//!empty($_FILES['categoryimage']['name']) ? uploadCategoryImage("categoryimage") : "";
    $category['display_order'] = filter_var(trim($_POST['display_order']), FILTER_SANITIZE_NUMBER_INT);
    $category['meta_title'] = filter_var(trim($_POST['metatitle']), FILTER_SANITIZE_STRING);
    $category['meta_keywords'] = filter_var(trim($_POST['metakeywords']), FILTER_SANITIZE_STRING);
    $category['meta_description'] = filter_var(trim($_POST['metadescription']), FILTER_SANITIZE_STRING);
    $category['status'] = $_POST['active'];
    $category['filters'] = $_POST['filters'];

    if ($category['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter category name</div>';
    } else if ($category['slug'] == '') {
        $msg = '<div class="alert alert-danger">Please enter slug</div>';
    } else if ($category['main_category'] == '') {
        $msg = '<div class="alert alert-danger">Please select main category</div>';
    } else {
        $category['image'] = $category['image'] ? $category['image'] : "";
        $msg = '<div class="alert alert-success">Category added successfully!</div>';
        if (!addCategory($category)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

$categories = getCategories(array(), array(), 0, -1);
$filters = getFilters(false, array(), 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Add Product Category - Admin</title>
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
                        Add Product Category
                        <small>Add new category</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li>Catalog</li>
                        <li><a href="<?= $sys['site_url'] ?>/admin/product-categories.php">Product Categories</a></li>
                        <li class="active">Add Category</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add Category</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">
                                        <button type="button" id="activebid" class="btn btn-default btn-sm active">active</button>
                                        <button type="button" id="inactivebid" class="btn btn-default btn-sm">inactive</button>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <input type="hidden" class="form-control" id="activeid" name="active" value="1"/>
                                        <div class="form-group">
                                            <label for="categoryname">Category Name*</label>
                                            <input type="text" class="form-control" name="categoryname" id="categoryname" placeholder="Enter Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="slug">Slug*</label>
                                            <input type="text" class="form-control" name="slug" id="slug" placeholder="Slug" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea name="description" class="form-control" placeholder="Description"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="maincategory">Main Category</label>
                                            <select name="maincategory" class="form-control select2">
                                                <option value="0">Root</option>
                                                <?php
                                                foreach ($categories as $category) {
                                                    ?>     
                                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                                <?php } ?>                    
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="filters">Filters</label>
                                            <select name="filters[]" class="form-control select2" multiple="multiple">
                                                <?php
                                                foreach ($filters as $f) {
                                                    ?>     
                                                    <option value="<?= $f['id'] ?>"><?= $f['group_name'] . " > " . $f['name'] ?></option>
                                                    <?php
                                                }
                                                ?>                    
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Display Order</label>
                                            <input type="number" class="form-control" name="display_order" id="display_order" placeholder="Display Order">
                                        </div>
                                        <div class="form-group">
                                            <label for="categoryimage">Header Image</label>
                                            <img id="categoryimageimg" src="http://placehold.it/1200x400" class="img-responsive"/>
                                            <span id="upload_status"></span>
                                            <input id="categoryimage" type="hidden" name="categoryimage"/>
                                            <input type="file" class="form-control" id="categoryimageinput" placeholder="Category Image">
                                        </div>

                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" class="form-control" name="metatitle" id="metatitle" placeholder="Meta Title">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Keywords</label>
                                            <input type="text" class="form-control" name="metakeywords" id="metakeywords" placeholder="Meta Keywords">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea name="metadescription" class="form-control" placeholder="Meta Description"></textarea>
                                        </div>
                                        <hr>
                                        <?= $msg ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
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
            
            $("#categoryimageinput").change(function(e){                                      
                e.preventDefault();                                            
                var action = "<?= $sys['site_url']; ?>/requests.php?action=upload-category-image";
                if($("#categoryimageinput").val() === "") {
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
                }).done(function(data){                            
                    $("#upload_status").html(data.msg);
                    if(data.code === '0') {
                        $("#categoryimage").val(data.file_url);
                        $("#categoryimageimg").prop("src", data.file_url);
                        $("#upload_status").html('');
                    }                        
                }).fail(function(data){
                    //any message
                });  

            });
        </script>
    </body>
</html>