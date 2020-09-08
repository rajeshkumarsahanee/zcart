<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(MANAGE_SHOPS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$errormsg = "";

//Delete Category
if (isset($_GET['del']) && isUserHavePermission(MANAGE_SHOPS_SECTION, getUserLoggedId())) {
    $data['status_message'] = "Deleted";
    $data['status'] = "T";
    $where['id'] = filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT);
    if (update(T_SHOPS, $data, $where)) {
        echo "<script>alert('Deleted successfully'); location.href='shops.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='shops.php';</script>";
    }
}

$shops = getShops(array(), array(), 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Manage Shops - Admin</title>
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
                        Manage Shops
                        <small>List of shops</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active">Shops</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">                        
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Shops</h3>
                                    <div class="pull-right">
                                        <!--<a class="btn btn-primary btn-sm" href="shop-add">Add Shop</a>-->
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">                                                                        
                                    <div class="">
                                        <table id="shops" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>S.No.</th>                                                
                                                    <th>Shop Owner</th>
                                                    <th>Name</th>                                                
                                                    <th>Items</th>                                                  
                                                    <th>Reviews</th>
                                                    <th>Reports</th>
                                                    <th>Active</th>
                                                    <th>Display Status</th>
                                                    <th width="110px">Action</th>                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($shops as $shop) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $i++ ?></td>
                                                        <td><?= $shop['contact_person_name']; ?></td>
                                                        <td><?= $shop['name']; ?></td>
                                                        <td><a href="<?= $sys['site_url'] . "/admin/products.php?shop=" . $shop['id']; ?>"><?= $shop['items_count'] ?></a></td>
                                                        <td><a href="<?= $sys['site_url'] . "/admin/product-reviews.php?shop=" . $shop['id']; ?>"><?= $shop['reviews_count'] ?></a></td>
                                                        <td><a href="<?= $sys['site_url'] . "/admin/shop-reports.php?shop=" . $shop['id']; ?>"><?= $shop['reports_count'] ?></a></td>
                                                        <td><?= $sys['statuses'][$shop['status']]; ?></td>
                                                        <td><?= $shop['status_message'] ?></td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <?php if (isUserHavePermission(MANAGE_SHOPS_SECTION, getUserLoggedId()) && $shop['id'] != 1) { ?>
                                                                <a class='btn btn-sm btn-primary' href="<?= $sys['site_url'] . '/admin/shop-edit.php?id=' . $shop['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                                <a class='btn btn-sm btn-danger' href="<?= $sys['site_url'] . '/admin/shops.php?del=' . $shop['id']; ?>" onclick="return confirm('Are you sure you want to delete this shop?')" title="Delete"><i class="fa fa-trash"></i></a>
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
                $('#shops').dataTable({
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