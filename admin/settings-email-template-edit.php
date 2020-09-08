<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(EMAIL_TEMPLATE_SETTINGS_SECTION, getUserLoggedId())) {
    header("location: settings-email-templates.php");
    exit();
}

$msg = "";

//Update Email Template
if (isset($_POST['name']) && isUserHavePermission(EMAIL_TEMPLATE_SETTINGS_SECTION, getUserLoggedId())) {
    $template['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $tmptemplate = getEmailTemplate($template['id']);
    $template['code'] = $tmptemplate['code'];
    $template['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $template['subject'] = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
    $template['body'] = $_POST['body'];
    $template['replacements'] = $tmptemplate['replacements'];
    $template['status'] = $tmptemplate['status'];
        
    if ($template['name'] == '') {
        $msg = '<div class="alert alert-danger">Please enter name</div>';
    } else {        
        $msg = '<div class="alert alert-success">Email Template updated successfully!</div>';
        if (!updateEmailTemplate($template)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

$id = filter_var(trim($_REQUEST['id']), FILTER_SANITIZE_NUMBER_INT);
/*add filters if required*/
$template = getEmailTemplate($id);
if($template == null) {
    header("location: settings-email-templates.php");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Email Template - Admin</title>
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
                        Edit Email Template
                        <small>[ <?= $template['code'] ?> ]</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li><a href="<?= $sys['site_url'] ?>/admin/settings-email-templates.php">Email Templates</a></li>
                        <li class="active">Edit Email Template</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body">                                                                        
                                    <form action="" method="post">
                                        <input type="hidden" name="id" value="<?= $template['id'] ?>"/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="name">Template Name*</label>
                                                    <input type="text" class="form-control" name="name" value="<?= $template['name'] ?>" id="name" placeholder="Template Name" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="subject">Subject</label>
                                                    <input type="text" class="form-control" name="subject" value="<?= $template['subject'] ?>" id="title" placeholder="Enter Subject" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Body</label>
                                                    <textarea class="form-control" name="body" id="body" placeholder="Email Body"><?= $template['body'] ?></textarea>
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Template Vars</label>
                                                    <div>
                                                        <?= $template['replacements'] ?>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                        <hr>
                                        <?= $msg ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </form>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col-md-8 -->
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>  
        <script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('body');
        </script>
    </body>
</html>