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
//
$searches = getSearches($from, $to);
//$sellers = Sys_getSellers();        
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Searches Report - Admin</title>
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
                        Searches Report
                        <small>Optional description</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active">Searches Report</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <!-- Info boxes -->      

                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Searches Report</h3>

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
                                        <div class="col-md-12">
                                            <p class="text-center">
                                                <strong><?php echo date("d M, Y", strtotime($from)) . " - " . date("d M, Y", strtotime($to)); ?></strong>
                                            </p>

                                            <div class="chart">
                                                <table class="table">
                                                    <tr>
                                                        <th>Keyword</th>                              
                                                        <th>Date</th>
                                                        <th>Ip Address</th>
                                                        <th>Previous Searches</th>
                                                    </tr>
                                                    <?php
                                                    foreach ($searches as $search) {
                                                        //$totalviews += $productview['views'];
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $search['keyword'] ?></td>                              
                                                            <td><?php echo $search['s_date'] ?></td>
                                                            <td><?php echo $search['ip_address'] ?></td>
                                                            <td><?php echo $search['previous_search_counts'] ?></td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                </table>

                                            </div>
                                            <!-- /.chart-responsive -->
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
                                              <!--<span class="description-percentage text-green"><i class="fa fa-caret-up"></i> 17%</span>-->
                                                <h5 class="description-header"><?php ?></h5>
                                                <span class="description-text"></span>
                                            </div>
                                            <!-- /.description-block -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block border-right">
                                              <!--<span class="description-percentage text-yellow"><i class="fa fa-caret-left"></i></span>-->
                                                <h5 class="description-header"></h5>
                                                <span class="description-text"></span>
                                            </div>
                                            <!-- /.description-block -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block border-right">
                                              <!--<span class="description-percentage text-green"><i class="fa fa-caret-up"></i> </span>-->
                                                <h5 class="description-header"></h5>
                                                <span class="description-text"></span>
                                            </div>
                                            <!-- /.description-block -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="description-block">
                                              <!--<span class="description-percentage text-red"><i class="fa fa-caret-down"></i> </span>-->
                                                <h5 class="description-header"></h5>
                                                <span class="description-text"></span>
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
                //Date range picker
                $('#reportdaterange').daterangepicker({format: 'YYYYMMDD'});
                $('#reportdaterange').on("change", function () {
                    window.location.href = "<?php echo $sys['config']['site_url'].'/admin/report-searches'; ?>?daterange=" + encodeURI($(this).val());
                });
            });
        </script>
    </body>
</html>