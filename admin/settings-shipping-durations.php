<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(SHIPPING_COMPANIES_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Add Shipping Duration
if (isset($_POST['label']) && isUserHavePermission(SHIPPING_DURATIONS_SECTION, getUserLoggedId())) {
    $shippingDuration['label'] = filter_var(trim($_POST['label']), FILTER_SANITIZE_STRING);
    $shippingDuration['duration_from'] = filter_var(trim($_POST['duration_from']), FILTER_SANITIZE_NUMBER_INT);
    $shippingDuration['duration_to'] = filter_var(trim($_POST['duration_to']), FILTER_SANITIZE_NUMBER_INT);
    $shippingDuration['days_or_week'] = filter_var(trim($_POST['days_or_week']), FILTER_SANITIZE_STRING);


    if ($shippingDuration['label'] == '') {
        $msg = '<div class="alert alert-danger">Please enter name</div>';
    } else {
        $msg = '<div class="alert alert-success">Shipping Duration added successfully!</div>';
        if (!addShippingDuration($shippingDuration)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

//Delete Shipping Company
if (isset($_GET['del']) && isUserHavePermission(SHIPPING_DURATIONS_SECTION, getUserLoggedId())) {
    $duration_id = filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT);
    if (deleteShippingDuration($duration_id)) {
        echo "<script>alert('Deleted successfully'); location.href='settings-shipping-companies.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='settings-shipping-companies.php';</script>";
    }
}
$shippingDurations = getShippingDurations();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Shipping Durations - Admin</title>
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
                        Shipping Durations
                        <small>List of created shipping durations</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="active">Shipping Durations</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">   
                        <div class="col-md-4">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add Shipping Duration</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">   
                                        <div class="form-group">
                                            <label for="label">Label</label>
                                            <input type="text" class="form-control" name="label" id="label" placeholder="Label" required>
                                        </div>
                                        <div class="row">  
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Duration From</label>
                                                    <select class="form-control" name="duration_from" required>
                                                        <?php for ($i = 1; $i <= 10; $i++) { ?>
                                                            <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Duration To</label>
                                                    <select class="form-control" name="duration_to" required>
                                                        <?php for ($i = 1; $i <= 10; $i++) { ?>
                                                            <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Business Days</label>
                                            <select class="form-control" name="days_or_week" required>                                                            
                                                <option value="D">Days</option>
                                                <option value="W">Week</option>
                                            </select>
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
                                        <table id="durationsT" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>                                                
                                                    <th>Name</th>
                                                    <th>Duration</th>
                                                    <th width="110px">Action</th>                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($shippingDurations as $shippingDuration) {
                                                    ?>
                                                    <tr>                                                    
                                                        <td><?= $shippingDuration['label'] ?></td>
                                                        <td><?= $shippingDuration['duration_from'] . " - " . $shippingDuration['duration_to'] . " " . $DAYSORWEEKS[$shippingDuration['days_or_week']]; ?></td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <?php if (isUserHavePermission(SHIPPING_DURATIONS_SECTION, getUserLoggedId())) { ?>
                                                                <a class="btn btn-sm btn-primary" href="<?= $sys['site_url'] . '/admin/settings-shipping-duration-edit.php?id=' . $shippingDuration['id'] ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                                <a class="btn btn-sm btn-danger" href="<?= $sys['site_url'] . '/admin/settings-shipping-durations.php?del=' . $shippingDuration['id'] ?>" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><i class="fa fa-trash"></i></a>
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
            $(function () {
                $('#durationsT').dataTable({
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