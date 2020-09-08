<?php
require '../system/init.php';
if (isUserLogged()) {
    header("location: dashboard.php");
    exit();
}

$msg = "";
if (isset($_POST['submit'])) {
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_STRING);
    if (isValidUser($username, $password)) {
        $user = getUser($username);
        if ($user['status'] == "A") {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = isset($user['metas']['role']) ? $user['metas']['role'] : "na";
            $_SESSION['display_name'] = $user['display_name'];
            $_SESSION['registered'] = date("M. Y", strtotime($user['registered']));
            if (isset($_POST['remember'])) {
                setcookie("user_id", $user['id'], time() + 60 * 60 * 24 * 30); //setting cookie for 30 days
            }
            $redirect_to = isset($_REQUEST['redirect_to']) && trim($_REQUEST['redirect_to']) <> "" ? trim($_REQUEST['redirect_to']) : "dashboard.php";
            header("location: " . $redirect_to);         
            exit();
        } else {
            $msg = '<div class="alert alert-danger">You Account Status is ' . $sys['statuses'][$user['status']] . '</div>';
        }
    } else {
        $msg = '<div class="alert alert-danger">Username or Password is incorrect</div>';
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Log in - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link rel="stylesheet" href="<?= $sys['site_url']; ?>/admin/plugins/iCheck/square/blue.css">
    </head>
    <body class="login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="login.php"><b>Admin</b> Panel</a>
            </div><!-- /.login-logo -->
            <div class="login-box-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <form action="" method="post">
                    <div class="form-group has-feedback">
                        <input type="text" name="username" class="form-control" placeholder="Username"/>
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" name="password" class="form-control" placeholder="Password"/>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <?= $msg ?>              
                    <div class="row">
                        <div class="col-xs-8">    
                            <div class="checkbox icheck">
                                <label><input type="checkbox" name="remember" value="1"> Remember Me</label>
                            </div>                        
                        </div><!-- /.col -->
                        <div class="col-xs-4">
                            <button type="submit" name="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                        </div><!-- /.col -->
                    </div>
                </form>       

                <!--<a href="#">I forgot my password</a><br>-->        

            </div><!-- /.login-box-body -->
        </div><!-- /.login-box -->

        <?php include 'script.php'; ?>
        <script src="<?php echo $sys['site_url']; ?>/admin/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });
            });
        </script>
    </body>
</html>