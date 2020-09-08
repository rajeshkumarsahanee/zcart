<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(PAYMENT_METHODS_SECTION, getUserLoggedId())) {
    header("location: settings-payment-method.php");
    exit();
}

$msg = "";

//Update Payment Method
if (isset($_POST['name']) && isset($_POST['display_order']) && isUserHavePermission(PAYMENT_METHODS_SECTION, getUserLoggedId())) {
    $pmethod['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $tmppmethod = getPaymentMethod($pmethod['id']);
    $pmethod['code'] = $tmppmethod['code'];
    $pmethod['fields'] = $tmppmethod['fields'];
    $pmethod['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $pmethod['display_order'] = filter_var(trim($_POST['display_order']), FILTER_SANITIZE_NUMBER_INT);
    $pmethod['icon'] = filter_var(trim($_POST['icon']), FILTER_SANITIZE_STRING);
    $pmethod['status'] = filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING);
    $pmethod['details'] = filter_var(trim($_POST['details']), FILTER_SANITIZE_STRING);
        
    if ($pmethod['name'] == '' && $pmethod['display_order'] == '') {
        $msg = '<div class="alert alert-danger">Please enter name and display order</div>';
    } else {        
        $msg = '<div class="alert alert-success">Payment method updated successfully!</div>';
        if (!updatePaymentMethod($pmethod)) {
            $msg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }        
    }
}

$id = filter_var(trim($_REQUEST['id']), FILTER_SANITIZE_NUMBER_INT);
/*add filters if required*/
$pmethod = getPaymentMethod($id);
if($pmethod == null) {
    header("location: settings-payment-methods.php");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Payment Method - Admin</title>
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
                        Edit Payment Method
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Settings</li>
                        <li>Payment Methods</li>
                        <li class="active">Edit Payment Method</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-body">                                                                        
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $pmethod['id'] ?>"/>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Name*</label>
                                                    <input type="text" class="form-control" name="name" value="<?= $pmethod['name'] ?>" id="name" placeholder="Name" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Display Order*</label>
                                                            <input type="number" class="form-control" name="display_order" value="<?= $pmethod['display_order'] ?>" id="display_order" placeholder="Display Order"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Status</label>
                                                            <select class="form-control" name="status">
                                                                <option value="A" <?= $pmethod['status'] == 'A' ? "selected" : "" ?>>Active</option>
                                                                <option value="I" <?= $pmethod['status'] == 'I' ? "selected" : "" ?>>Inactive</option>
                                                            </select>
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Icon</label>
                                                    <img id="iconimg" src="<?= isset($pmethod['icon']) && trim($pmethod['icon']) <> "" ? $pmethod['icon'] : 'https://via.placeholder.com/75x75'  ?>" class="img-responsive"/>
                                                    <input type="hidden" id="icon" name="icon" value="<?= $pmethod['icon'] ?>"/>
                                                    <input type="file" id="imguploadinput" class="form-control"/>
                                                    <span id="uploadingspanmsg"></span>
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Details</label>
                                                    <textarea class="form-control" name="details" id="details" placeholder="Enter Details"><?= $pmethod['details'] ?></textarea>
                                                </div>
                                            </div>
                                        </div>
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
        <script>
            $("#imguploadinput").change(function(e){                                      
                e.preventDefault();                                            
                var action = "<?= $sys['site_url']; ?>/requests.php?action=upload-logo";
                if($("#imguploadinput").val() === "") {
                    return;
                }
                $("#uploadingspanmsg").html("Uploading...");
                var data = new FormData();
                data.append("image", $('input[type=file]')[0].files[0]);
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: data,
                    /*THIS MUST BE DONE FOR FILE UPLOADING*/
                    contentType: false,
                    processData: false,
                }).done(function(data){  
                    $("#uploadingspanmsg").html(data.msg);
                    if(data.code === '0') {   
                        $("#icon").val(data.file_url);
                        $("#iconimg").attr("src", data.file_url);
                        $("#uploadingspanmsg").html("");
                    }  
                }).fail(function(data){
                    //any message
                });  

            });
        </script>
    </body>
</html>