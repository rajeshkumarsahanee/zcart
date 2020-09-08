<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(COUNTRY_MANAGEMENT_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Update Country
if (isset($_POST['id']) && isset($_POST['code']) && isset($_POST['name']) && isUserHavePermission(COUNTRY_MANAGEMENT_SECTION, getUserLoggedId())) {
    $country['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);    
    $country['code'] = filter_var(trim($_POST['code']), FILTER_SANITIZE_STRING);    
    $country['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $country['status'] = 'A';
        
    if ($country['code'] == '' || $country['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter reason code and name</div>';
    } else {        
        $msg = '<div class="alert alert-success">Country updated successfully!</div>';
        if (!updateCountry($country)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

$id = filter_var(trim($_REQUEST['id']), FILTER_SANITIZE_NUMBER_INT);
/*add filters if required*/
$country = getCountry($id);
if($country == null) {
    header("location: settings-countries.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Country - Admin</title>
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
                        Edit Country
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="">Country Management</li>
                        <li class="active">Edit Country</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="box box-primary">
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data"> 
                                        <input type="hidden" name="id" value="<?= $country['id'] ?>"/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="code">Code*</label>
                                                    <input type="text" class="form-control" name="code" value="<?= $country['code'] ?>" id="code" placeholder="Country Code" required>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Name*</label>
                                                    <input type="text" class="form-control" name="name" value="<?= $country['name'] ?>" id="name" placeholder="Country Name" required>
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