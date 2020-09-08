<?php require_once 'check_login_status.php'; ?>
<?php 
if(!isUserHavePermission(SELLER_SECTION, ADD_PERMISSION)) {
    header("location: sellers");
}

$savemsg = "";
if (isset($_POST['save'])) {
    $seller['name'] = filter_var(trim($_POST['seller_name']),FILTER_SANITIZE_STRING);
    $seller['email'] = filter_var(trim($_POST['seller_email']),FILTER_SANITIZE_STRING);
    $seller['mobile'] = filter_var(trim($_POST['seller_mobile']),FILTER_SANITIZE_STRING);
    $seller['phone'] = filter_var(trim($_POST['seller_phone']),FILTER_SANITIZE_STRING);
    $seller['address'] = filter_var(trim($_POST['seller_address']),FILTER_SANITIZE_STRING);
    $seller['city'] = filter_var(trim($_POST['seller_city']),FILTER_SANITIZE_STRING);
    $seller['state'] = filter_var(trim($_POST['seller_state']),FILTER_SANITIZE_STRING);
    $seller['country'] = filter_var(trim($_POST['seller_country']),FILTER_SANITIZE_STRING);
    $seller['username'] = filter_var(trim($_POST['seller_username']),FILTER_SANITIZE_STRING);
    $seller['password'] = trim($_POST['seller_password']);        
    $seller['status'] = trim($_POST['active']);
    
    if(trim($seller['name']) == "" || trim($seller['username']) == "" || trim($seller['password']) == "") {
        $savemsg = '<div class="alert alert-danger">Seller name, username and password required!</div>';
    } else {
        if (Sys_addSeller($seller)) {
            $savemsg = '<div class="alert alert-success">Seller added successfully!</div>';
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
        <title>Add Seller - Admin</title>
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
                        Add Seller
                        <small>Add seller</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?php echo $sys['config']['site_url'].'/admin/sellers'; ?>">Sellers</a></li>
                        <li class="active"><a href="#">Add Seller</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">                    
                    <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Add New Seller</h3>
                                <div class="btn-group pull-right" data-toggle="btn-toggle">
                                    <button type="button" id="activebid" class="btn btn-default btn-sm active">active</button>
                                    <button type="button" id="inactivebid" class="btn btn-default btn-sm">inactive</button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" action="" method="post">                                
                                <div class="box-body">                                    
                                        <input type="hidden" class="form-control" id="activeid" name="active" value="A"/>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Seller Name*</label>
                                                <input type="text" class="form-control" id="seller_name" name="seller_name"  placeholder="Seller Name" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Email</label>
                                                <input type="text" class="form-control" id="seller_email" name="seller_email"  placeholder="Seller Email"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Mobile</label>
                                                <input type="text" class="form-control" id="seller_mobile" name="seller_mobile"  placeholder="Seller Mobile"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Phone</label>
                                                <input type="text" class="form-control" id="seller_phone" name="seller_phone"  placeholder="Seller Phone"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">                                              
                                                <label>Address</label>
                                                <input type="text" class="form-control" id="seller_address" name="seller_address"  placeholder="Seller Address"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>City</label>
                                                <input type="text" class="form-control" id="seller_city" name="seller_city"  placeholder="Seller City"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>State</label>
                                                <input type="text" class="form-control" id="seller_state" name="seller_state"  placeholder="Seller State"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>Country</label>
                                                <input type="text" class="form-control" id="seller_country" name="seller_country"  placeholder="Seller Country"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Username*</label>
                                                <input type="text" class="form-control" id="seller_username" name="seller_username"  placeholder="Seller Username" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Password*</label>
                                                <input type="password" class="form-control" id="seller_password" name="seller_password"  placeholder="Seller Password" required/>
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