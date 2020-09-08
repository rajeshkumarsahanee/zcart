<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php 
//Not authorized to access
if (!isUserHavePermission(MANAGE_APPEARANCE_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

$msg = "";
if(isset($_REQUEST['activate']) && trim($_REQUEST['activate'])) {
    $current_theme = filter_var(trim($_REQUEST['activate']), FILTER_SANITIZE_STRING);
    if(saveConfig("theme", $current_theme)) {
        $sys['theme'] = $current_theme;
        $msg = '<div class="alert alert-success">' . $current_theme . ' Theme Activated</div>';
    }
}
$themes = getThemes();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Themes - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <style>            
            .search {
                font-size: 15px;
                border: solid thin silver;
                padding: 5px 10px;
            }
        </style>
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
                        Themes
                        <small><a href="" class="btn btn-default btn-sm" style="margin: auto 10px 2px 10px;">Add New</a></small>
                        <input type="search" placeholder="Search installed themes..." id="theme-search-input" class="search"/>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Appearance</li>
                        <li class="active">Themes</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <?= $msg ?>
                    <div class="row themes" id="themes">
                        <?php foreach ($themes as $theme) { ?>
                            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <div class="panel">
                                    <div class="panel-body">
                                        <img src="<?= $theme['screenshot'] ?>" class="img-responsive"/>
                                    </div>
                                    <div class="panel-footer">
                                        <b><?= $theme['name'] ?></b>
                                        <?php if ($sys['theme'] != $theme['folder']) { ?>
                                            <a href="themes.php?activate=<?= $theme['folder'] ?>" class="btn btn-default btn-xs pull-right">Activate</a>
                                            <?php
                                        } else {
                                            echo '<span class="pull-right">Activated</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                </section>
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>
            <?php include 'right_sidebar.php'; ?>

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>                
        <script>
            
        </script>
    </body>
</html>