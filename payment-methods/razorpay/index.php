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
    $fields['keyid'] = filter_var(trim($_POST['keyid']), FILTER_SANITIZE_STRING);
    $fields['keysecret'] = filter_var(trim($_POST['keysecret']), FILTER_SANITIZE_STRING);
    $fields['mode'] = filter_var(trim($_POST['mode']), FILTER_SANITIZE_STRING);
    $pmethod['fields'] = json_encode($fields);
    
    if ($fields['keyid'] == '' || $fields['keysecret'] == '' || $fields['mode'] == '') {
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
                                                    <label>Key Id*</label>
                                                    <input type="text" class="form-control" name="keyid" value="<?= isset($fields['keyid']) ? $fields['keyid'] : '' ?>" id="keyid" placeholder="Key Id" required/>
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Key Secret*</label>
                                                    <input type="text" class="form-control" name="keysecret" value="<?= isset($fields['keysecret']) ? $fields['keysecret'] : '' ?>" id="keysecret" placeholder="Key Secret" required/>
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Transaction Mode*</label>
                                                    <select name="mode" class="form-control" required>
                                                        <option value="">Select</option>
                                                        <option value="test" <?= $fields['mode'] == 'test' ? 'selected' : '' ?>>Test/Sandbox</option>
                                                        <option value="live" <?= $fields['mode'] == 'live' ? 'selected' : '' ?>>Live</option>
                                                    </select>
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