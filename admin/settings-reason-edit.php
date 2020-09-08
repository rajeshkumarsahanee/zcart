<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(REASONS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Update Reason
if (isset($_POST['reason_type']) && isset($_POST['title']) && isUserHavePermission(REASONS_SECTION, getUserLoggedId())) {
    $reason['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $reason['reason_type'] = filter_var(trim($_POST['reason_type']), FILTER_SANITIZE_STRING);
    $reason['title'] = filter_var(trim($_POST['title']), FILTER_SANITIZE_STRING);
    $reason['description'] = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);    
        
    if ($reason['reason_type'] == '' && $reason['title'] == '') {
        $msg = '<div class="alert alert-danger">Please enter reason type and title</div>';
    } else {        
        $msg = '<div class="alert alert-success">Reason updated successfully!</div>';
        if (!updateReason($reason)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

$id = filter_var(trim($_REQUEST['id']), FILTER_SANITIZE_NUMBER_INT);
$reason = getReason($id);

if($reason == null) {
    header("location: settings-reasons.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Reasons - Admin</title>
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
                        Edit Reason
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="">Reasons</li>
                        <li class="active">Edit Reason</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data"> 
                                        <input type="hidden" name="id" value="<?= $reason['id'] ?>"/>
                                        <div class="form-group">
                                            <label for="reason_type">Reason Type</label>
                                            <select id="reason_type" name="reason_type" class="form-control">
                                                <option value="REPORT" <?= $reason['reason_type'] == 'REPORT' ? 'selected' : '' ?>>Report</option>
                                                <option value="CANCEL" <?= $reason['reason_type'] == 'CANCEL' ? 'selected' : '' ?>>Cancel</option>
                                                <option value="RETURN" <?= $reason['reason_type'] == 'RETURN' ? 'selected' : '' ?>>Return</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="title">Reason Title</label>
                                            <input type="text" class="form-control" name="title" value="<?= $reason['title'] ?>" id="title" placeholder="Enter Title" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Reason Description</label>
                                            <textarea class="form-control" name="description" id="description" placeholder="Enter Description"><?= $reason['description'] ?></textarea>
                                        </div>                                                                                  
                                        <hr>
                                        <?= $msg ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
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