<?php require_once 'check_login_status.php'; ?>
<?php 
if(!isUserHavePermission(SELLER_SECTION, ADD_PERMISSION)) {
    header("location: listers");
}

$savemsg = "";
if (isset($_POST['save'])) {
    $lister['name'] = filter_var(trim($_POST['lister_name']),FILTER_SANITIZE_STRING);
    $lister['email'] = filter_var(trim($_POST['lister_email']),FILTER_SANITIZE_STRING);
    $lister['mobile'] = filter_var(trim($_POST['lister_mobile']),FILTER_SANITIZE_STRING);
    $lister['phone'] = filter_var(trim($_POST['lister_phone']),FILTER_SANITIZE_STRING);
    $lister['address'] = filter_var(trim($_POST['lister_address']),FILTER_SANITIZE_STRING);
    $lister['city'] = filter_var(trim($_POST['lister_city']),FILTER_SANITIZE_STRING);
    $lister['state'] = filter_var(trim($_POST['lister_state']),FILTER_SANITIZE_STRING);
    $lister['pincode'] = filter_var(trim($_POST['lister_pincode']),FILTER_SANITIZE_STRING);
    $lister['country'] = filter_var(trim($_POST['lister_country']),FILTER_SANITIZE_STRING);
    $lister['website'] = filter_var(trim($_POST['lister_website']),FILTER_SANITIZE_STRING);
    $lister['username'] = filter_var(trim($_POST['lister_username']),FILTER_SANITIZE_STRING);
    $lister['password'] = trim($_POST['lister_password']);        
    $lister['status'] = trim($_POST['active']);
    
    if(trim($lister['name']) == "" || trim($lister['username']) == "" || trim($lister['password']) == "") {
        $savemsg = '<div class="alert alert-danger">Lister name, username and password required!</div>';
    } else {
        if (Sys_addLister($lister)) {
            $savemsg = '<div class="alert alert-success">Lister added successfully!</div>';
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
        <title>Add Lister - Admin</title>
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
                        Add Lister
                        <small>Add lister</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?php echo $sys['config']['site_url'].'/admin/listers'; ?>">Listers</a></li>
                        <li class="active"><a href="#">Add Lister</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">                    
                    <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Add New Lister</h3>
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
                                                <label>Lister Name*</label>
                                                <input type="text" class="form-control" id="lister_name" name="lister_name"  placeholder="Lister Name" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Email</label>
                                                <input type="text" class="form-control" id="lister_email" name="lister_email"  placeholder="Lister Email"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Mobile</label>
                                                <input type="text" class="form-control" id="lister_mobile" name="lister_mobile"  placeholder="Lister Mobile"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Phone</label>
                                                <input type="text" class="form-control" id="lister_phone" name="lister_phone"  placeholder="Lister Phone"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">                                              
                                                <label>Address</label>
                                                <input type="text" class="form-control" id="lister_address" name="lister_address"  placeholder="Lister Address"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>City</label>
                                                <input type="text" class="form-control" id="lister_city" name="lister_city"  placeholder="Lister City"/>
                                            </div>
                                        </div>                                        
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>State</label>
                                                <input type="text" class="form-control" id="lister_state" name="lister_state"  placeholder="Lister State"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>Pincode</label>
                                                <input type="text" class="form-control" id="lister_pincode" name="lister_pincode"  placeholder="Pincode"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Country</label>
                                                <input type="text" class="form-control" id="lister_country" name="lister_country"  placeholder="Lister Country"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Website</label>
                                                <input type="text" class="form-control" id="lister_website" name="lister_website"  placeholder="Website"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Username*</label>
                                                <input type="text" class="form-control" id="lister_username" name="lister_username"  placeholder="Lister Username" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Password*</label>
                                                <input type="password" class="form-control" id="lister_password" name="lister_password"  placeholder="Lister Password" required/>
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