<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (Sys_isUserHavePermission(STAFF_MEMBERS_SECTION, Sys_getAdminLoggedId())) {
    
} else {
    header("location: dashboard");
}

//Delete User
$deletemsg = "";
if (isset($_GET['del']) && Sys_isUserHavePermission(STAFF_MEMBERS_SECTION, Sys_getAdminLoggedId())) {
    if (Sys_deleteUser(trim($_REQUEST['del']))) {
        $deletemsg = "<script>alert('Deleted successfully');</script>";
    } else {
        $deletemsg = "<script>alert('Cannot delete');</script>";
    }
}

$users = Sys_getUsers('A', array("status" => 'A'), 0, 1000);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Admin Users - Admin</title>
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
                        Admin Users
                        <small>List of admin users</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active"><a href="#">Admin Users</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">List of admin</h3>
                            <div class="box-tools pull-right">
                                <a href="admin-add" class="btn btn-default btn-box-tool"><i class="fa fa-plus"></i>&nbsp; Add New Admin</a>
                            </div>
                        </div><!-- /.box-header -->
                        <div class="box-body">  
                            <?php if(isset($msg)) { echo $msg; } ?>
                            <table id="users" class="table table-bordered table-striped">
                                <thead>
                                    <tr>               
                                        <th>Display Name</th>
                                        <th>Username</th>                                                                                
                                        <th>Email</th>
                                        <th>Registered</th>                                        
                                        <th>Status</th>                                        
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php                       
                                    $i=1;
                                    foreach ($users as $user) {
                                        ?>
                                        <tr>                                       
                                            <td><?php echo $user['display_name']; ?></td>
                                            <td><?php echo $user['username'] ?></td>
                                            <td><?php echo $user['email']; ?></td>
                                            <td><?php echo $user['registered']; ?></td>
                                            <td><?php echo $user['status']; ?></td>                                            
                                            <td>     
                                                <?php if($user['id'] <> 1) { ?>
                                                <div class="btn-group">
                                                    <?php if(Sys_isUserHavePermission(STAFF_MEMBERS_SECTION, Sys_getAdminLoggedId())) { ?>
                                                    <a class='btn btn-sm btn-default' href="<?php echo $sys['config']['site_url'].'/admin/admin-edit?id='.$user['id']; ?>" title="Edit User"><i class="fa fa-pencil"></i></a>
                                                    <?php } ?>
                                                    <?php if(Sys_isUserHavePermission(STAFF_MEMBERS_SECTION, Sys_getAdminLoggedId())) { ?>
                                                    <a class='btn btn-sm btn-danger' href="<?php echo $sys['config']['site_url'].'/admin/users?del='.$user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')" title="Delete User"><i class="fa fa-trash"></i></a>
                                                    <?php } ?>
                                                </div>                      
                                                <?php } ?>
                                            </td>
                                        </tr>                      
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>               
                                        <th>Display Name</th>
                                        <th>Username</th>                                                                                
                                        <th>Email</th>
                                        <th>Registered</th>                                        
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
                $('#users').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });
            });

        </script>     
        <?php echo $deletemsg; ?>
    </body>
</html>