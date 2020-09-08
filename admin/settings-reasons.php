<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(REASONS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Add Reason
if (isset($_POST['reason_type']) && isset($_POST['title']) && isUserHavePermission(REASONS_SECTION, getUserLoggedId())) {
    $reason['reason_type'] = filter_var(trim($_POST['reason_type']), FILTER_SANITIZE_STRING);    
    $reason['title'] = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
    $reason['description'] = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);    
        
    if ($reason['reason_type'] == '' && $reason['title'] == '') {
        $msg = '<div class="alert alert-danger">Please enter reason type and title</div>';
    } else {        
        $msg = '<div class="alert alert-success">Reason added successfully!</div>';
        if (!addReason($reason)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

//Delete Shipping Company
if (isset($_GET['del']) && isUserHavePermission(REASONS_SECTION, getUserLoggedId())) {    
    $reason_id = filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT);
    if (deleteReason($reason_id)) {
        echo "<script>alert('Deleted successfully'); location.href='settings-reasons.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='settings-reasons.php';</script>";
    }
}
$filters = array();
if(isset($_REQUEST['reason_type']) && trim($_REQUEST['reason_type']) <> "") {
    $filters['reason_type'] = filter_var(trim($_REQUEST['reason_type']), FILTER_SANITIZE_STRING);
}
$reasons = getReasons(array("id", "reason_type", "title"), $filters);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Reasons - Admin</title>
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
                        Reasons
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="active">Reasons</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary collapsed-box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add Report Reason</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data"> 
                                        <div class="form-group">
                                            <label for="reason_type">Reason Type</label>
                                            <select id="reason_type" name="reason_type" class="form-control">
                                                <option value="REPORT">Report</option>
                                                <option value="CANCEL">Cancel</option>
                                                <option value="RETURN">Return</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="title">Reason Title</label>
                                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Reason Description</label>
                                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description"></textarea>
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
                                        <table id="reasonsT" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>      
                                                    <th>Type</th>
                                                    <th>Title</th>
                                                    <th width="100px">Action</th>                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($reasons as $reason) {
                                                    ?>
                                                    <tr>                                                    
                                                        <td><?= $reason['reason_type'] ?></td>
                                                        <td><?= $reason['title'] ?></td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <?php if (isUserHavePermission(REASONS_SECTION, getUserLoggedId())) { ?>
                                                                    <a class='btn btn-sm btn-primary' href="<?= $sys['site_url'] . '/admin/settings-reason-edit.php?id=' . $reason['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                                    <a class='btn btn-sm btn-danger' href="<?= $sys['site_url'] . '/admin/settings-reasons.php?del=' . $reason['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><i class="fa fa-trash"></i></a>
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
            $('#reasonsT').dataTable({
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