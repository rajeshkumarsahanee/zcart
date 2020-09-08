<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
$updatemsg = "";
//Update User
if (isset($_POST['update'])) {
    $user['id'] = getUserLoggedId();
    $tmpuser = getUser(getUserLoggedId());
    $user['username'] = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $user['password'] = trim($_POST['password']) <> "" ? trim($_POST['password']) : '';
    $user['display_name'] = filter_var(trim($_POST['display_name']), FILTER_SANITIZE_STRING);
    $user['url'] = filter_var(trim($_POST['url']), FILTER_SANITIZE_STRING);
    $user['email'] = filter_var(trim($_POST['email']), FILTER_SANITIZE_STRING);    
    $user['status'] = isset($_POST['status']) ? trim($_POST['status']) : $tmpuser['status'];
    
    $meta['first_name'] = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
    $meta['last_name'] = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);    
    $meta['show_toolbar'] = isset($_POST['show_toolbar']) ? filter_var(trim($_POST['show_toolbar']), FILTER_SANITIZE_STRING) : "0";
    $meta['googleplus'] = filter_var(trim($_POST['googleplus']), FILTER_SANITIZE_STRING);
    $meta['twitter'] = filter_var(trim($_POST['twitter']), FILTER_SANITIZE_STRING);
    $meta['facebook'] = filter_var(trim($_POST['facebook']), FILTER_SANITIZE_STRING);
    $meta['about'] = filter_var(trim($_POST['about']), FILTER_SANITIZE_STRING);
    $meta['photo'] = $tmpuser['metas']['photo'];
    $user['metas'] = $meta;
    

    if (updateUser($user)) {
        $updatemsg .= '<div class="alert alert-success">Updated Successfully</div>';
    } else {
        $updatemsg .= '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

if (isUserLogged()) {
    $user = getUser(getUserLoggedId());
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
        <title>Profile - Admin</title>
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
                        Profile
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active"><a href="users.php">Users</a></li>
                        <li class="active"><a href="#">Your Profile</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="box box-primary">
                                <div class="box-body box-profile">
                                    <img class="profile-user-img img-responsive img-circle" src="<?= isset($user['metas']['photo']) && !empty($user['metas']['photo']) ? $sys['site_url'] . "/" . $user['metas']['photo'] : "https://placehold.it/96x96" ?>" alt="User profile picture">
                                    <h3 class="profile-username text-center"><?= $user['display_name'] ?></h3>
                                    <p class="text-muted text-center"><?= $user['username'] ?></p>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                        <div class="col-md-9">
                            <form role="form" action="" method="post">
                                <table class="table">                                    
                                    <tr>
                                        <th><label for="username">Username</label></th>
                                        <td>
                                            <input type="text" class="form-control" id="username" name="username" value="<?= $user['username']; ?>" placeholder="Username" readonly/>
                                             Usernames cannot be changed.
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="show_toolbar">Toolbar</label></th>
                                        <td>
                                            <div class="checkbox" style="margin-top: auto;">
                                                <label>
                                                    <input type="checkbox" class="" id="show_toolbar" name="show_toolbar" value="1" <?= isset($user['metas']['show_toolbar']) && trim($user['metas']['show_toolbar']) == "1" ? "checked" : "" ?>/> 
                                                    Show Toolbar when viewing site
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="first_name">First Name</label></th>
                                        <td><input type="text" class="form-control" id="first_name" name="first_name" value="<?= isset($user['metas']['first_name']) ? $user['metas']['first_name'] : "" ?>" placeholder="First Name"/></td>
                                    </tr>
                                    <tr>
                                        <th><label for="last_name">Last Name</label></th>
                                        <td><input type="text" class="form-control" id="last_name" name="last_name" value="<?= isset($user['metas']['last_name']) ? $user['metas']['last_name'] : "" ?>" placeholder="Last Name"/></td>
                                    </tr>
                                    <tr>
                                        <th><label for="display_name">Display Name</label></th>
                                        <td><input type="text" class="form-control" id="displayname" name="display_name" value="<?= $user['display_name']; ?>" placeholder="Display Name"/></td>
                                    </tr>
                                    <tr>
                                        <th><label for="email">Email address</label></th>
                                        <td><input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>"  placeholder="Email"/></td>
                                    </tr>
                                    <tr>
                                        <th><label for="url">Website</label></th>
                                        <td><input type="text" class="form-control" id="url" name="url" value="<?= $user['url'] ?>"  placeholder="Website"/></td>
                                    </tr>
                                    <tr>
                                        <th><label for="googleplus">Google+</label></th>
                                        <td><input type="googleplus" class="form-control" id="googleplus" name="googleplus" value="<?= isset($user['metas']['googleplus']) ? $user['metas']['googleplus'] : "" ?>"  placeholder="Url"/></td>
                                    </tr>
                                    <tr>
                                        <th><label for="twitter">Twitter</label></th>
                                        <td><input type="twitter" class="form-control" id="twitter" name="twitter" value="<?= isset($user['metas']['twitter']) ? $user['metas']['twitter'] : "" ?>"  placeholder="Twitter Url"/></td>
                                    </tr>
                                    <tr>
                                        <th><label for="facebook">Facebook</label></th>
                                        <td><input type="facebook" class="form-control" id="facebook" name="facebook" value="<?= isset($user['metas']['facebook']) ? $user['metas']['facebook'] : "" ?>"  placeholder="Facebook Url"/></td>
                                    </tr>
                                    <tr>
                                        <th><label for="about">About Yourself</label></th>
                                        <td><textarea class="form-control" id="about" name="about" placeholder="About"><?= isset($user['metas']['about']) ? $user['metas']['about'] : "" ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <th><label for="photo">Profile Picture</label></th>
                                        <td>
                                            <img id="photo" src="<?= isset($user['metas']['photo']) && !empty($user['metas']['photo']) ? $sys['site_url'] . "/" . $user['metas']['photo'] : "https://placehold.it/96x96" ?>" class="img-responsive"/>
                                            <input type="file" id="photoinput" style="position: absolute;margin-top: -96px;height: 96px;width: 96px;cursor: pointer;opacity: 0;">
                                            <span id="upload_status"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="password">Password</label></th>
                                        <td>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Password"/>
                                            Leave this field blank if you don't want to change password
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="session">Session</label></th>
                                        <td>
                                            <a href="" class="btn btn-default">Log Out Everywhere Else</a><br/>
                                            Did you lose your phone or leave your account logged in at a public computer? You can log out everywhere else, and stay logged in here. 
                                        </td>
                                    </tr>
                                </table>                                 

                                <?php echo $updatemsg; ?>
                                <button type="submit" class="btn btn-success" name="update">Update</button>
                            </form>
                        </div>
                    </div>                                        
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?> 
            <?php include 'right_sidebar.php'; ?>

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>             
        <script>
             $("#photoinput").change(function (e) {
                    e.preventDefault();
                    var action = "<?= $sys['site_url'] ?>/requests.php?action=uploadPhoto";
                    if ($("#photoinput").val() === "") {
                        return;
                    }
                    $("#upload_status").html("Uploading...");
                    var data = new FormData();
                    data.append('photo', $('input[type=file]')[0].files[0]);
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