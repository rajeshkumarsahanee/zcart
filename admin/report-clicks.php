<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (isUserHavePermission(REPORT_SECTION, VIEW_PERMISSION) || isUserHavePermission(REPORT_SECTION, MANAGE_PERMISSION)) {
    
} else {
    header("location: dashboard");
}

$from = date('Y-m-1');
$to = date('Y-m-d');
if (isset($_REQUEST['daterange']) && strpos(trim($_REQUEST['daterange']), "-") != FALSE) {
    $dr = explode("-", urldecode($_REQUEST['daterange']));
    $from = date("Y-m-d", strtotime($dr[0]));
    $to = date("Y-m-d", strtotime($dr[1]));
}

$productclicks = getProductClicks($from, $to);
$sellers = Sys_getSellers();        

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Clicks Report - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link href="<?php echo $sys['config']['site_url'] . '/admin/plugins/daterangepicker/daterangepicker-bs3.css'; ?>" rel="stylesheet" type="text/css" />
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
                        Clicks Report
                        <small>Optional description</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active">Clicks Report</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <!-- Info boxes -->      

                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Clicks Report</h3>

                                    <div class="box-tools pull-right">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input class="form-control pull-right" id="reportdaterange" type="text">
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <p class="text-center">
                                                <strong><?php echo date("d M, Y", strtotime($from)) . " - " . date("d M, Y", strtotime($to)); ?></strong>
                                            </p>

                                            <div class="chart">
                                                <table class="table">
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Seller</th>
                                                        <th>Date</th>
                                                        <th>Clicks</th>
                                                    </tr>
                                                    <?php
                                                    $totalclicks = 0;
                                                    $scarr = array();
                                                    foreach ($productclicks as $productclick) {
                                                        $totalclicks += $productclick['clicks'];
                                                        if (!array_key_exists($productclick['seller_id'], $scarr)) {
                                                            $scarr[$productclick['seller_id']] = 0;
                                                        }
                                                        $scarr[$productclick['seller_id']] += $productclick['clicks'];
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $productclick['product_id'] ?></td>
                                                            <td><?php echo $sellers[$productclick['seller_id']]['name']; ?></td>
                                                            <td><?php echo $productclick['c_date']; ?></td>
                                                            <td><?php echo $productclick['clicks']; ?></td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                </table>

                                            </div>
                                            <!-- /.chart-responsive -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-md-4">
                                            <p class="text-center">
                                                <strong>Seller Clicks</strong>
                                            </p>
                                            <?php
                                            foreach ($sellers as $seller) {
                                                if (!array_key_exists($seller['id'], $scarr)) {
                                                    $scarr[$seller['id']] = 0;
                                                }
                                                if ($totalclicks == 0) {
                                                    $percent = 0;
                                                } else {
                                                    $percent = ($scarr[$seller['id']] / $totalclicks) * 100;
                                                }


                                                if ($percent > 80) {
                                                    $pbclass = 'progress-bar-green';
                                                } else if ($percent > 60) {
                                                    $pbclass = 'progress-bar-aqua';
                                                } else if ($percent > 30) {
                                                    $pbclass = 'progress-bar-yellow';
                                                } else if ($percent >= 0) {
                                                    $pbclass = 'progress-bar-red';
                                                }
                                                ?>

                                                <div class="progress-group">
                                                    <span class="progress-text"><?php echo $seller['name'] ?></span>
                                                    <span class="progress-number"><b><?php echo $scarr[$seller['id']]; ?></b>/<?php echo $totalclicks; ?></span>

                                                    <div class="progress sm">
                                                        <div class="progress-bar <?php echo $pbclass ?>" style="width: <?php echo $percent . "%"; ?>"></div>
                                                    </div>
                                                </div>
                                                <!-- /.progress-group -->
                                            <?php } ?>                  
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->
                                </div>
                                <!-- ./box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block border-right">                    
                                                <h5 class="description-header"><?php echo $totalclicks; ?></h5>
                                                <span class="description-text">TOTAL CLICKS</span>
                                            </div>
                                            <!-- /.description-block -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block border-right">                                                
                                                <h5 class="description-header"></h5>
                                                <span class="description-text">NA</span>
                                            </div>
                                            <!-- /.description-block -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block border-right">                                                
                                                <h5 class="description-header"></h5>
                                                <span class="description-text">NA</span>
                                            </div>
                                            <!-- /.description-block -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block">                                                
                                                <h5 class="description-header"></h5>
                                                <span class="description-text">NA</span>
                                            </div>
                                            <!-- /.description-block -->
                                        </div>
                                    </div>
                                    <!-- /.row -->
                                </div>
                                <!-- /.box-footer -->
                            </div>
                            <!-- /.box -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>    
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
        <script src="<?php echo $sys['config']['site_url'].'/admin/plugins/daterangepicker/daterangepicker.js'; ?>" type="text/javascript"></script>
        <script>
            $(function () {
                $('#inquiries').dataTable({
                    "bPaginate": true,
                    "bLengthChange": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    "bAutoWidth": false
                });

                //Date range picker
                $('#reportdaterange').daterangepicker({format: 'YYYYMMDD'});
                $('#reportdaterange').on("change", function () {
                    window.location.href = "<?php echo $sys['config']['site_url'].'/admin/report-clicks'; ?>?daterange=" + encodeURI($(this).val());
                });
            });

        </script>
    </body>
</html>