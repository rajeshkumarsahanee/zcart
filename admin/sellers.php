<?php require_once 'check_login_status.php'; ?>
<?php 
if(isset($_REQUEST['del'])) {
    if(Sys_deleteSeller(trim($_REQUEST['del']))){
        echo '<script>alert("Seller deleted successfully!");</script>';
    }
}
$sellers = Sys_getSellers();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Admin | List Sellers</title>
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
                        List Sellers
                        <small>List of sellers</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active">Sellers</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Sellers</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">                            
                            <table class="table table-bordered table-striped" id='sellers'>
                                <thead>
                                    <tr>
                                        <th width="50px">##</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile/Phone</th>                                        
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    foreach ($sellers as $seller) { ?>
                                        <tr>
                                            <td><?php echo $i++;//$seller['id']; ?></td>
                                            <td><?php echo $seller['name']; ?></td>                                            
                                            <td><?php echo $seller['email']; ?></td>                                            
                                            <td><?php echo $seller['mobile']."/".$seller['phone']; ?></td>
                                            <td><?php echo $seller['status']; ?></td>
                                            <td>
                                                <div class='btn-group'>
                                                    <?php if (isUserHavePermission(SELLER_SECTION, EDIT_PERMISSION)) { ?>
                                                        <a class='btn btn-sm btn-primary' href="<?php echo $sys['config']['site_url'].'/admin/seller-edit?id='.$seller['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                        <a class='btn btn-sm btn-danger' href="<?php echo $sys['config']['site_url'].'/admin/sellers?del='.$seller['id']; ?>" onclick="return confirm('Are you sure you want to delete this seller?')" title="Delete"><i class="fa fa-trash"></i></a>
                                                    <?php } ?>                                                    
                                                </div>
                                            </td>                                                    
                                        </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                       <th width="50px">##</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile/Phone</th>                                        
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->

                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>        
        <script type="text/javascript">
            $(function () {
                    $('#sellers').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });             
            });                                  
        </script>                        
    </body>
</html>