<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(COUNTRY_MANAGEMENT_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Add Country
if (isset($_POST['code']) && isset($_POST['name']) && isUserHavePermission(COUNTRY_MANAGEMENT_SECTION, getUserLoggedId())) {
    $country['code'] = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING);    
    $country['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $country['status'] = 'A';
        
    if ($country['code'] == '' || $country['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter reason code and name</div>';
    } else {        
        $msg = '<div class="alert alert-success">Country added successfully!</div>';
        if (!addCountry($country)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

//Delete Country
if (isset($_GET['del']) && isUserHavePermission(COUNTRY_MANAGEMENT_SECTION, getUserLoggedId())) {    
    $id = filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT);
    if (deleteCountry($id)) {
        echo "<script>alert('Deleted successfully'); location.href='settings-countries.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='settings-countries.php';</script>";
    }
}
$filters = array();
/*add filters if required*/
$countries = getCountries(array("id", "code", "name"), $filters, 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Countries - Admin</title>
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
                        Countries
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="active">Country Management</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add New Country</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data"> 
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="code">Code*</label>
                                                    <input type="text" class="form-control" name="code" id="code" placeholder="Country Code" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Name*</label>
                                                    <input type="text" class="form-control" name="name" id="name" placeholder="Country Name" required>
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
                                        <table id="countriesT" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>  
                                                    <th>S.No.</th>
                                                    <th>Code</th>
                                                    <th>Name</th>
                                                    <th width="100px">Action</th>                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($countries as $c) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $i++ ?></td>
                                                        <td><?= $c['code'] ?></td>
                                                        <td><?= $c['name'] ?></td>
                                                        <td>
                                                            <div class='btn-group'>
                                                                <?php if (isUserHavePermission(COUNTRY_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                                                                    <a class='btn btn-sm btn-primary' href="<?= $sys['site_url'] . '/admin/settings-country-edit.php?id=' . $c['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                                    <a class='btn btn-sm btn-danger' href="<?= $sys['site_url'] . '/admin/settings-countries.php?del=' . $c['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><i class="fa fa-trash"></i></a>
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
            $('#countriesT').dataTable({
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