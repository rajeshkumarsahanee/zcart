<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(SHIPPING_COMPANIES_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$updatemsg = "";
//Update Shipping Duration
if (isset($_POST['label']) && isUserHavePermission(SHIPPING_DURATIONS_SECTION, getUserLoggedId())) {
    $shippingDuration['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $shippingDuration['label'] = filter_var(trim($_POST['label']), FILTER_SANITIZE_STRING);
    $shippingDuration['duration_from'] = filter_var(trim($_POST['duration_from']), FILTER_SANITIZE_NUMBER_INT);
    $shippingDuration['duration_to'] = filter_var(trim($_POST['duration_to']), FILTER_SANITIZE_NUMBER_INT);
    $shippingDuration['days_or_week'] = filter_var(trim($_POST['days_or_week']), FILTER_SANITIZE_STRING);

    if ($shippingDuration['label'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter name</div>';
    } else {
        $updatemsg = '<div class="alert alert-success">Shipping Duration updated successfully!</div>';
        if (!updateShippingDuration($shippingDuration)) {
            $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

$shippingDuration = getShippingDuration($_REQUEST['id']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Shipping Duration - Admin</title>
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
                        Edit Shipping Duration
                        <small>Edit shipping company</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="active"><a href="<?= $sys['site_url'] ?>/admin/shipping-companies">Shipping Companies</a></li>
                        <li class="active">Edit Shipping Duration</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Edit Shipping Duration</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">

                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">  
                                        <input type="hidden" name="id" value="<?= $shippingDuration['id'] ?>"/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="label">Label</label>
                                                    <input type="text" class="form-control" name="label" id="label" placeholder="Label" value="<?= $shippingDuration['label'] ?>" required>
                                                </div>                                            
                                            </div>   
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Duration From</label>
                                                    <select class="form-control" name="duration_from" required>
                                                        <?php for ($i = 1; $i <= 10; $i++) { ?>
                                                        <option value="<?= $i ?>" <?= $shippingDuration['duration_from'] == $i ? 'selected' : "" ?>><?= $i ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Duration To</label>
                                                    <select class="form-control" name="duration_to" required>
                                                        <?php for ($i = 1; $i <= 10; $i++) { ?>
                                                        <option value="<?= $i ?>" <?= $shippingDuration['duration_to'] == $i ? 'selected' : "" ?>><?= $i ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Business Days</label>
                                                    <select class="form-control" name="days_or_week" required>                                                            
                                                        <option value="D">Days</option>
                                                        <option value="W">Week</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>                                                                                                                                                                               
                                        <hr>
                                        <?= $updatemsg ?>
                                        <button type="submit" class="btn btn-primary">Update</button>
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
    </body>
</html>