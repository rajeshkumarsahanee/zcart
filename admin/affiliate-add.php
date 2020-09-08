<?php require_once 'check_login_status.php'; ?>
<?php 
if(!isUserHavePermission(AFFILIATE_SECTION, ADD_PERMISSION)) {
    header("location: affiliates");
}

$savemsg = "";
if (isset($_POST['save'])) {
    $affiliate['name'] = filter_var(trim($_POST['affiliate_name']),FILTER_SANITIZE_STRING);
    $affiliate['affiliate_id'] = filter_var(trim($_POST['affiliate_id']),FILTER_SANITIZE_STRING);
    $affiliate['tracking_id'] = filter_var(trim($_POST['tracking_id']),FILTER_SANITIZE_STRING);
    $affiliate['status'] = htmlspecialchars(addslashes(trim($_POST['active'])));
    
    if(trim($affiliate['name']) == "") {
        $savemsg = '<div class="alert alert-danger">Affiliate name required!</div>';
    } else {
        if (Sys_addAffiliate($affiliate)) {
            $savemsg = '<div class="alert alert-success">Affiliate added successfully!</div>';
        } else {
            $savemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Add Affiliate - Admin</title>
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
                        Add Affiliate
                        <small>Add affiliate</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?php echo $sys['config']['site_url'].'/admin/affiliates'; ?>">Affiliates</a></li>
                        <li class="active"><a href="#">Add Affiliate</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Add New Affiliate</h3>
                                <div class="btn-group pull-right" data-toggle="btn-toggle">
                                    <button type="button" id="activebid" class="btn btn-default btn-sm">active</button>
                                    <button type="button" id="inactivebid" class="btn btn-default btn-sm">inactive</button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" action="" method="post">
                                <div class="">
                                    <div class="box-body">                                    
                                        <input type="hidden" class="form-control" id="activeid" name="active"/>
                                        <div class="form-group">                                              
                                            <label for="affiliate_name">Affiliate Name</label>
                                            <input type="text" class="form-control" id="affiliate_name" name="affiliate_name"  placeholder="Affiliate Name"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="affiliate_id">Affiliate ID</label>
                                            <input type="text" class="form-control" id="affiliate_id" name="affiliate_id" placeholder="Affiliate ID" />
                                        </div>
                                        <div class="form-group">
                                            <label for="tracking_id">Tracking ID</label>
                                            <input type="text" class="form-control" id="tracking_id" name="tracking_id" placeholder="Tracking ID"/>
                                        </div>                                        
                                    </div>
                                </div>
                                <!-- /.box-body -->

                                <div class="box-footer">
                                    <?php if(isset($savemsg)) { echo $savemsg; } ?>
                                    <button type="submit" class="btn btn-primary" name="save">Save</button>
                                </div>
                            </form>
                        </div><!-- /.box -->
                    </div>

                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>  

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>            
        <script>
            $("#activebid").click(function () {
                $("#activeid").val("1");
            });

            $("#inactivebid").click(function () {
                $("#activeid").val("0");
            });
        </script>
    </body>
</html>