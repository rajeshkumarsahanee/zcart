<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(CURRENCY_MANAGEMENT_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Add Currency
if (isset($_POST['code']) && isset($_POST['title']) && isUserHavePermission(CURRENCY_MANAGEMENT_SECTION, getUserLoggedId())) {
    $currency['title'] = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
    $currency['code'] = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING);
    $currency['decimal_places'] = filter_var(trim($_POST['decimal_places']), FILTER_SANITIZE_NUMBER_INT);
    $currency['decimal_separator'] = filter_var(trim($_POST['decimal_separator']), FILTER_SANITIZE_STRING);
    $currency['thousand_separator'] = filter_var(trim($_POST['thousand_separator']), FILTER_SANITIZE_STRING);
    $currency['symbol_left'] = filter_var(trim($_POST['symbol_left']), FILTER_SANITIZE_STRING);
    $currency['symbol_right'] = filter_var(trim($_POST['symbol_right']), FILTER_SANITIZE_STRING);
    $currency['countries'] = filter_var(trim($_POST['countries']), FILTER_SANITIZE_STRING);
    $currency['rate_usd_base'] = filter_var(trim($_POST['rate_usd_base']), FILTER_SANITIZE_STRING);
    $currency['rate_dc_base'] = filter_var(trim($_POST['rate_dc_base']), FILTER_SANITIZE_STRING);
    $currency['rate_last_updated'] = date("Y-m-d H:i:s");
    $currency['rate_last_updated_by'] = getUserLoggedId();
        
    if ($currency['title'] == '' || $currency['code'] == '' || $currency['decimal_places'] == '') {
        $msg = '<div class="alert alert-danger">Title, Code and Decimal Places are required</div>';
    } else {        
        $msg = '<div class="alert alert-success">Currency added successfully!</div>';
        if (!addCurrency($currency)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

//Delete Currency
if (isset($_GET['del']) && isUserHavePermission(CURRENCY_MANAGEMENT_SECTION, getUserLoggedId())) {    
    $id = filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT);
    if (deleteCurrency($id)) {
        echo "<script>alert('Deleted successfully'); location.href='settings-currencies.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='settings-currencies.php';</script>";
    }
}
$filters = array();
/*add filters if required*/
$currencies = getCurrencies(array("id", "title", "code"), $filters, 0, -1);
$dCurrencyCode = getConfig("DEFAULT_CURRENCY");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Currencies - Admin</title>
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
                        Currencies
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="active">Currency Management</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary <?= isset($_POST['code']) ? '' : 'collapsed-box' ?>">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add New Currency</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data"> 
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="title">Title*</label>
                                                    <input type="text" name="title" id="title" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="code">Code*</label>
                                                    <input type="text" name="code" id="code" class="form-control" placeholder="Currency Code" required/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="symbol_left">Symbol Left</label>
                                                    <input type="text" class="form-control" name="symbol_left" id="symbol_left" placeholder="Symbol Left"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="symbol_right">Symbol Right</label>
                                                    <input type="text" class="form-control" name="symbol_right" id="symbol_right" placeholder="Symbol Right"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="decimal_places">Decimal Places*</label>
                                                    <input type="text" class="form-control" name="decimal_places" id="decimal_places" placeholder="Decimal Places"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="decimal_separator">Decimal Separator</label>
                                                    <input type="text" class="form-control" name="decimal_separator" id="decimal_separator" placeholder="Decimal Separator"/>
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
                                                    <textarea class="form-control" name="countries" id="countries" placeholder="Country Codes"></textarea>
                                                    Please type in comma separated multiple country codes where this currency will be applied
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="rate_usd_base">Rate USD Base</label>
                                                    <input type="text" class="form-control" name="rate_usd_base" id="rate_usd_base" placeholder="Rate USD Based"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="rate_dc_base">Rate <?= $dCurrencyCode !== null ? $dCurrencyCode : "INR" ?> Base</label>
                                                    <input type="text" class="form-control" name="rate_dc_base" id="rate_dc_base" placeholder="Rate Default Currency Based"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="rate_last_updated">Rate Last Updated</label>
                                                    <input type="text" class="form-control" name="rate_last_updated" id="rate_last_updated" placeholder="Rate Last Updated" readonly/>
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
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body">                                                                        
                                    <div class="">
                                        <table id="currenciesT" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>  
                                                    <th>Title</th>
                                                    <th>Code</th>
                                                    <th width="100px">Action</th>                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($currencies as $c) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $c['title'] ?><?= $dCurrencyCode == $c['code'] ? "<b> - Default</b>" : "" ?></td>
                                                        <td><?= $c['code']  ?></td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <?php if (isUserHavePermission(CURRENCY_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                                                                    <a class='btn btn-sm btn-primary' href="<?= $sys['site_url'] . '/admin/settings-currency-edit.php?id=' . $c['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                                    <a class='btn btn-sm btn-danger' href="<?= $sys['site_url'] . '/admin/settings-currencies.php?del=' . $c['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><i class="fa fa-trash"></i></a>
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
            $('#currenciesT').dataTable({
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