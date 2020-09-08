<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(STATE_MANAGEMENT_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Add State
if (isset($_POST['code']) && isset($_POST['name']) && isUserHavePermission(STATE_MANAGEMENT_SECTION, getUserLoggedId())) {
    $state['country_id'] = filter_var(trim($_POST['country_id']), FILTER_SANITIZE_STRING);
    $state['zone_id'] = filter_var(trim($_POST['zone_id']), FILTER_SANITIZE_STRING);
    $state['code'] = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING); 
    $state['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $state['status'] = 'A';
        
    if ($state['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter state name</div>';
    } else {        
        $msg = '<div class="alert alert-success">State added successfully!</div>';
        if (!addState($state)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

//Delete Country
if (isset($_GET['del']) && isUserHavePermission(STATE_MANAGEMENT_SECTION, getUserLoggedId())) {    
    $id = filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT);
    if (deleteState($id)) {
        echo "<script>alert('Deleted successfully'); location.href='settings-states.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='settings-states.php';</script>";
    }
}
$filters = array();
/*add filters if required*/
if(isset($_REQUEST['country_id']) && trim($_REQUEST['country_id']) <> "") {
    $filters['country_id'] = filter_var(trim($_REQUEST['country_id']), FILTER_SANITIZE_NUMBER_INT);
}
$states = getStates(array("id","country_id", "zone_id", "code", "name"), $filters, 0, -1);
$tmpcountries = getCountries(array("id", "name"), array(), 0, -1);
foreach($tmpcountries as $c) {
    $countries[$c['id']] = $c;
}
$tmpzones = getZones(array("id", "name"), array(), 0, -1);
foreach($tmpzones as $z) {
    $zones[$z['id']] = $z;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>States - Admin</title>
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
                        States
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="active">State Management</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add New State</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data"> 
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="country_id">Country*</label>
                                                    <select class="form-control" name="country_id" id="country_id" required>
                                                        <option value="">Select</option>
                                                        <?php foreach($countries as $c) { ?>
                                                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
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
                                                        <option value="<?= $z['id'] ?>"><?= $z['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="code">Code</label>
                                                    <input type="text" class="form-control" name="code" id="code" placeholder="State Code"/>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Name*</label>
                                                    <input type="text" class="form-control" name="name" id="name" placeholder="State Name" required>
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
                        <div class="col-md-8">
                            <div class="box">
                                <div class="box-body">                                                                        
                                    <div class="">
                                        <table id="statesT" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>  
                                                    <th>S.No.</th>
                                                    <th>Name</th>
                                                    <th>Zone</th>
                                                    <th>Country</th> 
                                                    <th width="100px">Action</th>                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($states as $s) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $i++ ?></td>
                                                        <td><?= $s['name'] ?></td>
                                                        <td><?= isset($zones[$s['zone_id']]) ? $zones[$s['zone_id']]['name'] : ''  ?></td>
                                                        <td><?= isset($countries[$s['country_id']]) ? $countries[$s['country_id']]['name'] : ''  ?></td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <?php if (isUserHavePermission(COUNTRY_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                                                                    <a class='btn btn-sm btn-primary' href="<?= $sys['site_url'] . '/admin/settings-state-edit.php?id=' . $c['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                                    <a class='btn btn-sm btn-danger' href="<?= $sys['site_url'] . '/admin/settings-states.php?del=' . $c['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><i class="fa fa-trash"></i></a>
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
            $('#statesT').dataTable({
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