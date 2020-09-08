<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(MANAGE_SHOPS_SECTION, getUserLoggedId()) || !isset($_REQUEST['id']) || trim($_REQUEST['id']) == "" || trim($_REQUEST['id']) == 1) {
    header("location: shops.php");
}

//Edit Shop
$updatemsg = "";
if (isset($_POST['name']) && isset($_POST['url']) && isUserHavePermission(MANAGE_SHOPS_SECTION, getUserLoggedId())) {
    $ushop['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);    
    $shop = getShop($ushop['id']);
    
    $ushop['owner_id'] = $shop['owner_id'];
    $ushop['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $ushop['url'] = filter_var(trim($_POST['url']), FILTER_SANITIZE_STRING);
    $ushop['description'] = filter_var(trim($_POST['description']), FILTER_SANITIZE_STRING);
    $ushop['logo'] = !empty($_FILES['logo']['name']) ? Sys_uploadShopLogo("logo") : $shop['logo'];
    $ushop['banner'] = !empty($_FILES['banner']['name']) ? Sys_uploadShopBanner("banner") : $shop['banner'];
    $ushop['featured'] = isset($_POST['featured']) ? "Y" : "N";
    $ushop['cod_enabled'] = isset($_POST['cod_enabled']) ? "Y" : "N";
    $ushop['contact_person_name'] = filter_var(trim($_POST['contact_person_name']), FILTER_SANITIZE_STRING);
    $ushop['phone'] = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $ushop['address1'] = filter_var(trim($_POST['address1']), FILTER_SANITIZE_STRING);
    $ushop['address2'] = filter_var(trim($_POST['address2']), FILTER_SANITIZE_STRING);
    $ushop['city'] = filter_var(trim($_POST['city']), FILTER_SANITIZE_STRING);
    $ushop['state'] = filter_var(trim($_POST['state']), FILTER_SANITIZE_STRING);
    $ushop['pincode'] = filter_var(trim($_POST['pincode']), FILTER_SANITIZE_STRING);
    $ushop['country'] = filter_var(trim($_POST['state']), FILTER_SANITIZE_STRING);
    $ushop['payment_policy'] = filter_var(trim($_POST['payment_policy']), FILTER_SANITIZE_STRING);
    $ushop['delivery_policy'] = filter_var(trim($_POST['delivery_policy']), FILTER_SANITIZE_STRING);
    $ushop['refund_policy'] = filter_var(trim($_POST['refund_policy']), FILTER_SANITIZE_STRING);
    $ushop['additional_information'] = filter_var(trim($_POST['additional_information']), FILTER_SANITIZE_STRING);
    $ushop['seller_information'] = filter_var(trim($_POST['additional_information']), FILTER_SANITIZE_STRING);
    $ushop['items_count'] = $shop['items_count'];
    $ushop['reviews_count'] = $shop['reviews_count'];
    $ushop['reports_count'] = $shop['reports_count'];
    $ushop['meta_title'] = filter_var(trim($_POST['metatitle']), FILTER_SANITIZE_STRING);
    $ushop['meta_keywords'] = filter_var(trim($_POST['metakeywords']), FILTER_SANITIZE_STRING);
    $ushop['meta_description'] = filter_var(trim($_POST['metadescription']), FILTER_SANITIZE_STRING);
    $ushop['added_timestamp'] = $shop['added_timestamp'];
    $ushop['updated_timestamp'] = time();
    $ushop['status'] = $_POST['active'];                     
    $ushop['status_message'] = $ushop['status'] == "A" ? "Activated" : "Pending Status";        

    if ($ushop['name'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter shop name</div>';
    } else if ($ushop['url'] == '') {
        $updatemsg = '<div class="alert alert-danger">Please enter url</div>';
    } else {
        $updatemsg = '<div class="alert alert-success">Shop updated successfully!</div>';
        if (!updateShop($ushop)) {
            $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

$shop = getShop(trim($_REQUEST['id']));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Shop - Admin</title>
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
                        Edit Shop
                        <small>edit shop information</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class=""><a href="<?= $sys['site_url'] ?>/admin/shops.php">Shops</a></li>
                        <li class="active">Edit Shop</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><?= $shop['name'] ?> [<?= $sys['statuses'][$shop['status']] ?>]</h3>
                                    <div class="btn-group pull-right" data-toggle="btn-toggle">
                                        <button type="button" id="activebid" class="btn btn-default btn-sm <?= trim($shop['status']) == 'A' ? 'active' : '' ?>">active</button>
                                        <button type="button" id="inactivebid" class="btn btn-default btn-sm <?= trim($shop['status']) == 'I' ? 'active' : '' ?>">inactive</button>
                                    </div>
                                </div><!-- /.box-header -->
                                <div class="box-body">  
                                    <form role="form" method="post" action="" enctype="multipart/form-data">
                                        <input id="id" type="hidden" name="id" value="<?= $shop['id'] ?>"/>
                                        <input type="hidden" class="form-control" id="activeid" name="active" value="<?php echo $shop['status']; ?>"/>                                                                                        

                                        <div class="box box-solid">
                                            <div class="box-header with-border">                                                    
                                                <h3 class="box-title">Basic Information</h3>
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                                <!-- text input -->
                                                <div class="form-group">
                                                    <label>Name*</label>
                                                    <input id="name" type="text" name="name" class="form-control" placeholder="Enter Name..." value="<?php echo $shop['name']; ?>" required/>
                                                </div>
                                                <!-- text input -->
                                                <div class="form-group">
                                                    <label>Url*</label>
                                                    <input type="text" class="form-control" name="url" id="url" placeholder="Url" value="<?php echo $shop['url']; ?>" required/>
                                                </div>
                                                <!-- textarea -->
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea id="description" name="description" class="form-control" placeholder="Description"><?php echo $shop['description']; ?></textarea>
                                                </div>                                            
                                                <div class="form-group">
                                                    <label>Shop Logo</label>
                                                    <input type="file" name="logo" class="form-control"/>
                                                    Upload a .jpg, .gif or .png. This will be displayed in 165px x 165px on your store.
                                                    <img class="img-responsive" src="<?php echo $sys['config']['site_url'] . "/" . $shop['logo'] ?>"/>
                                                </div>
                                                <div class="form-group">
                                                    <label>Shop Banner</label>
                                                    <input type="file" name="banner" class="form-control"/>
                                                    Upload a .jpg, .gif or .png. This will be displayed in 1200px x 360px on your store.
                                                    <img class="img-responsive" src="<?php echo $sys['config']['site_url'] . "/" . $shop['banner'] ?>"/>
                                                </div>
                                                <div class="form-group">
                                                    <label for="filters">Featured Shop</label><br>
                                                    <input type="checkbox" name="featured" <?php if($shop['featured'] == "Y") { echo "checked"; } ?>/> Yes<br>
                                                    Featured Shops will be listed on Featured Shops Page. Featured Shops will get priority.
                                                </div>
                                                <div class="form-group">
                                                    <label for="filters">Enable COD Orders</label><br>
                                                    <input type="checkbox" name="cod_enabled" <?php if($shop['cod_enabled'] == "Y") { echo "checked"; } ?>/> Yes<br>
                                                    You will have to keep minimum $1,000.00 in your wallet to accept COD orders.
                                                </div>
                                            </div>
                                            <!-- /.box-body -->
                                        </div>

                                        <div class="box box-solid">
                                            <div class="box-header with-border">                                                    
                                                <h3 class="box-title">Shop Address (Please Provide Address And Contact Person Information Of Your Shop.)</h3>
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label>Contact Person Name</label>
                                                    <input id="contact_person_name" type="text" name="contact_person_name" class="form-control" placeholder="Contact Person Name" value="<?php echo $shop['contact_person_name']; ?>" required/>
                                                </div>
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input id="phone" type="text" name="phone" class="form-control" placeholder="Phone" value="<?php echo $shop['phone']; ?>" required/>
                                                </div>
                                                <div class="form-group">
                                                    <label>Address Line 1</label>
                                                    <input id="address1" type="text" name="address1" class="form-control" placeholder="Address Line 1" value="<?php echo $shop['address1']; ?>" required/>
                                                </div>
                                                <div class="form-group">
                                                    <label>Address Line 2</label>
                                                    <input id="address2" type="text" name="address2" class="form-control" placeholder="Address Line 2" value="<?php echo $shop['address2']; ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label>City</label>
                                                    <input id="city" type="text" name="city" class="form-control" placeholder="City" value="<?php echo $shop['city']; ?>" required/>
                                                </div>
                                                <div class="form-group">
                                                    <label>Pincode</label>
                                                    <input id="pincode" type="text" name="pincode" class="form-control" placeholder="Pincode" value="<?php echo $shop['pincode']; ?>" required/>
                                                </div>
                                                <div class="form-group">
                                                    <label>State</label>
                                                    <input id="state" type="text" name="state" class="form-control" placeholder="State" value="<?php echo $shop['state']; ?>" required/>
                                                </div>
                                                <div class="form-group">
                                                    <label>Country</label>
                                                    <input id="country" type="text" name="country" class="form-control" placeholder="Country" value="<?php echo $shop['country']; ?>" required/>
                                                </div>
                                            </div>
                                            <!-- /.box-body -->
                                        </div>

                                        <div class="box box-solid">
                                            <div class="box-header with-border">                                                    
                                                <h3 class="box-title">Shop Policies (Optional)</h3>
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label>Payment Policy</label>
                                                    <textarea id="payment_policy" name="payment_policy" class="form-control" placeholder="Payment Policy" rows="7"><?php echo $shop['payment_policy']; ?></textarea>
                                                    Payment methods, terms, deadlines, taxes, cancellation policy, etc.
                                                </div>  
                                                <div class="form-group">
                                                    <label>Delivery Policy</label>
                                                    <textarea id="delivery_policy" name="delivery_policy" class="form-control" placeholder="Delivery Policy" rows="7"><?php echo $shop['delivery_policy']; ?></textarea>
                                                    Delivery methods, upgrades, deadlines, insurance, confirmation, international customs, etc.
                                                </div>  
                                                <div class="form-group">
                                                    <label>Refund Policy</label>
                                                    <textarea id="refund_policy" name="refund_policy" class="form-control" placeholder="Refund Policy" rows="7"><?php echo $shop['refund_policy']; ?></textarea>
                                                    Terms, eligible items, damages, losses, etc.
                                                </div>
                                                <div class="form-group">
                                                    <label>Additional Information</label>
                                                    <textarea id="additional_information" name="additional_information" class="form-control" placeholder="Additional Information" rows="7"><?php echo $shop['additional_information']; ?></textarea>
                                                    Additional policies, FAQs, custom orders, wholesale & consignment, guarantees, etc.
                                                </div>
                                                <div class="form-group">
                                                    <label>Seller Information</label>
                                                    <textarea id="seller_information" name="seller_information" class="form-control" placeholder="Seller Information" rows="7"><?php echo $shop['seller_information']; ?></textarea>
                                                    Some countries require seller information such as your name, physical address, contact email address and, where applicable, tax identification number.
                                                </div>

                                            </div>
                                            <!-- /.box-body -->
                                        </div>

                                        <div class="box box-solid">
                                            <div class="box-header with-border">                                                    
                                                <h3 class="box-title">Shop SEO Information (Optional)</h3>
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                                <div class="form-group">
                                                    <label>Meta Title</label>
                                                    <input type="text" class="form-control" name="metatitle" id="metatitle" placeholder="Meta Title" value="<?php echo $shop['meta_title']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Meta Keywords</label>
                                                    <input type="text" class="form-control" name="metakeywords" id="metakeywords" placeholder="Meta Keywords" value="<?php echo $shop['meta_keywords']; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Meta Description</label>
                                                    <textarea name="metadescription" id="metadescription" class="form-control" placeholder="Meta Description"><?php echo $shop['meta_description']; ?></textarea>
                                                </div>

                                            </div>
                                            <!-- /.box-body -->
                                        </div>

                                        <?php echo $updatemsg; ?>
                                        <div class="box-footer">
                                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>                 
                    </div><!-- /.row -->
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>   

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>        
        <script>
            $("#activebid").click(function () {
                $("#activeid").val("A");
            });

            $("#inactivebid").click(function () {
                $("#activeid").val("I");
            });                        
            
            $(".select2").select2();
        </script>
    </body>
</html>