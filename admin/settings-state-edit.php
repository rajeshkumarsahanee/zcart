<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(STATE_MANAGEMENT_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Update Country
if (isset($_POST['id']) && isset($_POST['name']) && isUserHavePermission(STATE_MANAGEMENT_SECTION, getUserLoggedId())) {
    $state['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);    
    $state['country_id'] = filter_var(trim($_POST['country_id']), FILTER_SANITIZE_STRING);
    $state['zone_id'] = filter_var(trim($_POST['zone_id']), FILTER_SANITIZE_STRING);
    $state['code'] = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING); 
    $state['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $state['status'] = 'A';
        
    if ($state['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter state name</div>';
    } else {        
        $msg = '<div class="alert alert-success">State updated successfully!</div>';
        if (!updateState($state)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

$id = filter_var(trim($_REQUEST['id']), FILTER_SANITIZE_NUMBER_INT);
/*add filters if required*/
$state = getState($id);
if($state == null) {
    header("location: settings-states.php");
    exit();
}
$countries = getCountries(array("id", "name"), array(), 0, -1);
$zones = getZones(array("id", "name"), array(), 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit State - Admin</title>
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
                        Edit State
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="">State Management</li>
                        <li class="active">Edit State</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="box box-primary">
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data"> 
                                        <input type="hidden" name="id" value="<?= $state['id'] ?>"/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="country_id">Country*</label>
                                                    <select class="form-control" name="country_id" id="country_id" required>
                                                        <option value="">Select</option>
                                                        <?php foreach($countries as $c) { ?>
                                                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $state['country_id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="zone_id">Zone*</label>
                                                    <select class="form-control" name="zone_id" id="zone_id" required>
                                                        <option value="">Select</option>
                                                        <?php foreach($zones as $z) { ?>
                                                        <option value="<?= $z['id'] ?>" <?= $z['id'] == $state['zone_id'] ? 'selected' : '' ?>><?= $z['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="code">Code</label>
                                                    <input type="text" class="form-control" name="code" value="<?= $state['code'] ?>" id="code" placeholder="State Code"/>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Name*</label>
                                                    <input type="text" class="form-control" name="name" value="<?= $state['name'] ?>" id="name" placeholder="State Name" required>
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