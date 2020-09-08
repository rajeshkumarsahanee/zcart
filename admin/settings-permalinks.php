<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(MANAGE_SETTINGS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$updatemsg = "";
if (isset($_POST['update']) && isUserHavePermission(MANAGE_SETTINGS_SECTION, getUserLoggedId())) {
    $settings['permalink_structure'] = filter_var(trim($_POST['permalink_structure']), FILTER_SANITIZE_STRING);
    $settings['category_base'] = filter_var(trim($_POST['category_base']), FILTER_SANITIZE_STRING);
    $settings['tag_base'] = filter_var(trim($_POST['tag_base']), FILTER_SANITIZE_STRING);    

    if (saveAllConfig($settings) && updateRewriteRules()) {
        $updatemsg = '<div class="alert alert-success">Settings saved successfully!</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">There is some problem!</div>';
    }
}

$config = getConfig();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Permalinks Settings - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <style>
            .list {
                list-style: none;
                padding-left: 0px
            }
            .list li {
                float: left;
                margin: 1px;
            }            
            .custom-table th, td {
                border-top: none !important;
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
                        Settings
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li><a href="#">Settings</a></li>
                        <li class="active">Permalinks Settings</li>
                    </ol>
                </section>
                <section class="content">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Permalinks</h3>                            
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="" method="post">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p>
                                            ZCMS offers you the ability to create a custom URL structure for your permalinks and archives. 
                                            Custom URL structures can improve the aesthetics, usability, and forward-compatibility of your links.
                                        </p>
                                        <h4 class="title">Common Settings</h4>
                                        <table class="table custom-table">
                                            <tbody>
                                                <tr>
                                                    <th><label><input name="selection" type="radio" value="" <?= isset($config['permalink_structure']) && $config['permalink_structure'] == "" ? "checked" : "" ?>/> Plain</label></th>
                                                    <td><code><?= $sys['site_url'] ?>/?p=123</code></td>
                                                </tr>
                                                <tr>
                                                    <th><label><input name="selection" type="radio" value="/%year%/%monthnum%/%day%/%postname%/" <?= isset($config['permalink_structure']) && $config['permalink_structure'] == "/%year%/%monthnum%/%day%/%postname%/" ? "checked" : "" ?>/> Day and name</label></th>
                                                    <td><code><?= $sys['site_url'] ?>/2019/01/14/sample-post/</code></td>
                                                </tr>
                                                <tr>
                                                    <th><label><input name="selection" type="radio" value="/%year%/%monthnum%/%postname%/" <?= isset($config['permalink_structure']) && $config['permalink_structure'] == "/%year%/%monthnum%/%postname%/" ? "checked" : "" ?>/> Month and name</label></th>
                                                    <td><code><?= $sys['site_url'] ?>/2019/01/sample-post/</code></td>
                                                </tr>
                                                <tr>
                                                    <th><label><input name="selection" type="radio" value="/archives/%post_id%" <?= isset($config['permalink_structure']) && $config['permalink_structure'] == "/archives/%post_id%" ? "checked" : "" ?>/> Numeric</label></th>
                                                    <td><code><?= $sys['site_url'] ?>/archives/123</code></td>
                                                </tr>
                                                <tr>
                                                    <th><label><input name="selection" type="radio" value="/%postname%/" <?= isset($config['permalink_structure']) && $config['permalink_structure'] == "/%postname%/" ? "checked" : "" ?>/> Post name</label></th>
                                                    <td><code><?= $sys['site_url'] ?>/sample-post/</code></td>
                                                </tr>
                                                <tr>
                                                    <th><label><input name="selection" id="custom_selection" type="radio" value="<?= isset($config['permalink_structure']) ? $config['permalink_structure'] : "" ?>"/> Custom Structure</label></th>
                                                    <td>
                                                        <code><?= $sys['site_url'] ?></code>
                                                        <input name="permalink_structure" id="permalink_structure" type="text" value="<?= isset($config['permalink_structure']) ? $config['permalink_structure'] : "" ?>" class="regular-text code"/>
                                                        <div class="available-structure-tags hide-if-no-js">
                                                            <div id="custom_selection_updated" aria-live="assertive" class="screen-reader-text"></div>
                                                            <p>Available tags:</p>
                                                            <ul role="list" class="list">
                                                                <li><button type="button" class="btn btn-sm btn-default <?= strpos($config['permalink_structure'], '%year%') !== false ? "active" : "" ?>">%year%</button></li>
                                                                <li><button type="button" class="btn btn-sm btn-default <?= strpos($config['permalink_structure'], '%monthnum%') !== false ? "active" : "" ?>">%monthnum%</button></li>
                                                                <li><button type="button" class="btn btn-sm btn-default <?= strpos($config['permalink_structure'], '%day%') !== false ? "active" : "" ?>">%day%</button></li>
                                                                <li><button type="button" class="btn btn-sm btn-default <?= strpos($config['permalink_structure'], '%hour%') !== false ? "active" : "" ?>">%hour%</button></li>
                                                                <li><button type="button" class="btn btn-sm btn-default <?= strpos($config['permalink_structure'], '%minute%') !== false ? "active" : "" ?>">%minute%</button></li>
                                                                <li><button type="button" class="btn btn-sm btn-default <?= strpos($config['permalink_structure'], '%second%') !== false ? "active" : "" ?>">%second%</button></li>
                                                                <li><button type="button" class="btn btn-sm btn-default <?= strpos($config['permalink_structure'], '%post_id%') !== false ? "active" : "" ?>">%post_id%</button></li>
                                                                <li><button type="button" class="btn btn-sm btn-default <?= strpos($config['permalink_structure'], '%postname%') !== false ? "active" : "" ?>">%postname%</button></li>
                                                                <li><button type="button" class="btn btn-sm btn-default <?= strpos($config['permalink_structure'], '%category%') !== false ? "active" : "" ?>">%category%</button></li>
                                                                <li><button type="button" class="btn btn-sm btn-default <?= strpos($config['permalink_structure'], '%author%') !== false ? "active" : "" ?>">%author%</button></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="form-group">
                                            <h4 class="title">Optional</h4>
                                            <p>
                                                If you like, you may enter custom structures for your category and tag URLs here. For example, using topics as your category base would make your category links like <?= $sys['site_url'] ?>/topics/uncategorized/.
                                                If you leave these blank the defaults will be used.
                                            </p>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table custom-table">
                                                        <tr>
                                                            <td><label>Category Base</label></td>
                                                            <td><input type="text" class="form-control" name="category_base" value="<?= isset($config['category_base']) ? $config['category_base'] : "" ?>"/></td>
                                                        </tr>
                                                        <tr>
                                                            <td><label>Tag Base</label></td>
                                                            <td><input type="text" class="form-control" name="tag_base" value="<?= isset($config['tag_base']) ? $config['tag_base'] : "" ?>"/></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    
                                                </div>
                                            </div>                                                                                                                                  
                                        </div>                                                                                                              
                                    </div><!-- /.box-left -->                                         
                                </div>                                                                
                                <?= $updatemsg ?>
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <input type="submit" class="btn btn-primary" name="update" value="Update"/>
                            </div>
                        </form>
                    </div><!-- /.box -->

                </section>

            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>
        <script>
            $("input[type=radio]").click(function(){               
               $("#permalink_structure").val($(this).val());
            });
            $("button").click(function(){
                if($("#permalink_structure").val().indexOf($(this).html()) === -1) {
                    if($("#permalink_structure").val().endsWith("/")) {
                        $("#permalink_structure").val($("#permalink_structure").val() + $(this).html()+ "/");
                    } else {
                        $("#permalink_structure").val($("#permalink_structure").val() + "/" +  $(this).html() + "/");
                    }                    
                } else {                    
                    $("#permalink_structure").val($("#permalink_structure").val().replace($(this).html() + "/", ""));
                } 
                if($("#permalink_structure").val() === "/") {
                   $("#permalink_structure").val("") 
                }
            });
        </script>
    </body>
</html>