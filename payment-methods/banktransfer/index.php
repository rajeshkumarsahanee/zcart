<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(PAYMENT_METHODS_SECTION, getUserLoggedId())) {
    header("location: settings-payment-method.php");
    exit();
}

$msg = "";

//Update Payment Method Fields
if (isset($_POST['code']) && isUserHavePermission(PAYMENT_METHODS_SECTION, getUserLoggedId())) {
    $code = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING);
    $pmethod = getPaymentMethod($code);
    $fields['bankdetails'] = filter_var(trim($_POST['bankdetails']), FILTER_SANITIZE_STRING);
    $pmethod['fields'] = json_encode($fields);
    
    if ($fields['bankdetails'] == '') {
        $msg = '<div class="alert alert-danger">Please enter all fields</div>';
    } else {        
        $msg = '<div class="alert alert-success">Payment method settings updated successfully!</div>';
        if (!updatePaymentMethod($pmethod)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

$code = filter_var(trim($_REQUEST['code']), FILTER_SANITIZE_STRING);
/*add filters if required*/
$pmethod = getPaymentMethod($code);
if($pmethod == null) {
    header("location: settings-payment-methods.php");
}
$fields = json_decode($pmethod['fields'], true);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Payment Method Settings [<?= $pmethod['name'] ?>] - Admin</title>
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
                        Payment Method Settings [<?= $pmethod['name'] ?>]
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li><a href="<?= $sys['site_url'] ?>/admin/settings-payment-methods.php">Payment Methods</a></li>
                        <li class="active">Edit Payment Method Settings</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body">                                                                        
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $pmethod['id'] ?>"/>
                                        <input type="hidden" name="code" value="<?= $pmethod['code'] ?>"/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Bank Details*</label>
                                                    <textarea class="form-control" name="bankdetails" id="bankdetails" placeholder="Bank Details" required><?= isset($fields['bankdetails']) ? $fields['bankdetails'] : '' ?></textarea>
                                                    Please enter your bank details here.
                                                </div> 
                                            </div>
                                        </div>
                                        <?= $msg ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </form>
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
    </body>
</html>