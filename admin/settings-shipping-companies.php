<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(SHIPPING_COMPANIES_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";

//Add Shipping Company
if (isset($_POST['name']) && isset($_POST['website']) && isUserHavePermission(SHIPPING_COMPANIES_SECTION, getUserLoggedId())) {
    $shippingCompany['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);    
    $shippingCompany['website'] = filter_var(trim($_POST['website']), FILTER_SANITIZE_STRING);
    $shippingCompany['comments'] = filter_var(trim($_POST['comments']), FILTER_SANITIZE_STRING);    
    $shippingCompany['status'] = "A";
        
    
    if ($shippingCompany['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter name</div>';
    } else {        
        $msg = '<div class="alert alert-success">Shipping Company added successfully!</div>';
        if (!addShippingCompany($shippingCompany)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

//Delete Shipping Company
if (isset($_GET['del']) && isUserHavePermission(SHIPPING_COMPANIES_SECTION, Sys_getAdminLoggedId())) {    
    $company_id = filter_var(trim($_GET['del']), FILTER_SANITIZE_NUMBER_INT);
    if (update(T_SHIPPING_COMPANIES, array("status" => "T"), array("id" => $company_id))) {
        echo "<script>alert('Deleted successfully'); location.href='shipping-companies.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='shipping-companies.php';</script>";
    }
}
$shippingCompanies = getShippingCompanies();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Shipping Companies - Admin</title>
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
                        Shipping Companies
                        <small>List of created shipping companies</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li class="active">Shipping Companies</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Add Shipping Company</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">

                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form action="" method="post" enctype="multipart/form-data">                                            
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Website</label>
                                            <input type="text" class="form-control" name="website" id="website" placeholder="Website">
                                        </div>
                                        <div class="form-group">
                                            <label>Comments</label>
                                            <input type="text" class="form-control" name="comments" id="comments" placeholder="Comments">
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
                                        <table id="companiesT" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>                                                
                                                    <th>Name</th>
                                                    <th>Website</th>
                                                    <th width="110px">Action</th>                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($shippingCompanies as $shippingCompany) {
                                                    ?>
                                                    <tr>                                                    
                                                        <td><?= $shippingCompany['name']; ?></td>
                                                        <td><?= $shippingCompany['website']; ?></td>                                                                                                        
                                                        <td>
                                                            <div class='btn-group'>
                                                                <?php if (isUserHavePermission(SHIPPING_COMPANIES_SECTION, getUserLoggedId())) { ?>
                                                                    <a class='btn btn-sm btn-primary' href="<?= $sys['site_url'] . '/admin/settings-shipping-company-edit.php?id=' . $shippingCompany['id']; ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                                    <a class='btn btn-sm btn-danger' href="<?= $sys['site_url'] . '/admin/settings-shipping-companies.php?del=' . $shippingCompany['id']; ?>" onclick="return confirm('Are you sure you want to delete?')" title="Delete"><i class="fa fa-trash"></i></a>
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
            $('#companiesT').dataTable({
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