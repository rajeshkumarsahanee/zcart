<?php require_once 'check_login_status.php'; ?>
<?php 
if(!isUserHavePermission(SELLER_SECTION, EDIT_PERMISSION) || !isset($_REQUEST['id'])) {
    header("location: listers");
}

$lister = Sys_getLister(trim($_REQUEST['id']));
if($lister == null) {
    echo 'Incorrect lister id! <a href="listers">Go to listers list!</a>';
    die();
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Lister - Admin</title>
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
                        Edit Lister
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class=""><a href="<?php echo $sys['config']['site_url'].'/admin/listers'; ?>">Listers</a></li>
                        <li class="active"><a href="#">Edit Lister</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">                    
                    <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $lister['name'] ?></h3>
                                <div class="btn-group pull-right" data-toggle="btn-toggle">
                                    <button type="button" id="activebid" class="btn btn-default btn-sm <?php if(trim($lister['status']) == 'A') { echo 'active'; } ?>">active</button>
                                    <button type="button" id="inactivebid" class="btn btn-default btn-sm <?php if(trim($lister['status']) == 'I') { echo 'active'; } ?>">inactive</button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <!-- form start -->
                            <form id="suform" role="form" method="post" action="<?php echo $sys['config']['site_url'].'/requests.php?f=lister_update'; ?>">
                                <div class="box-body">                                    
                                        <input type="hidden" class="form-control" id="activeid" name="active" value="<?php echo $lister['status']; ?>"/>
                                        <input id="lister_id" type="hidden" name="lister_id" value="<?php echo $lister['id']; ?>"/>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Lister Name*</label>
                                                <input type="text" class="form-control" id="lister_name" name="lister_name"  placeholder="Lister Name" value="<?php echo $lister['name']; ?>" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Email</label>
                                                <input type="text" class="form-control" id="lister_email" name="lister_email"  placeholder="Lister Email" value="<?php echo $lister['email']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Mobile</label>
                                                <input type="text" class="form-control" id="lister_mobile" name="lister_mobile"  placeholder="Lister Mobile" value="<?php echo $lister['mobile']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Phone</label>
                                                <input type="text" class="form-control" id="lister_phone" name="lister_phone"  placeholder="Lister Phone" value="<?php echo $lister['phone']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">                                              
                                                <label>Address</label>
                                                <input type="text" class="form-control" id="lister_address" name="lister_address"  placeholder="Lister Address" value="<?php echo $lister['address']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>City</label>
                                                <input type="text" class="form-control" id="lister_city" name="lister_city"  placeholder="Lister City" value="<?php echo $lister['city']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>State</label>
                                                <input type="text" class="form-control" id="lister_state" name="lister_state"  placeholder="Lister State" value="<?php echo $lister['state']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">                                              
                                                <label>Pincode</label>
                                                <input type="text" class="form-control" id="lister_pincode" name="lister_pincode"  placeholder="Pincode" value="<?php echo $lister['pincode']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Country</label>
                                                <input type="text" class="form-control" id="lister_country" name="lister_country"  placeholder="Lister Country" value="<?php echo $lister['country']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Website</label>
                                                <input type="text" class="form-control" id="lister_website" name="lister_website"  placeholder="Lister Website" value="<?php echo $lister['website']; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Username*</label>
                                                <input type="text" class="form-control" id="lister_username" name="lister_username"  placeholder="Lister Username" value="<?php echo $lister['username']; ?>" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">                                              
                                                <label>Password*</label>
                                                <input type="password" class="form-control" id="lister_password" name="lister_password"  placeholder="Lister Password" value="<?php echo $lister['password']; ?>" required/>
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