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
    $settings['underconstruction'] = filter_var(trim($_POST['underconstruction']), FILTER_SANITIZE_NUMBER_INT);
    $settings['undermaintenance'] = filter_var(trim($_POST['undermaintenance']), FILTER_SANITIZE_NUMBER_INT);    
    $settings['default_language'] = filter_var(trim($_POST['default_language']), FILTER_SANITIZE_STRING);
    $settings['admin_email'] = filter_var(trim($_POST['admin_email']), FILTER_SANITIZE_EMAIL);
    $settings['users_can_register'] = filter_var(trim($_POST['users_can_register']), FILTER_SANITIZE_STRING);
    $settings['default_role'] = filter_var(trim($_POST['default_role']), FILTER_SANITIZE_STRING);
    $settings['timezone'] = filter_var(trim($_POST['timezone']), FILTER_SANITIZE_STRING);
    $settings['date_format'] = filter_var(trim($_POST['date_format']), FILTER_SANITIZE_STRING);
    $settings['time_format'] = filter_var(trim($_POST['time_format']), FILTER_SANITIZE_STRING);
    $settings['start_of_week'] = filter_var(trim($_POST['start_of_week']), FILTER_SANITIZE_STRING);
    $settings['site_name'] = filter_var(trim($_POST['site_name']), FILTER_SANITIZE_STRING);
    $settings['tagline'] = filter_var(trim($_POST['tagline']), FILTER_SANITIZE_STRING);
    $settings['site_url'] = filter_var(trim($_POST['site_url']), FILTER_SANITIZE_STRING);
    $settings['site_meta_title'] = filter_var(trim($_POST['site_meta_title']), FILTER_SANITIZE_STRING);
    $settings['site_meta_keywords'] = filter_var(trim($_POST['site_meta_keywords']), FILTER_SANITIZE_STRING);
    $settings['site_meta_desc'] = htmlspecialchars(addslashes(trim($_POST['site_meta_desc'])));
    $settings['analytics_code'] = htmlspecialchars(addslashes($_POST['analytics_code']));
    $settings['cache_system'] = isset($_POST['cache_system']) ? "1" : "0";
    $settings['seo_link'] = '0';
    $settings['default_category'] = isset($_POST['default_category']) ? filter_var(trim($_POST['default_category']), FILTER_SANITIZE_NUMBER_INT) : "1";
    $settings['default_post_format'] = filter_var(trim($_POST['default_post_format']), FILTER_SANITIZE_STRING);
    $settings['posts_per_page'] = filter_var(trim($_POST['posts_per_page']), FILTER_SANITIZE_NUMBER_INT);
    $settings['site_public'] = isset($_POST['site_public']) ? "0" : "1";
    //media settings
    $settings['thumbnail_size_w'] = filter_var(trim($_POST['thumbnail_size_w']), FILTER_SANITIZE_NUMBER_INT);
    $settings['thumbnail_size_h'] = filter_var(trim($_POST['thumbnail_size_h']), FILTER_SANITIZE_NUMBER_INT);
    $settings['medium_size_w'] = filter_var(trim($_POST['medium_size_w']), FILTER_SANITIZE_NUMBER_INT);
    $settings['medium_size_h'] = filter_var(trim($_POST['medium_size_h']), FILTER_SANITIZE_NUMBER_INT);
    $settings['large_size_w'] = filter_var(trim($_POST['large_size_w']), FILTER_SANITIZE_NUMBER_INT);
    $settings['large_size_h'] = filter_var(trim($_POST['large_size_h']), FILTER_SANITIZE_NUMBER_INT);
    //home page settings
    $settings['show_on_front'] = filter_var(trim($_POST['show_on_front']), FILTER_SANITIZE_STRING);
    //mail settings
    $settings['smtp_or_mail'] = filter_var(trim($_POST['smtp_or_mail']), FILTER_SANITIZE_STRING);
    $settings['smtp_host'] = filter_var(trim($_POST['smtp_host']), FILTER_SANITIZE_STRING);
    $settings['smtp_port'] = filter_var(trim($_POST['smtp_port']), FILTER_SANITIZE_NUMBER_INT);
    $settings['smtp_encryption'] = filter_var(trim($_POST['smtp_encryption']), FILTER_SANITIZE_STRING);
    $settings['smtp_username'] = filter_var(trim($_POST['smtp_username']), FILTER_SANITIZE_STRING);
    $settings['smtp_password'] = filter_var(trim($_POST['smtp_password']), FILTER_SANITIZE_STRING);

    if (saveAllConfig($settings)) {
        $updatemsg = '<div class="alert alert-success">Settings saved successfully!</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">There is some problem!</div>';
    }
}

$languages = getLanguages();
$config = getConfig();
$categories = getTerms(array(), array('taxonomy' => "category"), 0, -1);

$pages = getPosts(array(), array('post_type' => "page"), 0, -1);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>General Settings - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link rel="stylesheet" href="<?= $sys['site_url'] ?>/admin/plugins/select2/select2.min.css">
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
                        Settings
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li><a href="#">Settings</a></li>
                        <li class="active">General</li>
                    </ol>
                </section>
                <section class="content">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">General Settings</h3>                            
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="" method="post">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Under Construction</label><br/>
                                                    <input name="underconstruction" value="0" type="radio" <?= isset($config['underconstruction']) && $config['underconstruction'] == '0' ? 'checked' : "" ?>> No
                                                    <input name="underconstruction" value="1" type="radio" <?= isset($config['underconstruction']) && $config['underconstruction'] == '1' ? 'checked' : "" ?>> Yes
                                                </div>  
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Under Maintenance</label><br/>
                                                    <input name="undermaintenance" value="0" type="radio" <?= isset($config['undermaintenance']) && $config['undermaintenance'] == '0' ? 'checked' : "" ?>> No
                                                    <input name="undermaintenance" value="1" type="radio" <?= isset($config['undermaintenance']) && $config['undermaintenance'] == '1' ? 'checked' : "" ?>> Yes
                                                </div>
                                            </div>
                                        </div>                                                                                 
                                        <div class="form-group">                                        
                                            <label>Language</label><br/>
                                            <select class="form-control" name="default_language" required>
                                                <option value=""> - Select - </option>
                                                <?php
                                                foreach ($languages as $language) {
                                                    $langname = preg_replace('/\\.[^.\\s]{3,4}$/', '', $language);
                                                    ?>
                                                    <option value="<?= $langname ?>" <?= isset($config['default_language']) && $langname == $config['default_language'] ? "selected" : "" ?>><?= $langname ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">                                        
                                            <label >Email Address</label>
                                            <input type="email" name="admin_email" value="<?= isset($config['admin_email']) ? $config['admin_email'] : "" ?>" class="form-control"/>
                                        </div>
                                        <div class="form-group">                                        
                                            <label>Membership</label><br/>
                                            <input type="checkbox" name="users_can_register" value="1" <?= isset($config['users_can_register']) && $config['users_can_register'] == "1" ? "checked" : "" ?>/>
                                            Anyone can register
                                        </div>
                                        <div class="form-group">                                        
                                            <label>New User Default Role</label>
                                            <select name="default_role" class="form-control">
                                                <?php foreach($sys['roles'] as $key => $value) { ?>
                                                <option value="<?= $key ?>" <?= isset($config['default_role']) && trim($config['default_role']) == $key ? "selected" : "" ?>><?= $value ?></option>
                                                <?php } ?>
                                            </select>                                            
                                        </div>
                                        <div class="form-group">                                        
                                            <label>Timezone</label>
                                            <select name="timezone" class="form-control">
                                                <?php foreach(getTimezones() as $key => $value) { ?>
                                                <option value="<?= $key ?>" <?= isset($config['timezone']) && trim($config['timezone']) == $key ? "selected" : "" ?>><?= $value ?></option>
                                                <?php } ?>
                                            </select>                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label >Date Format</label><br/>
                                                    <input type="radio" name="date_format" value="F j, Y" <?= isset($config['date_format']) && $config['date_format'] == "F j, Y" ? "checked" : "" ?>/> <span class="badge"><?= date("F j, Y") ?></span><br/>
                                                    <input type="radio" name="date_format" value="Y-m-d" <?= isset($config['date_format']) && $config['date_format'] == "Y-m-d" ? "checked" : "" ?>/> <span class="badge"><?= date("Y-m-d") ?></span><br/>
                                                    <input type="radio" name="date_format" value="m/d/Y" <?= isset($config['date_format']) && $config['date_format'] == "m/d/Y" ? "checked" : "" ?>/> <span class="badge"><?= date("m/d/Y") ?></span><br/>
                                                    <input type="radio" name="date_format" value="d/m/Y" <?= isset($config['date_format']) && $config['date_format'] == "d/m/Y" ? "checked" : "" ?>/> <span class="badge"><?= date("d/m/Y") ?></span>
                                                </div>  
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label >Time Format</label><br/>
                                                    <input type="radio" name="time_format" value="g:i a" <?= isset($config['time_format']) && $config['time_format'] == "g:i a" ? "checked" : "" ?>/> <span class="badge"><?= date("g:i a") ?></span><br/>
                                                    <input type="radio" name="time_format" value="g:i A" <?= isset($config['time_format']) && $config['time_format'] == "g:i A" ? "checked" : "" ?>/> <span class="badge"><?= date("g:i A") ?></span><br/>
                                                    <input type="radio" name="time_format" value="H:i" <?= isset($config['time_format']) && $config['time_format'] == "H:i" ? "checked" : "" ?>/> <span class="badge"><?= date("H:i") ?></span>                                            
                                                </div>
                                            </div>
                                        </div>                                        
                                        <div class="form-group">                                        
                                            <label >Week Starts On</label>
                                            <select name="start_of_week" id="start_of_week" class="form-control">
                                                <option value="0" <?= isset($config['start_of_week']) && $config['start_of_week'] == "0" ? "selected" : "" ?>>Sunday</option>
                                                <option value="1" <?= isset($config['start_of_week']) && $config['start_of_week'] == "1" ? "selected" : "" ?>>Monday</option>
                                                <option value="2" <?= isset($config['start_of_week']) && $config['start_of_week'] == "2" ? "selected" : "" ?>>Tuesday</option>
                                                <option value="3" <?= isset($config['start_of_week']) && $config['start_of_week'] == "3" ? "selected" : "" ?>>Wednesday</option>
                                                <option value="4" <?= isset($config['start_of_week']) && $config['start_of_week'] == "4" ? "selected" : "" ?>>Thursday</option>
                                                <option value="5" <?= isset($config['start_of_week']) && $config['start_of_week'] == "5" ? "selected" : "" ?>>Friday</option>
                                                <option value="6" <?= isset($config['start_of_week']) && $config['start_of_week'] == "6" ? "selected" : "" ?>>Saturday</option>
                                            </select>
                                        </div>
                                        <div class="form-group">                                        
                                            <label>Cache System</label><br/>
                                            <input type="checkbox" name="cache_system" value="1" <?= isset($config['cache_system']) && $config['cache_system'] == "1" ? "checked" : "" ?>/>
                                            Enable
                                        </div>
                                        <div class="form-group">                                        
                                            <label>Default Post Category</label>
                                            <select name="default_category" class="form-control">
                                                <?php foreach($categories as $category) { ?>
                                                <option value="<?= $category['id'] ?>" <?= isset($config['default_category']) && trim($config['default_category']) == $category['id'] ? "selected" : "" ?>><?= $category['name'] ?></option>
                                                <?php } ?>
                                            </select>                                            
                                        </div>
                                        <div class="form-group">                                        
                                            <label>Default Post Format</label>
                                            <select name="default_post_format" class="form-control">
                                                <?php foreach($sys['post_formats'] as $key => $value) { ?>
                                                <option value="<?= $key ?>" <?= isset($config['default_post_format']) && trim($config['default_post_format']) == $key ? "selected" : "" ?>><?= $value ?></option>
                                                <?php } ?>
                                            </select>                                            
                                        </div>
                                        <div class="form-group">                                        
                                            <label>Blog posts per page</label>
                                            <input type="number" name="posts_per_page" value="<?= isset($config['posts_per_page']) ? $config['posts_per_page'] : "" ?>" class="form-control">
                                        </div>
                                        <div class="form-group">                                        
                                            <label>Search Engine Visibility</label><br/>
                                            <input type="checkbox" name="site_public" value="0" <?= isset($config['site_public']) && $config['site_public'] == "0" ? "checked" : "" ?>/>
                                             Discourage search engines from indexing this site
                                        </div>
                                    </div><!-- /.box-left -->     
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Site Name</label>
                                            <input type="text" name="site_name" value="<?= isset($config['site_name']) ? $config['site_name'] : "" ?>" class="form-control"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Tagline</label>
                                            <input type="text" name="tagline" value="<?= isset($config['tagline']) ? $config['tagline'] : "" ?>" class="form-control"/>
                                        </div>                                        
                                        <div class="form-group">
                                            <label>Site Url</label>
                                            <input type="text" name="site_url" value="<?= isset($config['site_url']) ? $config['site_url'] : "" ?>" class="form-control"/>
                                        </div>                                        
                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" name="site_meta_title" value="<?= isset($config['site_meta_title']) ? $config['site_meta_title'] : "" ?>" class="form-control"/>
                                        </div>                                        
                                        <div class="form-group">
                                            <label>Meta Keywords</label>
                                            <input type="text" name="site_meta_keywords" value="<?= isset($config['site_meta_keywords']) ? $config['site_meta_keywords'] : "" ?>" class="form-control"/>
                                        </div>                                        
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea rows="3" class="form-control" name="site_meta_desc"><?= isset($config['site_meta_desc']) ? $config['site_meta_desc'] : "" ?></textarea>
                                        </div>                                        
                                        <div class="form-group">
                                            <label>Google Analytics Code</label>
                                            <textarea rows="5" class="form-control" name="analytics_code"><?= isset($config['analytics_code']) ? $config['analytics_code'] : "" ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Media Settings</label>
                                            <table class="table" style="border: solid thin silver;">
                                                <thead>
                                                    <tr>
                                                        <th>Thumbnail</th>
                                                        <th>Medium</th>
                                                        <th>Large</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            Width: <input type="number" name="thumbnail_size_w" value="<?= isset($config['thumbnail_size_w']) ? $config['thumbnail_size_w'] : "" ?>" class="form-control">
                                                            Height: <input type="number" name="thumbnail_size_h" value="<?= isset($config['thumbnail_size_h']) ? $config['thumbnail_size_h'] : "" ?>" class="form-control">
                                                        </td>
                                                        <td>
                                                            Max Width: <input type="number" name="medium_size_w" value="<?= isset($config['medium_size_w']) ? $config['medium_size_w'] : "" ?>" class="form-control">
                                                            Max Height: <input type="number" name="medium_size_h" value="<?= isset($config['medium_size_w']) ? $config['medium_size_h'] : "" ?>" class="form-control">
                                                        </td>
                                                        <td>
                                                            Max Width: <input type="number" name="large_size_w" value="<?= isset($config['large_size_w']) ? $config['large_size_w'] : "" ?>" class="form-control">
                                                            Max Height: <input type="number" name="large_size_h" value="<?= isset($config['large_size_w']) ? $config['large_size_h'] : "" ?>" class="form-control">
                                                        </td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <label>Home Page Settings</label>
                                            <table class="table" style="border: solid thin silver;">                                                
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    Home Page Show
                                                                    <select name="show_on_front" class="form-control">
                                                                        <option value="posts" <?= isset($config['show_on_front']) && $config['show_on_front'] == "posts" ? "selected" : "" ?>>Latest Posts</option>
                                                                        <option value="page" <?= isset($config['show_on_front']) && $config['show_on_front'] == "page" ? "selected" : "" ?>>Static Page</option>
                                                                    </select>  
                                                                </div>
                                                                <div class="col-md-4">
                                                                    Home Page
                                                                    <select name="page_for_front" class="form-control select2" readonly>
                                                                        <?php foreach ($pages as $page) { ?>
                                                                            <option value="<?= $page['ID'] ?>"><?= $page['post_title'] ?></option>
                                                                        <?php } ?>
                                                                    </select>       
                                                                </div>
                                                                <div class="col-md-4">
                                                                    Posts Page
                                                                    <select name="page_for_posts" class="form-control select2" readonly>
                                                                        <?php foreach ($pages as $page) { ?>
                                                                            <option value="<?= $page['ID'] ?>"><?= $page['post_title'] ?></option>
                                                                        <?php } ?>
                                                                    </select>       
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>                                                                
                                <div class="row">
                                    <div class="col-md-4">
                                        
                                    </div>
                                    <div class="col-md-8">                                        
                                        <div class="form-group">                                            
                                            <table class="table" style="border: solid thin silver;">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            Mail Settings
                                                            <select id="smtp_or_mail" name="smtp_or_mail" class="pull-right">
                                                                <option value="mail" <?= isset($config['smtp_or_mail']) && trim($config['smtp_or_mail']) == "mail" ? "selected" : "" ?>>Mail</option>
                                                                <option value="smtp" <?= isset($config['smtp_or_mail']) && trim($config['smtp_or_mail']) == "smtp" ? "selected" : "" ?>>SMTP</option>
                                                            </select>
                                                        </th>
                                                    </tr>                                                    
                                                </thead>
                                                <tbody id="smtp_details_container">
                                                    <tr>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="">
                                                                        Host
                                                                        <input type="text" id="smtp_host" name="smtp_host" value="<?= isset($config['smtp_host']) ? $config['smtp_host'] : "" ?>" class="form-control"/>
                                                                    </div>                                                    
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="">
                                                                        Port
                                                                        <input type="number" id="smtp_port" name="smtp_port" value="<?= isset($config['smtp_port']) ? $config['smtp_port'] : "" ?>" class="form-control"/>
                                                                    </div>                                                    
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="">
                                                                        Encryption
                                                                        <select id="smtp_encryption" name="smtp_encryption" class="form-control">
                                                                            <option value="tls" <?= isset($config['smtp_encryption']) && trim($config['smtp_encryption']) == "tls" ? "selected" : "" ?>>TLS</option>
                                                                            <option value="ssl" <?= isset($config['smtp_encryption']) && trim($config['smtp_encryption']) == "ssl" ? "selected" : "" ?>>SSL</option>
                                                                        </select>
                                                                    </div>                                                    
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="">
                                                                        Username
                                                                        <input type="text" id="smtp_username" name="smtp_username" value="<?= isset($config['smtp_username']) ? $config['smtp_username'] : "" ?>" class="form-control"/>
                                                                    </div>                                                    
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="">
                                                                        Password
                                                                        <input type="text" id="smtp_password" name="smtp_password" value="<?= isset($config['smtp_password']) ? $config['smtp_password'] : "" ?>" class="form-control"/>
                                                                    </div>
                                                                </div>                                                    
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>                                            
                                        </div>
                                    </div>
                                    
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
        <script src="<?= $sys['site_url'] ?>/admin/plugins/select2/select2.full.min.js"></script> 
        <script>
            $("#smtp_or_mail").change(function(e) {
               var selected = $(this).val();
               $("#smtp_details_container").hide();
               if(selected === "smtp") {
                   $("#smtp_details_container").show();
               }
            });
            
            $(function () {
                $(".select2").select2();
                $("#smtp_or_mail").trigger("change");                
            });                        
        </script>
    </body>
</html>