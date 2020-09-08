<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(CURRENCY_MANAGEMENT_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

if(isset($_REQUEST['default']) && trim($_REQUEST['default']) <> "" && isUserHavePermission(CURRENCY_MANAGEMENT_SECTION, getUserLoggedId())) {
    $currency_code = filter_var(trim($_REQUEST['default']), FILTER_SANITIZE_STRING);
    $currency = getCurrency($currency_code, array("code"));    
    if($currency !== null && saveConfig("DEFAULT_CURRENCY", $currency['code'])) {
        $msg = '<div class="alert alert-success">Default currency changed successfully!</div>';
    }
}

//Update Currency
if (isset($_POST['id']) && isset($_POST['code']) && isUserHavePermission(CURRENCY_MANAGEMENT_SECTION, getUserLoggedId())) {
    $currency['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $currency['title'] = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
    $currency['code'] = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING);
    $currency['decimal_places'] = filter_var(trim($_POST['decimal_places']), FILTER_SANITIZE_NUMBER_INT);
    $currency['decimal_separator'] = filter_var(trim($_POST['decimal_separator']), FILTER_SANITIZE_STRING);
    $currency['thousand_separator'] = filter_var(trim($_POST['thousand_separator']), FILTER_SANITIZE_STRING);
    $currency['symbol_left'] = filter_var(trim($_POST['symbol_left']), FILTER_SANITIZE_STRING);
    $currency['symbol_right'] = filter_var(trim($_POST['symbol_right']), FILTER_SANITIZE_STRING);
    $currency['countries'] = filter_var(trim($_POST['countries']), FILTER_SANITIZE_STRING);
    $currency['rate_usd_base'] = filter_var(trim($_POST['rate_usd_base']), FILTER_SANITIZE_STRING);
    $currency['rate_inr_base'] = filter_var(trim($_POST['rate_inr_base']), FILTER_SANITIZE_STRING);
    $currency['rate_last_updated'] = date("Y-m-d H:i:s");
    $currency['rate_last_updated_by'] = getUserLoggedId();
        
    if ($currency['title'] == '' || $currency['code'] == '' || $currency['decimal_places'] == '') {
        $msg = '<div class="alert alert-danger">Title, Code and Decimal Places are required</div>';
    } else {        
        $msg = '<div class="alert alert-success">Currency updated successfully!</div>';
        if (!updateCurrency($currency)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

$id = filter_var(trim($_REQUEST['id']), FILTER_SANITIZE_NUMBER_INT);
/*add filters if required*/
$currency = getCurrency($id);
if($currency == null) {
    header("location: settings-currencies.php");
    exit();
}
$dCurrencyCode = getConfig("DEFAULT_CURRENCY");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Currency - Admin</title>
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
                        Edit Currency
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="">Currency Management</li>
                        <li class="active">Edit Currency</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="box box-primary">
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data"> 
                                        <input type="hidden" name="id" value="<?= $currency['id'] ?>"/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="title">Title*</label>
                                                    <input type="text" name="title" value="<?= $currency['title'] ?>" id="title" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="code">Code*</label>
                                                    <input type="text" name="code" value="<?= $currency['code'] ?>" id="code" class="form-control" placeholder="Currency Code" required/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="symbol_left">Symbol Left</label>
                                                    <input type="text" class="form-control" name="symbol_left" value="<?= $currency['symbol_left'] ?>" id="symbol_left" placeholder="Symbol Left"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="symbol_right">Symbol Right</label>
                                                    <input type="text" class="form-control" name="symbol_right" value="<?= $currency['symbol_right'] ?>" id="symbol_right" placeholder="Symbol Right"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="decimal_places">Decimal Places*</label>
                                                    <input type="text" class="form-control" name="decimal_places" value="<?= $currency['decimal_places'] ?>" id="decimal_places" placeholder="Decimal Places"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="decimal_separator">Decimal Separator</label>
                                                    <input type="text" class="form-control" name="decimal_separator" value="<?= $currency['decimal_separator'] ?>" id="decimal_separator" placeholder="Decimal Separator"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="thousand_separator">Thousand Separator</label>
                                                    <input type="text" class="form-control" name="thousand_separator" id="thousand_separator" placeholder="Thousand Separator"/>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="countries">Countries</label>
                                                    <textarea class="form-control" name="countries" id="countries" placeholder="Country Codes"><?= $currency['countries'] ?></textarea>
                                                    Please type in comma separated multiple country codes where this currency will be applied
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="rate_usd_base">Rate USD Base</label>
                                                    <input type="text" class="form-control" name="rate_usd_base" value="<?= $currency['rate_usd_base'] ?>" id="rate_usd_base" placeholder="Rate USD Based"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="rate_dc_base">Rate <?= $dCurrencyCode !== null ? $dCurrencyCode : "INR" ?> Base</label>
                                                    <input type="text" class="form-control" name="rate_dc_base" value="<?= $currency['rate_dc_base'] ?>" id="rate_usd_base" placeholder="Rate Default Currency Based"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="rate_last_updated">Rate Last Updated</label>
                                                    <input type="text" class="form-control" name="rate_last_updated" value="<?= $currency['rate_last_updated'] ?>" id="rate_last_updated" placeholder="Rate Last Updated" readonly/>
                                                </div>
                                            </div>
                                        </div>                                                      
                                        <hr>
                                        <?= $msg ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <?php
                                        if ($currency['code'] == getConfig("DEFAULT_CURRENCY")) {
                                            echo '<span class="pull-right text-bold">Default</span>';
                                        } else {
                                            echo '<a href="settings-currency-edit.php?default=' . $currency['code'] . '" class="btn btn-default pull-right">Make Default</a>';
                                        }
                                        ?> 
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