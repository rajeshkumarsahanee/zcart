<?php require_once 'check_login_status.php'; ?>
<?php 
if(isset($_REQUEST['del'])) {
    if(Sys_deleteLister(trim($_REQUEST['del']))){
        echo '<script>alert("Lister deleted successfully!");</script>';
    }
}
$listers = Sys_getListers();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>List Listers - Admin</title>
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
                        List Listers
                        <small>List of listers</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active">Listers</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Listers</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">                            
                            <table class="table table-bordered table-striped" id='listers'>
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
                                    foreach ($listers as $lister) { ?>
                                        <tr>
                                            <td><?php echo $i++;//$lister['id']; ?></td>
                                            <td><?php echo $lister['name']; ?></td>                                            
                                            <td><?php echo $lister['email']; ?></td>                                            
                                            <td><?php echo $lister['mobile']."/".$lister['phone']; ?></td>
                                            <td><?php echo $lister['status']; ?></td>
                                            <td>
                                                <div class='btn-group'>
                                                    <?php if (isUserHavePermission(SELLER_SECTION, EDIT_PERMISSION)) { ?>
                                                        <a class='btn btn-sm btn-primary' href="<?php echo $sys['config']['site_url'].'/admin/lister-edit?id='.$lister['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                        <a class='btn btn-sm btn-danger' href="<?php echo $sys['config']['site_url'].'/admin/listers?del='.$lister['id']; ?>" onclick="return confirm('Are you sure you want to delete this lister?')" title="Delete"><i class="fa fa-trash"></i></a>
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
                    $('#listers').dataTable({
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