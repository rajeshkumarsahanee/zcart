<?php require_once 'check_login_status.php'; ?>

<?php
//Not authorized to access
if (!isUserHavePermission(PRODUCT_BRANDS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

//Edit Brand
$updatemsg = "";
if (isset($_POST['name']) && isUserHavePermission(PRODUCT_BRANDS_SECTION, getUserLoggedId())) {
    $brand['id'] = filter_var(trim($_POST['brand_id']), FILTER_SANITIZE_NUMBER_INT);
    $b = getBrand($brand['id']);
    $brand['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $brand['slug'] = filter_var(trim($_POST['slug']), FILTER_SANITIZE_STRING);
    $brand['description'] = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    $brand['image'] = $_POST['brandimage'];//!empty($_FILES['brandimage']['name']) ? uploadBrandImage("brandimage") : $b['image'];
    $brand['items_count'] = $b['items_count'];
    $brand['meta_title'] = filter_var(trim($_POST['meta_title']), FILTER_SANITIZE_STRING);
    $brand['meta_keywords'] = filter_var(trim($_POST['meta_keywords']), FILTER_SANITIZE_STRING);
    $brand['meta_description'] = filter_var(trim($_POST['meta_description']), FILTER_SANITIZE_STRING);
    $brand['status'] = $_POST['active'];

    if ($brand['name'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter brand name</div>';
    } else if ($brand['slug'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter slug</div>';
    } else {
        $updatemsg = '<div class="alert alert-success">Brand updated successfully!</div>';
        if (!updateBrand($brand)) {
            $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}


$brand = getBrand(trim($_REQUEST['id']));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Product Brand - Admin</title>
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
                        Edit Product Brand
                        <small>edit brand information</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active"><a href="<?php echo $sys['config']['site_url'] ?>/admin/product-brands">Product Brands</a></li>
                        <li class="active">Edit Brand</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Edit Brand</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">
                                        <button type="button" id="activebid" class="btn btn-default btn-sm <?php
                                        if (trim($brand['status']) == 'A') {
                                            echo 'active';
                                        }
                                        ?>">active</button>
                                        <button type="button" id="inactivebid" class="btn btn-default btn-sm <?php
                                        if (trim($brand['status']) == 'I') {
                                            echo 'active';
                                        }
                                        ?>">inactive</button>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form id="cuform" role="form" method="post" action="" enctype="multipart/form-data">
                                        <input id="brand_id" type="hidden" name="brand_id" value="<?= $brand['id'] ?>"/>
                                        <input type="hidden" class="form-control" id="activeid" name="active" value="<?= $brand['status'] ?>"/>
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>Brand Name*</label>
                                            <input id="name" type="text" name="name" class="form-control" placeholder="Enter Brand Name..." value="<?= $brand['name'] ?>" required/>
                                        </div>
                                        <!-- text input -->
                                        <div class="form-group">
                                            <label>Slug*</label>
                                            <input type="text" class="form-control" name="slug" id="slug" placeholder="Slug" value="<?= $brand['slug'] ?>">
                                        </div>
                                        <!-- textarea -->
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea id="description" name="description" class="form-control" placeholder="Description"><?= $brand['description'] ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="brandimage">Header Image</label>
                                            <img id="brandimageimg" src="<?= !empty($brand['image']) ? $brand['image'] : 'http://placehold.it/1200x400' ?>" class="img-responsive"/>
                                            <span id="upload_status"></span>
                                            <input id="brandimage" type="hidden" name="brandimage" value="<?= $brand['image'] ?>"/>
                                            <input type="file" class="form-control" id="brandimageinput" placeholder="Brand Image">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" class="form-control" name="meta_title" id="meta_title" placeholder="Meta Title" value="<?= $brand['meta_title']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Keywords</label>
                                            <input type="text" class="form-control" name="meta_keywords" id="meta_keywords" placeholder="Meta Keywords" value="<?= $brand['meta_keywords']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea name="meta_description" id="meta_description" class="form-control" placeholder="Meta Description"><?= $brand['meta_description']; ?></textarea>
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
        <script>
            $("#activebid").click(function () {
                $("#activeid").val("A");
            });

            $("#inactivebid").click(function () {
                $("#activeid").val("I");
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