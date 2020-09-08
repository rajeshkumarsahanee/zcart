<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(PRODUCT_BRANDS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$msg = "";
//Add Brand
if (isset($_POST['name']) && isUserHavePermission(PRODUCT_BRANDS_SECTION, getUserLoggedId())) {
    $brand['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $brand['slug'] = filter_var(trim($_POST['slug']), FILTER_SANITIZE_STRING);
    $brand['description'] = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    $brand['image'] = $_POST['brandimage'];//uploadBrandImage("brandimage");
    $brand['items_count'] = 0;
    $brand['meta_title'] = filter_var(trim($_POST['meta_title']), FILTER_SANITIZE_STRING);
    $brand['meta_keywords'] = filter_var(trim($_POST['meta_keywords']), FILTER_SANITIZE_STRING);
    $brand['meta_description'] = filter_var(trim($_POST['meta_description']), FILTER_SANITIZE_STRING);
    $brand['status'] = $_POST['active'];

    if ($brand['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter brand name</div>';
    } else if ($brand['slug'] == '') {
        $msg = '<div class="alert alert-danger">Please enter slug</div>';
    } else {
        $brand['image'] = $brand['image'] ? $brand['image'] : "";
        $msg = '<div class="alert alert-success">Brand added successfully!</div>';
        if (!addBrand($brand)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Add Product Brand - Admin</title>
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
                        Add Product Brand
                        <small>Add new brand</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active"><a href="<?= $sys['config']['site_url'] ?>/admin/product-brands.php">Product Brands</a></li>
                        <li class="active">Add Brand</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add Brand</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">
                                        <button type="button" id="activebid" class="btn btn-default btn-sm active">active</button>
                                        <button type="button" id="inactivebid" class="btn btn-default btn-sm">inactive</button>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <input type="hidden" class="form-control" id="activeid" name="active" value="1"/>
                                        <div class="form-group">
                                            <label for="name">Brand Name*</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" required>
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
                                            <label for="brandimage">Header Image</label>
                                            <img id="brandimageimg" src="http://placehold.it/1200x400" class="img-responsive"/>
                                            <span id="upload_status"></span>
                                            <input id="brandimage" type="hidden" name="brandimage"/>
                                            <input type="file" class="form-control" id="brandimageinput" placeholder="Brand Image">
                                        </div>

                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" class="form-control" name="meta_title" id="metatitle" placeholder="Meta Title">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Keywords</label>
                                            <input type="text" class="form-control" name="meta_keywords" id="metakeywords" placeholder="Meta Keywords">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea name="meta_description" class="form-control" placeholder="Meta Description"></textarea>
                                        </div>
                                        <hr>
                                        <?php echo $msg; ?>
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
        <script>
            $("#activebid").click(function () {
                $("#activeid").val("1");
            });

            $("#inactivebid").click(function () {
                $("#activeid").val("0");
            });
            
            $("#brandimageinput").change(function(e){                                      
                e.preventDefault();                                            
                var action = "<?= $sys['site_url']; ?>/requests.php?action=upload-brand-image";
                if($("#brandimageinput").val() === "") {
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
                        $("#brandimage").val(data.file_url);
                        $("#brandimageimg").prop("src", data.file_url);
                        $("#upload_status").html('');
                    }                        
                }).fail(function(data){
                    //any message
                });  

            });
        </script>
    </body>
</html>