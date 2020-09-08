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
    $settings['default_comment_status'] = isset($_POST['default_comment_status']) ? "1" : "0";
    $settings['require_name_email'] = isset($_POST['require_name_email']) ? "1" : "0";
    $settings['comment_registration'] = isset($_POST['comment_registration']) ? "1" : "0";
    $settings['close_comments_for_old_posts'] = isset($_POST['close_comments_for_old_posts']) ? "1" : "0";
    $settings['close_comments_days_old'] = isset($_POST['close_comments_for_old_posts']) ? filter_var(trim($_POST['close_comments_for_old_posts']), FILTER_SANITIZE_NUMBER_INT) : "14";
    $settings['thread_comments'] = isset($_POST['thread_comments']) ? "1" : "0";
    $settings['thread_comments_depth'] = isset($_POST['thread_comments_depth']) ? filter_var(trim($_POST['thread_comments_depth']), FILTER_SANITIZE_NUMBER_INT) : "5";
    $settings['page_comments'] = isset($_POST['page_comments']) ? "1" : "0";
    $settings['comments_per_page'] = isset($_POST['comments_per_page']) ? filter_var(trim($_POST['comments_per_page']), FILTER_SANITIZE_NUMBER_INT) : "50";
    $settings['default_comments_page'] = isset($_POST['default_comments_page']) ? filter_var(trim($_POST['default_comments_page']), FILTER_SANITIZE_STRING) : "newest";
    $settings['comment_order'] = isset($_POST['comment_order']) ? filter_var(trim($_POST['comment_order']), FILTER_SANITIZE_STRING) : "asc";
    $settings['comments_notify'] = isset($_POST['comments_notify']) ? "1" : "0";
    $settings['moderation_notify'] = isset($_POST['moderation_notify']) ? "1" : "0";
    $settings['comment_moderation'] = isset($_POST['comment_moderation']) ? "1" : "0";
    $settings['comment_whitelist'] = isset($_POST['comment_whitelist']) ? "1" : "0";
    $settings['comment_max_links'] = isset($_POST['comment_max_links']) ? filter_var(trim($_POST['comment_max_links']), FILTER_SANITIZE_NUMBER_INT) : "2";
    $settings['moderation_keys'] = isset($_POST['moderation_keys']) ? trim($_POST['moderation_keys']) : "";
    $settings['blacklist_keys'] = isset($_POST['blacklist_keys']) ? trim($_POST['blacklist_keys']) : "";
    $settings['show_avatars'] = isset($_POST['show_avatars']) ? "1" : "0";    
    $settings['avatar_default'] = isset($_POST['avatar_default']) ? filter_var(trim($_POST['avatar_default']), FILTER_SANITIZE_STRING) : "mystery";
    
    if (saveAllConfig($settings)) {
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
        <title>Discussion Settings - Admin</title>
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
                        <li class="active">Discussion</li>
                    </ol>
                </section>
                <section class="content">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Discussion Settings</h3>                            
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="" method="post">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">                                        
                                        <div class="form-group">
                                            <label><input name="default_comment_status" type="checkbox" value="1" <?= isset($config['default_comment_status']) && $config['default_comment_status'] == "1" ? "checked" : "" ?>> Allow people to post comments on new articles</label>
                                            <span>(This settings may be overridden for individual articles.)</span>
                                        </div>
                                        <div class="form-group">
                                            <label><input name="require_name_email" type="checkbox" value="1" <?= isset($config['require_name_email']) && $config['require_name_email'] == "1" ? "checked" : "" ?>>  Comment author must fill out name and email</label>
                                        </div>
                                        <div class="form-group">
                                            <label><input name="comment_registration" type="checkbox" value="1" <?= isset($config['comment_registration']) && $config['comment_registration'] == "1" ? "checked" : "" ?>>  Users must be registered and logged in to comment</label>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input name="close_comments_for_old_posts" type="checkbox" value="1" <?= isset($config['close_comments_for_old_posts']) && $config['close_comments_for_old_posts'] == "1" ? "checked" : "" ?>> 
                                                Automatically close comments on articles older than 
                                                <input type="number" name="close_comments_days_old" value="<?= isset($config['close_comments_days_old']) ? $config['close_comments_days_old'] : "14" ?>"/> days
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input name="thread_comments" type="checkbox" value="1" <?= isset($config['thread_comments']) && $config['thread_comments'] == "1" ? "checked" : "" ?>> 
                                                 Enable threaded (nested) comments 
                                                 <select name="thread_comments_depth">
                                                     <?php for($i = 2; $i <= 10; $i++) { ?>
                                                     <option value="<?= $i ?>" <?= isset($config['thread_comments_depth']) && $config['thread_comments_depth'] == $i ? "selected" : "" ?>><?= $i ?></option>
                                                     <?php } ?>
                                                 </select> 
                                                 levels deep
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input name="page_comments" type="checkbox" value="1" <?= isset($config['thread_comments']) && $config['thread_comments'] == "1" ? "checked" : "" ?>> 
                                                Break comments into pages with <input type="number" name="comments_per_page" value="<?= isset($config['comments_per_page']) ? $config['comments_per_page'] : "50" ?>"/> top level comments per page and the
                                                  <select name="default_comments_page">
                                                      <option value="newest" <?= isset($config['default_comments_page']) && $config['default_comments_page'] == "newest" ? "selected" : "" ?>>last</option>
                                                      <option value="oldest" <?= isset($config['default_comments_page']) && $config['default_comments_page'] == "oldest" ? "selected" : "" ?>>first</option>
                                                  </select>
                                                  page displayed by default                                                
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>                                                
                                                Comments should be displayed with the 
                                                 <select name="comment_order">
                                                    <option value="asc" <?= isset($config['comment_order']) && $config['comment_order'] == "asc" ? "selected" : "" ?>>older</option>
                                                    <option value="desc" <?= isset($config['comment_order']) && $config['comment_order'] == "desc" ? "selected" : "" ?>>newer</option>
                                                 </select> 
                                                 comments at the top of each page
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label><input name="comments_notify" type="checkbox" value="1" <?= isset($config['comments_notify']) && $config['comments_notify'] == "1" ? "checked" : "" ?>>  Email me whenever Anyone posts a comment</label>
                                        </div>
                                        <div class="form-group">
                                            <label><input name="moderation_notify" type="checkbox" value="1" <?= isset($config['moderation_notify']) && $config['moderation_notify'] == "1" ? "checked" : "" ?>>  Email me whenever A comment is held for moderation</label>
                                        </div>
                                        <div class="form-group">
                                            <label><input name="comment_moderation" type="checkbox" value="1" <?= isset($config['comment_moderation']) && $config['comment_moderation'] == "1" ? "checked" : "" ?>> Comment must be manually approved</label>
                                        </div>
                                        <div class="form-group">
                                            <label><input name="comment_whitelist" type="checkbox" value="1" <?= isset($config['comment_whitelist']) && $config['comment_whitelist'] == "1" ? "checked" : "" ?>> Comment author must have a previously approved comment</label>
                                        </div>
                                        <div class="form-group">
                                            <label>                                                
                                                Hold a comment in the queue if it contains <input type="number" name="comment_max_links" value="<?= isset($config['comment_max_links']) ? $config['comment_max_links'] : "2" ?>"/> or more links.
                                                (A common characteristic of comment spam is a large number of hyperlinks.)                                                 
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>                                                
                                                When a comment contains any of these words in its content, name, URL, email, or IP address, it will be held in the moderation queue. One word or IP address per line. 
                                                It will match inside words, so "cms" will match "zcms".
                                                <textarea class="form-control" name="moderation_keys"><?= isset($config['moderation_keys']) ? $config['moderation_keys'] : "" ?></textarea>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>                                                
                                                When a comment contains any of these words in its content, name, URL, email, or IP address, it will be put in the trash. One word or IP address per line. 
                                                It will match inside words, so "cms" will match "zcms".
                                                <textarea class="form-control" name="blacklist_keys"><?= isset($config['blacklist_keys']) ? $config['blacklist_keys'] : "" ?></textarea>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label><input name="show_avatars" type="checkbox" value="1" <?= isset($config['show_avatars']) && $config['show_avatars'] == "1" ? "checked" : "" ?>> Show Avatars</label>
                                        </div>
                                        <div class="form-group" id="avatars_container">
                                            <label>Default Avatar</label><br/>
                                            <label style="font-weight: normal; margin-left: 15px;">
                                                <input name="avatar_default" type="radio" value="mystery" <?= isset($config['avatar_default']) && $config['avatar_default'] == "mystery" ? "checked" : "" ?>> 
                                                <img alt="" src="https://secure.gravatar.com/avatar/c60ba6d6fb7e5f3fcc8b2e2938a4d03c?s=32&amp;d=mm&amp;f=y&amp;r=g" srcset="https://secure.gravatar.com/avatar/c60ba6d6fb7e5f3fcc8b2e2938a4d03c?s=64&amp;d=mm&amp;f=y&amp;r=g 2x" class="avatar avatar-32 photo avatar-default" width="32" height="32">
                                                Mystery Person
                                            </label><br/>
                                            <label style="font-weight: normal; margin-left: 15px;">
                                                <input name="avatar_default" type="radio" value="blank" <?= isset($config['avatar_default']) && $config['avatar_default'] == "blank" ? "checked" : "" ?>> 
                                                <img alt="" src="https://secure.gravatar.com/avatar/c60ba6d6fb7e5f3fcc8b2e2938a4d03c?s=32&amp;d=blank&amp;f=y&amp;r=g" srcset="https://secure.gravatar.com/avatar/c60ba6d6fb7e5f3fcc8b2e2938a4d03c?s=64&amp;d=blank&amp;f=y&amp;r=g 2x" class="avatar avatar-32 photo avatar-default" width="32" height="32">
                                                Blank
                                            </label>
                                        </div>
                                    </div><!-- /.box-left -->                                         
                                </div>                                                                
                                <?= $updatemsg ?>
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary" name="update">Update</button>
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
            $("input[name=show_avatars]").click(function(){
                if($(this).prop("checked") === true) {
                    $("#avatars_container").show();
                } else {
                    $("#avatars_container").hide();
                }               
            });            
            $("button").click(function(){
                if($("#permalink_structure").val().indexOf($(this).html()) === -1) {
                    if($("#permalink_structure").val().endsWith("/")) {
                        $("#permalink_structure").val($("#permalink_structure").val() + $(this) .html()+ "/");
                    } else {
                        $("#permalink_structure").val($("#permalink_structure").val() + "/" +  $(this) .html() + "/");
                    }                    
                } else {                    
                    $("#permalink_structure").val($("#permalink_structure").val().replace($(this) .html() + "/", ""));
                } 
                if($("#permalink_structure").val() === "/") {
                   $("#permalink_structure").val("") 
                }
            });
        </script>
    </body>
</html>