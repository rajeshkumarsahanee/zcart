<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(ZONE_MANAGEMENT_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Add Country
if (isset($_POST['name']) && isUserHavePermission(ZONE_MANAGEMENT_SECTION, getUserLoggedId())) {    
    $zone['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $zone['description'] = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    $zone['status'] = 'A';
        
    if ($zone['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter zone name</div>';
    } else {        
        $msg = '<div class="alert alert-success">Zone added successfully!</div>';
        if (!addZone($zone)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

//Delete Country
if (isset($_GET['del']) && isUserHavePermission(ZONE_MANAGEMENT_SECTION, getUserLoggedId())) {    
    $id = filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT);
    if (deleteZone($id)) {
        echo "<script>alert('Deleted successfully'); location.href='settings-zones.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='settings-zones.php';</script>";
    }
}
$filters = array();
/*add filters if required*/
$zones = getZones(array("id", "name"), $filters, 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Zones - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link href="<?= $sys['site_url'] ?>/admin/plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
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
                        Zones
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="active">Zone Management</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add New Zone</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data"> 
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Name*</label>
                                                    <input type="text" class="form-control" name="name" id="name" placeholder="Zone Name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="description">Description</label>
                                                    <textarea class="form-control" name="description" id="description" placeholder="Description"></textarea>
                                                </div>
                                            </div>
                                        </div>                                                      
                                        <hr>
                                        <?= $msg ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </form>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                        <div class="col-md-8">
                            <div class="box">
                                <div class="box-body">                                                                        
                                    <div class="">
                                        <table id="zonesT" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>  
                                                    <th>S.No.</th>
                                                    <th>Name</th>
                                                    <th width="100px">Action</th>                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($zones as $z) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $i++ ?></td>
                                                        <td><?= $z['name'] ?></td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <?php if (isUserHavePermission(COUNTRY_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                                                                    <a class='btn btn-sm btn-primary' href="<?= $sys['site_url'] . '/admin/settings-zone-edit.php?id=' . $z['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                                    <a class='btn btn-sm btn-danger' href="<?= $sys['site_url'] . '/admin/settings-zones.php?del=' . $z['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><i class="fa fa-trash"></i></a>
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
        <script src="<?= $sys['site_url'] ?>/admin/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="<?= $sys['site_url'] ?>/admin/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $('#zonesT').dataTable({
                "bPaginate": true,
                "bLengthChange": true,
                "bFilter": true,
                "bSort": true,
                "bInfo": true,
                "bAutoWidth": false
            });         
        </script>        
        <!-- Modal -->        
    </body>
</html>