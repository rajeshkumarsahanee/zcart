<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(SHIPPING_COMPANIES_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$updatemsg = "";
//Update Shipping Company
if (isset($_POST['name']) && isUserHavePermission(SHIPPING_COMPANIES_SECTION, getUserLoggedId())) {
    $shippingCompany['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $shippingCompany['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $shippingCompany['website'] = filter_var(trim($_POST['website']), FILTER_SANITIZE_STRING);
    $shippingCompany['comments'] = filter_var(trim($_POST['comments']), FILTER_SANITIZE_STRING);
    $shippingCompany['status'] = "A";

    if ($shippingCompany['name'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter name</div>';
    } else {
        $updatemsg = '<div class="alert alert-success">Shipping Company updated successfully!</div>';
        if (!updateShippingCompany($shippingCompany)) {
            $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

$shippingCompany = getShippingCompany($_REQUEST['id']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Shipping Company - Admin</title>
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
                        Edit Shipping Company
                        <small>Edit shipping company</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="active"><a href="<?= $sys['site_url'] ?>/admin/settings-shipping-companies.php">Shipping Companies</a></li>
                        <li class="active">Edit Shipping Company</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Edit Shipping Company</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">

                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">  
                                        <input type="hidden" name="id" value="<?php echo $shippingCompany['id']; ?>"/>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $shippingCompany['name'] ?>" placeholder="Enter Name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="website">Website</label>
                                                    <input type="text" class="form-control" name="website" id="website" value="<?php echo $shippingCompany['website'] ?>" placeholder="Website">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="comments">Comments</label>
                                                    <input type="text" class="form-control" name="comments" id="comments" value="<?php echo $shippingCompany['comments'] ?>" placeholder="Comments">
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