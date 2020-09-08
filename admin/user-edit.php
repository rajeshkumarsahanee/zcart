<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
if (!isset($_REQUEST['id']) || trim($_REQUEST['id']) == '1' || !isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {
    header("location: users.php");
}

$updatemsg = "";
//Update User
if (isset($_POST['update']) && isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {        
    $user['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $tmpuser = getUser($user['id']);
    $user['email'] = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);
    $user['username'] = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $user['password'] = isset($_POST['password']) && trim($_POST['password']) <> "" ? filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING) : '';
    $user['display_name'] = isset($_POST['display_name']) ? filter_var(trim($_POST['display_name']), FILTER_SANITIZE_STRING) : $user['username'];
    $user['url'] = filter_var(trim($_POST['url']), FILTER_SANITIZE_STRING);    
    $user['status'] = isset($_POST['status']) ? filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING) : "A";
    
    $meta['role'] = isset($_POST['role']) ? filter_var(trim($_POST['role']), FILTER_SANITIZE_STRING) : "contributer";
    $meta['first_name'] = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
    $meta['last_name'] = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);    
    $meta['show_toolbar'] = isset($_POST['show_toolbar']) ? filter_var(trim($_POST['show_toolbar']), FILTER_SANITIZE_STRING) : "0";
    $meta['facebook'] = filter_var(trim($_POST['facebook']), FILTER_SANITIZE_STRING);
    $meta['twitter'] = filter_var(trim($_POST['twitter']), FILTER_SANITIZE_STRING);
    $meta['googleplus'] = filter_var(trim($_POST['googleplus']), FILTER_SANITIZE_STRING);    
    $meta['about'] = filter_var(trim($_POST['about']), FILTER_SANITIZE_STRING);
    $meta['photo'] = $tmpuser['metas']['photo'];
    $meta['permissions'] = implode(",", $_POST['permissions']);
    $user['metas'] = $meta; 
    
    if (updateUser($user)) {        
        $updatemsg = '<div class="alert alert-success">Updated Successfully</div>';        
    } else {        
        $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

if (isset($_REQUEST['id']) && trim($_REQUEST['id']) != '') {
    $user = getUser(trim($_REQUEST['id']));
    if ($user == null) {
        header("location: users.php");
    }
} else {
    header("location: users.php");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit User - Admin</title>
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
                        Edit User
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active"><a href="users.php">Users</a></li>
                        <li class="active"><a href="#">Edit User</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Edit User <?= $user['display_name'] ?></h3>
                            <div class="btn-group pull-right" data-toggle="btn-toggle">
                                <button type="button" id="activebid" class="btn btn-default btn-sm <?= $user['status'] == 'A' ? 'active' : "" ?>">active</button>
                                <button type="button" id="inactivebid" class="btn btn-default btn-sm <?= $user['status'] == 'I' ? 'active' : "" ?>">inactive</button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="" method="post">
                            <div class="box-body">
                                <input type="hidden" name="status" id="activeid" value="<?= $user['status'] ?>"/>
                                <input type="hidden" name="id" value="<?= $user['id'] ?>"/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="show_toolbar">Toolbar</label>
                                            <div class="checkbox" style="margin-top: auto;">
                                                <label>
                                                    <input type="checkbox" class="" id="show_toolbar" name="show_toolbar" value="1" <?= isset($user['metas']['show_toolbar']) && trim($user['metas']['show_toolbar']) == "1" ? "checked" : "" ?>/> 
                                                    Show Toolbar when viewing site
                                                </label>
                                            </div>                                                                                        
                                        </div>                                        
                                    </div>
                                    <div class="col-md-6">
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" placeholder="Username" readonly/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password"/>
                                            Leave this field blank if don't want to change password
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label for="email">Email address</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>"  placeholder="Email"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">                                        
                                            <label for="url">Website</label>
                                            <input type="text" class="form-control" id="url" name="url" value="<?= $user['url'] ?>"  placeholder="Website"/>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="row">                                                                           
                                    <div class="col-md-6">
                                        <div class="form-group"> 
                                            <label for="first_name">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= isset($user['metas']['first_name']) ? $user['metas']['first_name'] : "" ?>" placeholder="First Name"/>
                                        </div>                                        
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= isset($user['metas']['last_name']) ? $user['metas']['last_name'] : "" ?>" placeholder="Last Name"/>
                                        </div>                                            
                                    </div>
                                </div>                                                                                                                                        
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="display_name">Display Name</label>
                                            <input type="text" class="form-control" id="display_name" name="display_name" value="<?= $user['display_name'] ?>" placeholder="Display Name"/>
                                        </div>
                                    </div>
                                </div>                                                                    
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">                                        
                                            <label for="role">Role</label>
                                            <select id="role" name="role" class="form-control">
                                                <?php foreach($sys['roles'] as $key => $value) { ?>
                                                <option value="<?= $key ?>" <?= isset($user['metas']['role']) && $user['metas']['role'] == $key ? "selected" : "" ?>><?= $value ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label>Permissions</label><br/>
                                            <input name="permissions[]" id="permissions[]" value="<?= DASHBOARD_SECTION ?>" title="Permissions" type="checkbox" <?= isUserHavePermission(DASHBOARD_SECTION, $user['id']) ? "checked" : "" ?>> Dashboard
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_POSTS_SECTION ?>" title="Permissions" type="checkbox" <?= isUserHavePermission(MANAGE_POSTS_SECTION, $user['id']) ? "checked" : "" ?>> Manage Posts
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_MEDIA_SECTION ?>" title="Permissions" type="checkbox" <?= isUserHavePermission(MANAGE_MEDIA_SECTION, $user['id']) ? "checked" : "" ?>> Manage Media
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_PAGES_SECTION ?>" title="Permissions" type="checkbox" <?= isUserHavePermission(MANAGE_PAGES_SECTION, $user['id']) ? "checked" : "" ?>> Manage Pages
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_COMMENTS_SECTION ?>" title="Permissions" type="checkbox" <?= isUserHavePermission(MANAGE_COMMENTS_SECTION, $user['id']) ? "checked" : "" ?>> Manage Comments
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_APPEARANCE_SECTION ?>" title="Permissions" type="checkbox" <?= isUserHavePermission(MANAGE_APPEARANCE_SECTION, $user['id']) ? "checked" : "" ?>> Manage Appearance
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_USERS_SECTION ?>" title="Permissions" type="checkbox" <?= isUserHavePermission(MANAGE_USERS_SECTION, $user['id']) ? "checked" : "" ?>> Manage Users
                                            <input name="permissions[]" id="permissions[]" value="<?= MANAGE_SETTINGS_SECTION ?>" title="Permissions" type="checkbox" <?= isUserHavePermission(MANAGE_SETTINGS_SECTION, $user['id']) ? "checked" : "" ?>> Manage Settings
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">                                        
                                        <div class="form-group">
                                            <label for="googleplus">About</label>
                                            <textarea class="form-control" id="about" name="about" placeholder="Biographical Info"><?= isset($user['metas']['about']) ? $user['metas']['about'] : "" ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <img id="photo" src="<?= isset($user['metas']['photo']) && !empty($user['metas']['photo']) ? $sys['site_url'] . "/" . $user['metas']['photo'] : "https://placehold.it/96x96" ?>" class="img-responsive"/>
                                        <input type="file" id="photoinput" style="position: absolute;margin-top: -96px;height: 96px;width: 96px;cursor: pointer;opacity: 0;">
                                        <span id="upload_status"></span>
                                    </div>
                                </div>                                
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="facebook">Facebook</label>
                                            <input type="text" class="form-control" id="facebook" name="facebook" value="<?= isset($user['metas']['facebook']) ? $user['metas']['facebook'] : "" ?>" placeholder="Facebook Url"/>
                                        </div>                                        
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="twitter">Twitter</label>
                                            <input type="text" class="form-control" id="facebook" name="twitter" value="<?= isset($user['metas']['twitter']) ? $user['metas']['twitter'] : "" ?>" placeholder="Twitter Url"/>
                                        </div>
                                    </div>
                                    <div class="col-md-10">                                        
                                        <div class="form-group">
                                            <label for="googleplus">Google+</label>
                                            <input type="text" class="form-control" id="googleplus" name="googleplus" value="<?= isset($user['metas']['googleplus']) ? $user['metas']['googleplus'] : "" ?>" placeholder="Google+ Url"/>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <?php echo $updatemsg; ?>
                                <button type="submit" class="btn btn-primary" name="update">Update</button>
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
            $("#activebid").click(function(){
               $("#activeid").val("A"); 
            });
            
            $("#inactivebid").click(function(){
               $("#activeid").val("I"); 
            });
                        
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
            
            $("#photoinput").change(function (e) {
                    e.preventDefault();
                    var action = "<?= $sys['site_url'] ?>/requests.php?action=uploadPhoto";
                    if ($("#photoinput").val() === "") {
                        return;
                    }
                    $("#upload_status").html("Uploading...");
                    var data = new FormData();
                    data.append('photo', $('input[type=file]')[0].files[0]);
                    data.append('user_id', '<?= $user['id'] ?>');
                    $.ajax({
                        type: 'POST',
                        url: action,
                        data: data,
                        /*THIS MUST BE DONE FOR FILE UPLOADING*/
                        contentType: false,
                        processData: false,
                    }).done(function (data) {
                        $("#upload_status").html("");
                        if (data.code === '<?= SUCCESS_RESPOSE_CODE ?>') {
                            $("#photo").attr("src", "<?= $sys['site_url'] ?>/" + data.filepath);
                        }
                    }).fail(function (data) {
                        //any message
                    });
                });
        </script>
    </body>
</html>