<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
if (!isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {
    header("location: users.php");
}

$savemsg = "";
//Add User
if (isset($_POST['save']) && isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {
    $user['email'] = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
    $user['username'] = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $user['password'] = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
    $user['display_name'] = isset($_POST['display_name']) ? filter_var(trim($_POST['display_name']), FILTER_SANITIZE_STRING) : $user['username'];
    $user['url'] = filter_var(trim($_POST['url']), FILTER_SANITIZE_STRING);    
    $user['status'] = "A";
    
    $meta['role'] = isset($_POST['role']) ? filter_var(trim($_POST['role']), FILTER_SANITIZE_STRING) : "contributer";
    $meta['first_name'] = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
    $meta['last_name'] = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
    $meta['permissions'] = implode(",", $_POST['permissions']);
    $user['metas'] = $meta;    
    
    if (addUser($user)) {        
        $savemsg = '<div class="alert alert-success">Added Successfully</div>';
        if(isset($_POST['send_user_notification'])) {
            //send notification email            
            $data['from_email'] = secure($sys['admin_email']);
            $data['from_name'] = "ZCMS";
            $data['to_email'] = $user['email'];
            $data['to_name'] = $user['display_name'];
            $data['charSet'] = "";
            $data['is_html'] = true;        
            $data['subject'] = "Your Account Created";
            $data['message_body'] = "Your account has been created on " . $sys['site_name'];
            sendMessage($data);
        }
    } else {        
        $savemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Add New User - Admin</title>
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
                        Add User
                        <small>Create a brand new user and add them to this site.</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?= $sys['site_url']; ?>/admin"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active"><a href="<?= $sys['site_url'] ?>/admin/users.php">Users</a></li>
                        <li class="active"><a href="#">Add New</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Add New</h3>                            
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="" method="post">
                            <div class="box-body">
                                <input type="hidden" name="active" id="activeid" value="1"/>                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Username"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label for="email">Email address</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Email"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label for="url">Website</label>
                                            <input type="text" class="form-control" id="url" name="url" placeholder="Website"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">                                                                           
                                    <div class="col-md-6">
                                        <div class="form-group"> 
                                            <label for="first_name">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name"/>
                                        </div>                                        
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name"/>
                                        </div>                                            
                                    </div>
                                </div>                                                                                                                                                                            
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="display_name">Display Name</label>
                                            <input type="text" class="form-control" id="display_name" name="display_name" placeholder="Display Name"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="send_user_notification">Send User Notification</label>
                                            <div class="checkbox" style="margin-top: auto;">
                                                <label>
                                                    <input type="checkbox" class="" id="send_user_notification" name="send_user_notification" checked/> 
                                                    Send the new user an email about their account.
                                                </label>
                                            </div>                                                                                        
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">                                        
                                            <label for="role">Role</label>
                                            <select id="role" name="role" class="form-control">
                                                <?php foreach($sys['roles'] as $key => $value) { ?>
                                                <option value="<?= $key ?>"><?= $value ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label>Permissions</label><br/>
                                            <input name="permissions[]" id="permissions[]" value="<?= DASHBOARD_SECTION ?>" title="Permissions" type="checkbox"> Dashboard
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_POSTS_SECTION ?>" title="Permissions" type="checkbox"> Manage Posts
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_MEDIA_SECTION ?>" title="Permissions" type="checkbox"> Manage Media
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_PAGES_SECTION ?>" title="Permissions" type="checkbox"> Manage Pages
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_COMMENTS_SECTION ?>" title="Permissions" type="checkbox"> Manage Comments
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_APPEARANCE_SECTION ?>" title="Permissions" type="checkbox"> Manage Appearance
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_USERS_SECTION ?>" title="Permissions" type="checkbox"> Manage Users
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_SETTINGS_SECTION ?>" title="Permissions" type="checkbox"> Manage Settings
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <?php echo $savemsg; ?>
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
            $("#role").change(function(){               
                switch($(this).val()) {
                    case "administrator": 
                        $("input:checkbox").not(this).prop("checked", true);
                        break;
                    case "editor":
                        var permissions = [];
                        permissions.push("<?= DASHBOARD_SECTION ?>");
                        permissions.push("<?= MANAGE_POSTS_SECTION ?>");
                        permissions.push("<?= MANAGE_MEDIA_SECTION ?>");
                        permissions.push("<?= MANAGE_PAGES_SECTION ?>");
                        permissions.push("<?= MANAGE_COMMENTS_SECTION ?>");
                        permissions.push("<?= MANAGE_SETTINGS_SECTION ?>");
                        
                        $('input[type=checkbox]').each(function () {                            
                            if($.inArray($(this).val(), permissions) !== -1) {
                                $(this).prop("checked", true)
                            } else {
                                $(this).prop("checked", false)
                            }
                        });
                        break;
                    case "author":
                        var permissions = [];
                        permissions.push("<?= DASHBOARD_SECTION ?>");
                        permissions.push("<?= MANAGE_POSTS_SECTION ?>");
                        permissions.push("<?= MANAGE_MEDIA_SECTION ?>");
                        permissions.push("<?= MANAGE_PAGES_SECTION ?>");
                        permissions.push("<?= MANAGE_COMMENTS_SECTION ?>");                        
                        
                        $('input[type=checkbox]').each(function () {                            
                            if($.inArray($(this).val(), permissions) !== -1) {
                                $(this).prop("checked", true)
                            } else {
                                $(this).prop("checked", false)
                            }
                        });
                        break;
                    case "contributer":
                        var permissions = [];
                        permissions.push("<?= DASHBOARD_SECTION ?>");
                        permissions.push("<?= MANAGE_POSTS_SECTION ?>");                                                                        
                        
                        $('input[type=checkbox]').each(function () {                            
                            if($.inArray($(this).val(), permissions) !== -1) {
                                $(this).prop("checked", true)
                            } else {
                                $(this).prop("checked", false)
                            }
                        });
                        break;
                    case "subscriber":
                        var permissions = [];
                        permissions.push("<?= DASHBOARD_SECTION ?>");                        
                        
                        $('input[type=checkbox]').each(function () {                            
                            if($.inArray($(this).val(), permissions) !== -1) {
                                $(this).prop("checked", true)
                            } else {
                                $(this).prop("checked", false)
                            }
                        });
                        break;
                }                
            });
            
            $("#role").trigger("change");
        </script>
    </body>
</html>