<?php require_once 'check_login_status.php'; ?>
<?php 
if(!isUserHavePermission(SELLER_SECTION, EDIT_PERMISSION) || !isset($_REQUEST['id'])) {
    header("location: sellers");
}

$seller = Sys_getSeller(trim($_REQUEST['id']));
if($seller == null) {
    echo 'Incorrect seller id! <a href="sellers">Go to sellers list!</a>';
    die();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Seller - Admin</title>
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
                        Edit Seller
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?php echo $sys['config']['site_url'].'/admin/sellers'; ?>">Sellers</a></li>
                        <li class="active"><a href="#">Edit Seller</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">                    
                    <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $seller['name'] ?></h3>
                                <div class="btn-group pull-right" data-toggle="btn-toggle">
                                    <button type="button" id="activebid" class="btn btn-default btn-sm <?php if(trim($seller['status']) == 'A') { echo 'active'; } ?>">active</button>
                                    <button type="button" id="inactivebid" class="btn btn-default btn-sm <?php if(trim($seller['status']) == 'I') { echo 'active'; } ?>">inactive</button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form id="suform" role="form" method="post" action="<?php echo $sys['config']['site_url'].'/requests.php?f=seller_update'; ?>">
                                <div class="box-body">                                    
                                        <input type="hidden" class="form-control" id="activeid" name="active" value="<?php echo $seller['status']; ?>"/>
                                        <input id="seller_id" type="hidden" name="seller_id" value="<?php echo $seller['id']; ?>"/>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Seller Name*</label>
                                                <input type="text" class="form-control" id="seller_name" name="seller_name"  placeholder="Seller Name" value="<?php echo $seller['name']; ?>" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Email</label>
                                                <input type="text" class="form-control" id="seller_email" name="seller_email"  placeholder="Seller Email" value="<?php echo $seller['email']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Mobile</label>
                                                <input type="text" class="form-control" id="seller_mobile" name="seller_mobile"  placeholder="Seller Mobile" value="<?php echo $seller['mobile']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Phone</label>
                                                <input type="text" class="form-control" id="seller_phone" name="seller_phone"  placeholder="Seller Phone" value="<?php echo $seller['phone']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">                                              
                                                <label>Address</label>
                                                <input type="text" class="form-control" id="seller_address" name="seller_address"  placeholder="Seller Address" value="<?php echo $seller['address']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>City</label>
                                                <input type="text" class="form-control" id="seller_city" name="seller_city"  placeholder="Seller City" value="<?php echo $seller['city']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>State</label>
                                                <input type="text" class="form-control" id="seller_state" name="seller_state"  placeholder="Seller State" value="<?php echo $seller['state']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>Country</label>
                                                <input type="text" class="form-control" id="seller_country" name="seller_country"  placeholder="Seller Country" value="<?php echo $seller['country']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Username*</label>
                                                <input type="text" class="form-control" id="seller_username" name="seller_username"  placeholder="Seller Username" value="<?php echo $seller['username']; ?>" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Password*</label>
                                                <input type="password" class="form-control" id="seller_password" name="seller_password"  placeholder="Seller Password" value="<?php echo $seller['password']; ?>" required/>
                                            </div>
                                        </div>                                        
                                    </div>                                
                                <!-- /.box-body -->
                                <div id="msg"></div>
                                <div class="box-footer">                                    
                                    <input type="submit" class="btn btn-primary" name="update" value="Update"/>
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
            
            $(function(){
                $("#suform").submit(function(e){                  
                    e.preventDefault();
                    var action = $(this).attr('action');
                    var data = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: action,
                        data: data
                    }).done(function(data){
                        $("#msg").html(data);
                    }).fail(function(data){
                        //any message
                    });                
                });
            });
        </script>
    </body>
</html>