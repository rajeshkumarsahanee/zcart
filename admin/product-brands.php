<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(PRODUCT_BRANDS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

//Delete Category
if (isset($_GET['del']) && isUserHavePermission(PRODUCT_BRANDS_SECTION, getUserLoggedId())) {

    if (deleteBrand(filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT))) {
        echo "<script>alert('Deleted successfully'); location.href='product-brands.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='product-brands.php';</script>";
    }
}
$brands = getBrands(array(), array(), 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Product Brands - Admin</title>
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
                        Product Brands
                        <small>List of created brands</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active">Product Brands</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">                        
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Brands</h3>
                                    <div class="pull-right">
                                        <a class="btn btn-primary btn-sm" href="product-brand-add.php">Add Brand</a>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">                                                                        
                                    <div class="">
                                        <table id="brands" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>                                                
                                                    <th>S.No.</th>
                                                    <th>Name</th>
                                                    <th>Items</th>
                                                    <th>Status</th>
                                                    <th width="110px">Action</th>                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($brands as $brand) {
                                                    ?>
                                                    <tr>                                                    
                                                        <td><?= $i++ ?></td>
                                                        <td><?= $brand['name']; ?></td>
                                                        <td><a href="products.php?brand=<?= $brand['id'] ?>"><?= $brand['items_count']; ?></a></td>
                                                        <td><?= $sys['statuses'][$brand['status']] ?></td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <?php if (isUserHavePermission(PRODUCT_BRANDS_SECTION, getUserLoggedId())) { ?>
                                                                <a class='btn btn-sm btn-primary' href="<?= $sys['site_url'] . '/admin/product-brand-edit.php?id=' . $brand['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                                <a class='btn btn-sm btn-danger' href="<?= 'product-brands.php?del=' . $brand['id']; ?>" onclick="return confirm('Are you sure you want to delete this brand?')" title="Delete"><i class="fa fa-trash"></i></a>
                                                                <?php } ?>                                                                
                                                            </div>
                                                        </td>                                                    
                                                    </tr>
                                                <?php } ?>
                                            </tbody>                                        
                                        </table>                                
                                    </div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col-md-8 -->
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>
        <script type="text/javascript">
            $(function () {
                $('#brands').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
            });
        </script>        
        <!-- Modal -->        
    </body>
</html>