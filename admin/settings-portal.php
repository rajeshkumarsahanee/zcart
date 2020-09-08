<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(PORTAL_SETTINGS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$updatemsg = "";
//update general settings
if (isset($_POST['update']) && isUserHavePermission(PORTAL_SETTINGS_SECTION, getUserLoggedId())) {
    $settings['PRIVACY_POLICY'] = isset($_POST['PRIVACY_POLICY']) ? filter_var(trim($_POST['PRIVACY_POLICY']), FILTER_SANITIZE_NUMBER_INT) : "";
    $settings['EMAIL_TEMPLATE_LOGO'] = filter_var(trim($_POST['EMAIL_TEMPLATE_LOGO']), FILTER_SANITIZE_STRING);
    $settings['ADDRESS'] = filter_var(trim($_POST['ADDRESS']), FILTER_SANITIZE_STRING);
    $settings['COUNTRY'] = filter_var(trim($_POST['COUNTRY']), FILTER_SANITIZE_STRING);
    $settings['STATE'] = filter_var(trim($_POST['STATE']), FILTER_SANITIZE_STRING);
    $settings['CITY'] = filter_var(trim($_POST['CITY']), FILTER_SANITIZE_STRING);
    $settings['PINCODE'] = filter_var(trim($_POST['PINCODE']), FILTER_SANITIZE_STRING);
    $settings['EMAIL'] = filter_var(trim($_POST['EMAIL']), FILTER_SANITIZE_STRING);
    $settings['PHONE'] = filter_var(trim($_POST['PHONE']), FILTER_SANITIZE_STRING);
    
    if (saveAllConfig($settings)) {
        $updatemsg = '<div class="alert alert-success">General Settings saved successfully!</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">There is some problem!</div>';
    }
}
//update product settings
if (isset($_POST['updateproductsettings']) && isUserHavePermission(PORTAL_SETTINGS_SECTION, getUserLoggedId())) {
    $settings['PRODUCT_MIN_PRICE'] = filter_var(trim($_POST['PRODUCT_MIN_PRICE']), FILTER_SANITIZE_STRING);
    $settings['PRODUCT_MAXIMUM_COMMISSION'] = filter_var(trim($_POST['PRODUCT_MAXIMUM_COMMISSION']), FILTER_SANITIZE_STRING);
    $settings['PRODUCT_ALLOWED_FILE_EXTN'] = filter_var(trim($_POST['PRODUCT_ALLOWED_FILE_EXTN']), FILTER_SANITIZE_STRING);
    $settings['PRODUCT_MAX_FILE_SIZE'] = filter_var(trim($_POST['PRODUCT_MAX_FILE_SIZE']), FILTER_SANITIZE_NUMBER_INT);
    $settings['PRODUCT_DOWNLOAD_ENABLE'] = isset($_POST['PRODUCT_DOWNLOAD_ENABLE']) ? implode(",", $_POST['PRODUCT_DOWNLOAD_ENABLE']) : "";
    $settings['PRODUCT_ALLOW_REVIEWS'] = isset($_POST['PRODUCT_ALLOW_REVIEWS']) ? filter_var(trim($_POST['PRODUCT_ALLOW_REVIEWS']), FILTER_SANITIZE_STRING) : 'N';
    $settings['PRODUCT_REVIEWS_ALERT'] = isset($_POST['PRODUCT_REVIEWS_ALERT']) ? filter_var(trim($_POST['PRODUCT_REVIEWS_ALERT']), FILTER_SANITIZE_STRING) : 'N';
    $settings['PRODUCT_REVIEWS_DEFAULT_STATUS'] = filter_var(trim($_POST['PRODUCT_REVIEWS_DEFAULT_STATUS']), FILTER_SANITIZE_STRING);

    if (saveAllConfig($settings)) {
        $updatemsg = '<div class="alert alert-success">Product Settings saved successfully!</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">There is some problem!</div>';
    }
}

//update cart settings
if (isset($_POST['updatecartsettings']) && isUserHavePermission(PORTAL_SETTINGS_SECTION, getUserLoggedId())) {
    $settings['CART_ABANDONED_EMAIL'] = isset($_POST['CART_ABANDONED_EMAIL']) ? filter_var(trim($_POST['CART_ABANDONED_EMAIL']), FILTER_SANITIZE_STRING) : 'N';
    $settings['CART_ABANDONED_EMAIL_HRS'] = filter_var(trim($_POST['CART_ABANDONED_EMAIL_HRS']), FILTER_SANITIZE_NUMBER_INT);
    $settings['WISHLIST_ITEMS_EMAIL'] = isset($_POST['WISHLIST_ITEMS_EMAIL']) ? filter_var(trim($_POST['WISHLIST_ITEMS_EMAIL']), FILTER_SANITIZE_STRING) : 'N';
    $settings['WISHLIST_ITEMS_EMAIL_HRS'] = filter_var(trim($_POST['WISHLIST_ITEMS_EMAIL_HRS']), FILTER_SANITIZE_STRING);

    if (saveAllConfig($settings)) {
        $updatemsg = '<div class="alert alert-success">Cart Settings saved successfully!</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">There is some problem!</div>';
    }
}

//update checkout settings
if (isset($_POST['updatecheckoutsettings']) && isUserHavePermission(PORTAL_SETTINGS_SECTION, getUserLoggedId())) {
    $settings['CHECKOUT_CHECK_STOCK'] = isset($_POST['CHECKOUT_CHECK_STOCK']) ? filter_var(trim($_POST['CHECKOUT_CHECK_STOCK']), FILTER_SANITIZE_STRING) : 'N';
    $settings['CHECKOUT_ALLOW_ON_NO_STOCK'] = isset($_POST['CHECKOUT_ALLOW_ON_NO_STOCK']) ? filter_var(trim($_POST['CHECKOUT_ALLOW_ON_NO_STOCK']), FILTER_SANITIZE_STRING) : 'N';

    if (saveAllConfig($settings)) {
        $updatemsg = '<div class="alert alert-success">Checkout Settings saved successfully!</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">There is some problem!</div>';
    }
}

//update order settings
if (isset($_POST['updateordersettings']) && isUserHavePermission(PORTAL_SETTINGS_SECTION, getUserLoggedId())) {
    $settings['ORDER_EMAIL_ALERT_SELLER'] = isset($_POST['ORDER_EMAIL_ALERT_SELLER']) ? filter_var(trim($_POST['ORDER_EMAIL_ALERT_SELLER']), FILTER_SANITIZE_STRING) : 'N';
    $settings['ORDER_REFUND_FORM'] = isset($_POST['ORDER_REFUND_FORM']) ? filter_var(trim($_POST['ORDER_REFUND_FORM']), FILTER_SANITIZE_STRING) : 'A_C';
    $settings['ORDER_DEFAULT_STATUS'] = filter_var(trim($_POST['ORDER_DEFAULT_STATUS']), FILTER_SANITIZE_STRING);
    $settings['ORDER_PAID_DEFAULT_STATUS'] = filter_var(trim($_POST['ORDER_PAID_DEFAULT_STATUS']), FILTER_SANITIZE_STRING);
    $settings['ORDER_SHIPPING_DEFAULT_STATUS'] = filter_var(trim($_POST['ORDER_SHIPPING_DEFAULT_STATUS']), FILTER_SANITIZE_STRING);
    $settings['ORDER_DELIVERED_DEFAULT_STATUS'] = filter_var(trim($_POST['ORDER_DELIVERED_DEFAULT_STATUS']), FILTER_SANITIZE_STRING);
    $settings['ORDER_CANCELLED_DEFAULT_STATUS'] = filter_var(trim($_POST['ORDER_CANCELLED_DEFAULT_STATUS']), FILTER_SANITIZE_STRING);
    $settings['ORDER_RETURN_REQUESTED_DEFAULT_STATUS'] = filter_var(trim($_POST['ORDER_RETURN_REQUESTED_DEFAULT_STATUS']), FILTER_SANITIZE_STRING);
    $settings['ORDER_RETURN_REQUEST_WIDHDRAWN_DEFAULT_STATUS'] = filter_var(trim($_POST['ORDER_RETURN_REQUEST_WIDHDRAWN_DEFAULT_STATUS']), FILTER_SANITIZE_STRING);
    $settings['ORDER_RETURN_REQUEST_APPROVED_DEFAULT_STATUS'] = filter_var(trim($_POST['ORDER_RETURN_REQUEST_APPROVED_DEFAULT_STATUS']), FILTER_SANITIZE_STRING);
    $settings['ORDER_COD_ENABLED'] = isset($_POST['ORDER_COD_ENABLED']) ? filter_var(trim($_POST['ORDER_COD_ENABLED']), FILTER_SANITIZE_STRING) : 'N';
    $settings['ORDER_COD_MINIMUM'] = filter_var(trim($_POST['ORDER_COD_MINIMUM']), FILTER_SANITIZE_STRING);
    $settings['ORDER_COD_MAXIMUM'] = filter_var(trim($_POST['ORDER_COD_MAXIMUM']), FILTER_SANITIZE_STRING);
    $settings['ORDER_SELLER_WALLET_MINIMUM_FOR_COD'] = filter_var(trim($_POST['ORDER_SELLER_WALLET_MINIMUM_FOR_COD']), FILTER_SANITIZE_STRING);
    $settings['ORDER_SELLER_WALLET_MINIMUM_NOTIFY'] = isset($_POST['ORDER_SELLER_WALLET_MINIMUM_NOTIFY']) ? 'Y' : 'N';
    $settings['ORDER_COD_PAYMENT_METHOD'] = filter_var(trim($_POST['ORDER_COD_PAYMENT_METHOD']), FILTER_SANITIZE_STRING);
    $settings['ORDER_COD_DEFAULT_STATUS'] = filter_var(trim($_POST['ORDER_COD_DEFAULT_STATUS']), FILTER_SANITIZE_STRING);
    $settings['ORDER_SELLER_STATUSES'] = isset($_POST['ORDER_SELLER_STATUSES']) ? implode(",", $_POST['ORDER_SELLER_STATUSES']) : '';
    $settings['ORDER_BUYER_STATUSES'] = isset($_POST['ORDER_BUYER_STATUSES']) ? implode(",", $_POST['ORDER_BUYER_STATUSES']) : '';
    $settings['ORDER_SUBTRACT_STATUSES'] = isset($_POST['ORDER_SUBTRACT_STATUSES']) ? implode(",", $_POST['ORDER_SUBTRACT_STATUSES']) : '';
    $settings['ORDER_COMPLETED_STATUSES'] = isset($_POST['ORDER_COMPLETED_STATUSES']) ? implode(",", $_POST['ORDER_COMPLETED_STATUSES']) : '';
    $settings['ORDER_FEEDBACK_STATUSES'] = isset($_POST['ORDER_FEEDBACK_STATUSES']) ? implode(",", $_POST['ORDER_FEEDBACK_STATUSES']) : '';
    $settings['ORDER_CANCELLATION_BUYER_STATUSES'] = isset($_POST['ORDER_CANCELLATION_BUYER_STATUSES']) ? implode(",", $_POST['ORDER_CANCELLATION_BUYER_STATUSES']) : '';
    $settings['ORDER_RETURN_BUYER_STATUSES'] = isset($_POST['ORDER_RETURN_BUYER_STATUSES']) ? implode(",", $_POST['ORDER_RETURN_BUYER_STATUSES']) : '';
    $settings['ORDER_PURCHASE_BUYER_STATUSES'] = isset($_POST['ORDER_PURCHASE_BUYER_STATUSES']) ? implode(",", $_POST['ORDER_PURCHASE_BUYER_STATUSES']) : '';
    $settings['ORDER_AUTO_COMPLETE'] = isset($_POST['ORDER_AUTO_COMPLETE']) ? filter_var(trim($_POST['ORDER_AUTO_COMPLETE']), FILTER_SANITIZE_STRING) : 'N';
    $settings['ORDER_AUTO_COMPLETE_DAYS'] = filter_var(trim($_POST['ORDER_AUTO_COMPLETE_DAYS']), FILTER_SANITIZE_NUMBER_INT);

    if (saveAllConfig($settings)) {
        $updatemsg = '<div class="alert alert-success">Order Settings saved successfully!</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">There is some problem!</div>';
    }
}

//update account settings
if (isset($_POST['updateaccountsettings']) && isUserHavePermission(PORTAL_SETTINGS_SECTION, getUserLoggedId())) {
    $settings['ACCOUNT_ADMIN_APPROVE'] = isset($_POST['ACCOUNT_ADMIN_APPROVE']) ? 'Y' : 'N';
    $settings['ACCOUNT_EMAIL_VERIFICATION'] = isset($_POST['ACCOUNT_EMAIL_VERIFICATION']) ? 'Y' : 'N';
    $settings['ACCOUNT_OTP_VERIFICATION'] = isset($_POST['ACCOUNT_OTP_VERIFICATION']) ? 'Y' : 'N';
    $settings['ACCOUNT_EMAIL_VERIFY_THROUGH_API'] = isset($_POST['ACCOUNT_EMAIL_VERIFY_THROUGH_API']) ? 'Y' : 'N';
    $settings['ACCOUNT_AUTO_LOGIN'] = isset($_POST['ACCOUNT_AUTO_LOGIN']) ? 'Y' : 'N';
    $settings['ACCOUNT_NOTIFICATION_ADMIN'] = isset($_POST['ACCOUNT_NOTIFICATION_ADMIN']) ? 'Y' : 'N';
    $settings['ACCOUNT_WELCOME_EMAIL'] = isset($_POST['ACCOUNT_WELCOME_EMAIL']) ? 'Y' : 'N';
    $settings['ACCOUNT_AUTO_LOGOUT_AFTER_PWD_CHANGE'] = isset($_POST['ACCOUNT_AUTO_LOGOUT_AFTER_PWD_CHANGE']) ? 'Y' : 'N';
    $settings['ACCOUNT_SEPARATE_SIGN_UP'] = isset($_POST['ACCOUNT_SEPARATE_SIGN_UP']) ? 'Y' : 'N';
    $settings['ACCOUNT_ADMIN_APPROVE_SELLER'] = isset($_POST['ACCOUNT_ADMIN_APPROVE_SELLER']) ? 'Y' : 'N';
    $settings['ACCOUNT_BUYER_SEE_SELLER_TAB'] = isset($_POST['ACCOUNT_BUYER_SEE_SELLER_TAB']) ? 'Y' : 'N';
    $settings['ACCOUNT_FACEBOOK_LOGIN'] = isset($_POST['ACCOUNT_FACEBOOK_LOGIN']) ? 'Y' : 'N';
    $settings['ACCOUNT_GOOGLE_LOGIN'] = isset($_POST['ACCOUNT_GOOGLE_LOGIN']) ? 'Y' : 'N';
    $settings['ACCOUNT_TERM_PAGE'] = isset($_POST['ACCOUNT_TERM_PAGE']) ? filter_var(trim($_POST['ACCOUNT_TERM_PAGE']), FILTER_SANITIZE_NUMBER_INT) : '';
    $settings['ACCOUNT_MAX_LOGIN_ATTEMPTS'] = filter_var(trim($_POST['ACCOUNT_MAX_LOGIN_ATTEMPTS']), FILTER_SANITIZE_NUMBER_INT);

    if (saveAllConfig($settings)) {
        $updatemsg = '<div class="alert alert-success">Account Settings saved successfully!</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">There is some problem!</div>';
    }
}

//update live chat settings
if (isset($_POST['updatelivechatsettings']) && isUserHavePermission(PORTAL_SETTINGS_SECTION, getUserLoggedId())) {
    $settings['LIVE_CHAT_ENABLE'] = isset($_POST['LIVE_CHAT_ENABLE']) ? filter_var(trim($_POST['LIVE_CHAT_ENABLE']), FILTER_SANITIZE_STRING) : 'N';
    $settings['LIVE_CHAT_CODE'] = isset($_POST['LIVE_CHAT_CODE']) ? $_POST['LIVE_CHAT_CODE'] : '';

    if (saveAllConfig($settings)) {
        $updatemsg = '<div class="alert alert-success">Live Chat Settings saved successfully!</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">There is some problem!</div>';
    }
}

//update api settings
if (isset($_POST['updateapisettings']) && isUserHavePermission(PORTAL_SETTINGS_SECTION, getUserLoggedId())) {
    $settings['API_GOOGLE_KEY'] = isset($_POST['API_GOOGLE_KEY']) ? filter_var(trim($_POST['API_GOOGLE_KEY']), FILTER_SANITIZE_STRING) : '';
    $settings['API_GOOGLE_CLIENT_ID'] = isset($_POST['API_GOOGLE_CLIENT_ID']) ? $_POST['API_GOOGLE_CLIENT_ID'] : '';
    $settings['API_GOOGLE_CLIENT_SECRET'] = isset($_POST['API_GOOGLE_CLIENT_SECRET']) ? $_POST['API_GOOGLE_CLIENT_SECRET'] : '';
    $settings['API_GOOGLE_RECAPTCHA_SECRET_KEY'] = isset($_POST['API_GOOGLE_RECAPTCHA_SECRET_KEY']) ? $_POST['API_GOOGLE_RECAPTCHA_SECRET_KEY'] : '';
    $settings['API_GOOGLE_RECAPTCHA_SITE_KEY'] = isset($_POST['API_GOOGLE_RECAPTCHA_SITE_KEY']) ? $_POST['API_GOOGLE_RECAPTCHA_SITE_KEY'] : '';
    $settings['API_CURRENCYLAYER_ACCESS_KEY'] = isset($_POST['API_CURRENCYLAYER_ACCESS_KEY']) ? $_POST['API_CURRENCYLAYER_ACCESS_KEY'] : '';

    if (saveAllConfig($settings)) {
        $updatemsg = '<div class="alert alert-success">API Settings saved successfully!</div>';
    } else {
        $updatemsg = '<div class="alert alert-danger">There is some problem!</div>';
    }
}

$config = getConfig();
$pages = getPosts(array('id', 'post_title'), array('post_type' => "page"), 0, -1);
$pmethods = getPaymentMethods(array('id', 'name'), array('status' => 'A'));
$countries = getCountries(array('id', 'name'), array(), 0, -1);
$country_id = isset($config['COUNTRY']) ? $config['COUNTRY'] : "";
$states = getStates(array('id', 'name'), array('country_id' => $country_id));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Portal Settings - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link rel="stylesheet" href="<?= $sys['site_url'] ?>/admin/plugins/select2/select2.min.css">
        <?php include 'css.php'; ?>
        <style>
            .table td {
                padding: 5px !important;
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
                        Portal Settings
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li><a href="#">Settings</a></li>
                        <li class="active">Portal</li>
                    </ol>
                </section>
                <section class="content">

                    <div class="">
                        <form role="form" action="" method="post" enctype="multipart/form-data">
                            <?= $updatemsg ?>
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#general" data-toggle="tab">General</a></li>
                                    <li><a href="#product" data-toggle="tab">Product</a></li>
                                    <li><a href="#cart" data-toggle="tab">Cart</a></li>
                                    <li><a href="#checkout" data-toggle="tab">Checkout</a></li>
                                    <li><a href="#order" data-toggle="tab">Order</a></li>
                                    <li><a href="#account" data-toggle="tab">Account</a></li>
                                    <li><a href="#livechat" data-toggle="tab">Live Chat</a></li>
                                    <li><a href="#thirdparty" data-toggle="tab">Third Party APIs</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="general">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Privacy Policy</label>
                                                    <select name="PRIVACY_POLICY" class="form-control">
                                                        <?php foreach ($pages as $page) { ?>
                                                            <option value="<?= $page['id'] ?>" <?= isset($config['PRIVACY_POLICY']) && $config['PRIVACY_POLICY'] == $page['id'] ? 'selected' : '' ?>><?= $page['post_title'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="EMAIL_TEMPLATE_LOGO">Email Template Logo</label>
                                                    <img id="EMAIL_TEMPLATE_LOGO_IMG" src="<?= isset($config['EMAIL_TEMPLATE_LOGO']) && trim($config['EMAIL_TEMPLATE_LOGO']) <> "" ? $config['EMAIL_TEMPLATE_LOGO'] : 'http://via.placeholder.com/172x55' ?>" class="img-responsive"/>
                                                    <input type="hidden" id="EMAIL_TEMPLATE_LOGO" name="EMAIL_TEMPLATE_LOGO" value="<?= isset($config['EMAIL_TEMPLATE_LOGO']) && trim($config['EMAIL_TEMPLATE_LOGO']) <> "" ? $config['EMAIL_TEMPLATE_LOGO'] : '' ?>"/>
                                                    <input type="file" class="form-control" id="imguploadinput"/>
                                                    <span id="uploadingspanmsg"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Address</label>
                                                    <input type="text" class="form-control" name="ADDRESS" value="<?= isset($config['ADDRESS']) && trim($config['ADDRESS']) <> "" ? $config['ADDRESS'] : '' ?>"/> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">                                        
                                                    <label>Country</label>
                                                    <select name="COUNTRY" class="form-control countryforaddress" required>
                                                        <option value="">-Select Country-</option>
                                                        <?php foreach ($countries as $c) { ?>
                                                            <option value="<?= $c['id'] ?>" <?= isset($config['COUNTRY']) && $config['COUNTRY'] == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">                                        
                                                    <label>State</label>
                                                    <select class="form-control stateforaddress" name="STATE">
                                                        <option value="">-Select State-</option>
                                                        <?php foreach ($states as $s) { ?>
                                                            <option value="<?= $s['id'] ?>" <?= isset($config['STATE']) && $config['STATE'] == $s['id'] ? 'selected' : '' ?>><?= $s['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">                                        
                                                    <label>City</label>
                                                    <input type="text" class="form-control" name="CITY" value="<?= isset($config['CITY']) && trim($config['CITY']) <> "" ? $config['CITY'] : '' ?>"/> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Pincode</label>
                                                    <input type="text" class="form-control" name="PINCODE" value="<?= isset($config['CITY']) && trim($config['CITY']) <> "" ? $config['CITY'] : '' ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="text" class="form-control" name="EMAIL" value="<?= isset($config['EMAIL']) && trim($config['EMAIL']) <> "" ? $config['EMAIL'] : '' ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label>Phone</label>
                                                    <input type="text" class="form-control" name="PHONE" value="<?= isset($config['PHONE']) && trim($config['PHONE']) <> "" ? $config['PHONE'] : '' ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="update" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="product">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="PRODUCT_MIN_PRICE">Product's Minimum Price</label>
                                                    <input type="text" class="form-control" id="PRODUCT_MIN_PRICE" name="PRODUCT_MIN_PRICE" value="<?= isset($config['PRODUCT_MIN_PRICE']) ? $config['PRODUCT_MIN_PRICE'] : '' ?>"/>
                                                    This is Product's Minimum Price allowed for listing.
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Maximum Commission</label><br/>
                                                    <input type="text" class="form-control" name="PRODUCT_MAXIMUM_COMMISSION" value="<?= isset($config['PRODUCT_MAXIMUM_COMMISSION']) ? $config['PRODUCT_MAXIMUM_COMMISSION'] : '' ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label for="PRODUCT_ALLOWED_FILE_EXTN">Allowed File Extensions for Digital Products</label>
                                                    <textarea name="PRODUCT_ALLOWED_FILE_EXTN" class="form-control"><?= isset($config['PRODUCT_ALLOWED_FILE_EXTN']) ? $config['PRODUCT_ALLOWED_FILE_EXTN'] : '' ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label for="PRODUCT_MAX_FILE_SIZE">Max File Size for Digital Products</label>
                                                    <input id='PRODUCT_MAX_FILE_SIZE' name="PRODUCT_MAX_FILE_SIZE" value="<?= isset($config['PRODUCT_MAX_FILE_SIZE']) ? $config['PRODUCT_MAX_FILE_SIZE'] : '' ?>" class="form-control"/>
                                                    The maximum file size you can upload. Enter as byte. Maximim 6291456 byte(s) allowed as per your hosting/server settings.
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Enable Downloads for Digital Products</label>
                                                    <?php
                                                    $pde = isset($config['PRODUCT_DOWNLOAD_ENABLE']) ? explode(",", $config['PRODUCT_DOWNLOAD_ENABLE']) : array();
                                                    ?>
                                                    <table class="table table-bordered" style="margin-bottom: 0px;">
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="Payment Pending" <?= in_array('Payment Pending', $pde) ? 'checked' : '' ?>/>
                                                                Payment Pending
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="Payment Confirmed" <?= in_array('Payment Confirmed', $pde) ? 'checked' : '' ?>/>
                                                                Payment Confirmed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="Cash on Delivery" <?= in_array('Cash on Delivery', $pde) ? 'checked' : '' ?>/>
                                                                Cash on Delivery
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="Approved" <?= in_array('Approved', $pde) ? 'checked' : '' ?>/>
                                                                Approved
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="In Process" <?= in_array('In Process', $pde) ? 'checked' : '' ?>/>
                                                                In Process
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="Shipped" <?= in_array('Shipped', $pde) ? 'checked' : '' ?>/>
                                                                Shipped
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="Delivered" <?= in_array('Delivered', $pde) ? 'checked' : '' ?>/>
                                                                Delivered
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="Return Requested" <?= in_array('Return Requested', $pde) ? 'checked' : '' ?>/>
                                                                Return Requested
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="Completed" <?= in_array('Completed', $pde) ? 'checked' : '' ?>/>
                                                                Completed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="Cancelled" <?= in_array('Cancelled', $pde) ? 'checked' : '' ?>/>
                                                                Cancelled
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="PRODUCT_DOWNLOAD_ENABLE[]" value="Refunded/Completed" <?= in_array('Refunded/Completed', $pde) ? 'checked' : '' ?>/>
                                                                Refunded/Completed
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    Set the order status the customer's order must reach before they are allowed to access their downloadable products.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">                                        
                                                    <label>Allow Reviews</label><br/>
                                                    <input type="radio" name="PRODUCT_ALLOW_REVIEWS" value="N" <?= isset($config['PRODUCT_ALLOW_REVIEWS']) && $config['PRODUCT_ALLOW_REVIEWS'] == 'N' ? 'checked' : '' ?>/> No
                                                    <input type="radio" name="PRODUCT_ALLOW_REVIEWS" value="Y" <?= isset($config['PRODUCT_ALLOW_REVIEWS']) && $config['PRODUCT_ALLOW_REVIEWS'] == 'Y' ? 'checked' : '' ?>/> Yes
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">                                        
                                                    <label>New Review Alert Email</label><br/>
                                                    <input type="radio" name="PRODUCT_REVIEWS_ALERT" value="N" <?= isset($config['PRODUCT_REVIEWS_ALERT']) && $config['PRODUCT_REVIEWS_ALERT'] == 'N' ? 'checked' : '' ?>/> No
                                                    <input type="radio" name="PRODUCT_REVIEWS_ALERT" value="Y" <?= isset($config['PRODUCT_REVIEWS_ALERT']) && $config['PRODUCT_REVIEWS_ALERT'] == 'Y' ? 'checked' : '' ?>/> Yes
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Default Review Status</label><br/>
                                                    <select name="PRODUCT_REVIEWS_DEFAULT_STATUS" class="form-control">
                                                        <option value="P">Pending</option>
                                                        <option value="A">Approved</option>
                                                        <option value="T">Cancelled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="updateproductsettings" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="cart">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Send Abandoned Cart Email</label><br/>
                                                    <input type="radio" name="CART_ABANDONED_EMAIL" value="N" <?= isset($config['CART_ABANDONED_EMAIL']) && $config['CART_ABANDONED_EMAIL'] == 'N' ? 'checked' : '' ?>/> No
                                                    <input type="radio" name="CART_ABANDONED_EMAIL" value="Y" <?= isset($config['CART_ABANDONED_EMAIL']) && $config['CART_ABANDONED_EMAIL'] == 'Y' ? 'checked' : '' ?>/> Yes
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Number of Hours</label><br/>
                                                    <input type="number" class="form-control" name="CART_ABANDONED_EMAIL_HRS" value="<?= isset($config['CART_ABANDONED_EMAIL_HRS']) ? $config['CART_ABANDONED_EMAIL_HRS'] : '' ?>"/>
                                                    <i>Enter the number of Hrs after which you wish to send email.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Send Wishlist Items Email</label><br/>
                                                    <input type="radio" name="WISHLIST_ITEMS_EMAIL" value="N" <?= isset($config['WISHLIST_ITEMS_EMAIL']) && $config['WISHLIST_ITEMS_EMAIL'] == 'N' ? 'checked' : '' ?>/> No
                                                    <input type="radio" name="WISHLIST_ITEMS_EMAIL" value="Y" <?= isset($config['WISHLIST_ITEMS_EMAIL']) && $config['WISHLIST_ITEMS_EMAIL'] == 'Y' ? 'checked' : '' ?>/> Yes
                                                    <br/>
                                                    <i>Enable Sending Wishlist items email to customers.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Number of Hours</label><br/>
                                                    <input type="number" class="form-control" name="WISHLIST_ITEMS_EMAIL_HRS" value="<?= isset($config['WISHLIST_ITEMS_EMAIL_HRS']) ? $config['WISHLIST_ITEMS_EMAIL_HRS'] : '' ?>"/>
                                                    <i>Enter the number of Hrs after which you wish to send wishlist email.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="updatecartsettings" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="checkout">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Check Stock</label><br/>
                                                    <input type="radio" name="CHECKOUT_CHECK_STOCK" value="N" <?= isset($config['CHECKOUT_CHECK_STOCK']) && $config['CHECKOUT_CHECK_STOCK'] == 'N' ? 'checked' : '' ?>/> No
                                                    <input type="radio" name="CHECKOUT_CHECK_STOCK" value="Y" <?= isset($config['CHECKOUT_CHECK_STOCK']) && $config['CHECKOUT_CHECK_STOCK'] == 'Y' ? 'checked' : '' ?>/> Yes
                                                    <br/>
                                                    <i>Display out of stock message on the shopping cart page if a product is out of stock.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Allow Checkout</label><br/>
                                                    <input type="radio" name="CHECKOUT_ALLOW_ON_NO_STOCK" value="N" <?= isset($config['CHECKOUT_ALLOW_ON_NO_STOCK']) && $config['CHECKOUT_ALLOW_ON_NO_STOCK'] == 'N' ? 'checked' : '' ?>/> No
                                                    <input type="radio" name="CHECKOUT_ALLOW_ON_NO_STOCK" value="Y" <?= isset($config['CHECKOUT_ALLOW_ON_NO_STOCK']) && $config['CHECKOUT_ALLOW_ON_NO_STOCK'] == 'Y' ? 'checked' : '' ?>/> Yes
                                                    <br/>
                                                    <i>Allow customers to still checkout if the products they are ordering are not in stock.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="updatecheckoutsettings" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="order">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>New Order Alert Email</label><br/>
                                                    <input type="radio" name="ORDER_EMAIL_ALERT_SELLER" value="N" <?= isset($config['ORDER_EMAIL_ALERT_SELLER']) && $config['ORDER_EMAIL_ALERT_SELLER'] == 'N' ? 'checked' : '' ?>/> No
                                                    <input type="radio" name="ORDER_EMAIL_ALERT_SELLER" value="Y" <?= isset($config['ORDER_EMAIL_ALERT_SELLER']) && $config['ORDER_EMAIL_ALERT_SELLER'] == 'Y' ? 'checked' : '' ?>/> Yes
                                                    <br/>
                                                    <i>Send an email to store owner when new order is placed.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Order Cancellation/Refund in form of</label><br/>
                                                    <input type="radio" name="ORDER_REFUND_FORM" value="A_C" <?= isset($config['ORDER_REFUND_FORM']) && $config['ORDER_REFUND_FORM'] == 'A_C' ? 'checked' : '' ?>/> Credits
                                                    <input type="radio" name="ORDER_REFUND_FORM" value="R_P" <?= isset($config['ORDER_REFUND_FORM']) && $config['ORDER_REFUND_FORM'] == 'R_P' ? 'checked' : '' ?>/> Reward Points
                                                    <br/>
                                                    <i>These both are equivalent and can be used at the time of checkout but reward points can't be withdrawn while credits can be withdrawn.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Default Order Status</label><br/>
                                                    <select name="ORDER_DEFAULT_STATUS" class="form-control">
                                                        <?php foreach($ORDER_STATUSES as $k => $v) { ?>
                                                        <option value="<?= $k ?>" <?= isset($config['ORDER_DEFAULT_STATUS']) && $config['ORDER_DEFAULT_STATUS'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <i>Set the default order status when an order is placed.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Default Paid Order Status</label><br/>
                                                    <select name="ORDER_PAID_DEFAULT_STATUS" class="form-control">
                                                        <?php foreach($ORDER_STATUSES as $k => $v) { ?>
                                                        <option value="<?= $k ?>" <?= isset($config['ORDER_PAID_DEFAULT_STATUS']) && $config['ORDER_PAID_DEFAULT_STATUS'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <i>Set the default order status when an order is marked Paid.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Default Shipping Order Status</label><br/>
                                                    <select name="ORDER_SHIPPING_DEFAULT_STATUS" class="form-control">
                                                        <?php foreach($ORDER_STATUSES as $k => $v) { ?>
                                                        <option value="<?= $k ?>" <?= isset($config['ORDER_SHIPPING_DEFAULT_STATUS']) && $config['ORDER_SHIPPING_DEFAULT_STATUS'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <i>Set the default order status when an order is marked Shipped.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Default Delivered Order Status</label><br/>
                                                    <select name="ORDER_DELIVERED_DEFAULT_STATUS" class="form-control">
                                                        <?php foreach($ORDER_STATUSES as $k => $v) { ?>
                                                        <option value="<?= $k ?>" <?= isset($config['ORDER_DELIVERED_DEFAULT_STATUS']) && $config['ORDER_DELIVERED_DEFAULT_STATUS'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <i>Set the default order status when an order is marked delivered.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Default Cancelled Order Status</label><br/>
                                                    <select name="ORDER_CANCELLED_DEFAULT_STATUS" class="form-control">
                                                        <?php foreach($ORDER_STATUSES as $k => $v) { ?>
                                                        <option value="<?= $k ?>" <?= isset($config['ORDER_CANCELLED_DEFAULT_STATUS']) && $config['ORDER_CANCELLED_DEFAULT_STATUS'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <i>Set the default order status when an order is marked Cancelled.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Return Requested Order Status</label><br/>
                                                    <select name="ORDER_RETURN_REQUESTED_DEFAULT_STATUS" class="form-control">
                                                        <?php foreach($ORDER_STATUSES as $k => $v) { ?>
                                                        <option value="<?= $k ?>" <?= isset($config['ORDER_RETURN_REQUESTED_DEFAULT_STATUS']) && $config['ORDER_RETURN_REQUESTED_DEFAULT_STATUS'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <i>Set the default order status when return request is opened on any order.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Return Request Withdrawn Order Status</label><br/>
                                                    <select name="ORDER_RETURN_REQUEST_WIDHDRAWN_DEFAULT_STATUS" class="form-control">
                                                        <?php foreach($ORDER_STATUSES as $k => $v) { ?>
                                                        <option value="<?= $k ?>" <?= isset($config['ORDER_RETURN_REQUEST_WIDHDRAWN_DEFAULT_STATUS']) && $config['ORDER_RETURN_REQUEST_WIDHDRAWN_DEFAULT_STATUS'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <i>Set the default order status when return request is withdrawn.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Return Request Approved Order Status</label><br/>
                                                    <select name="ORDER_RETURN_REQUEST_APPROVED_DEFAULT_STATUS" class="form-control">
                                                        <?php foreach($ORDER_STATUSES as $k => $v) { ?>
                                                        <option value="<?= $k ?>" <?= isset($config['ORDER_RETURN_REQUEST_APPROVED_DEFAULT_STATUS']) && $config['ORDER_RETURN_REQUEST_APPROVED_DEFAULT_STATUS'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <i>Set the default order status when return request is accepted by the vendor.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ORDER_COD_ENABLED">Enable COD</label><br/>
                                                    <input type="checkbox" id="COD_ENABLED" name="ORDER_COD_ENABLED" value="Y" <?= isset($config['ORDER_COD_ENABLED']) && $config['ORDER_COD_ENABLED'] == 'Y' ? 'checked' : '' ?>/> Yes
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label for="ORDER_COD_MINIMUM">Minimum COD Order Total</label><br/>
                                                    <input type="text" class="form-control" id="ORDER_COD_MINIMUM" name="ORDER_COD_MINIMUM" value="<?= isset($config['ORDER_COD_MINIMUM']) ? $config['ORDER_COD_MINIMUM'] : '' ?>"/>
                                                    This is the minimum cash on delivery order total, eligible for COD payments.
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label for="ORDER_COD_MAXIMUM">Maximum COD Order Total</label>
                                                    <input type="text" class="form-control" id="ORDER_COD_MAXIMUM" name="ORDER_COD_MAXIMUM" value="<?= isset($config['ORDER_COD_MAXIMUM']) ? $config['ORDER_COD_MAXIMUM'] : '' ?>"/>
                                                    This is the maximum cash on delivery order total, eligible for COD payments.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Minimum Wallet Balance</label>
                                                    <input type="text" class="form-control" name="ORDER_SELLER_WALLET_MINIMUM_FOR_COD" value="<?= isset($config['ORDER_SELLER_WALLET_MINIMUM_FOR_COD']) ? $config['ORDER_SELLER_WALLET_MINIMUM_FOR_COD'] : '' ?>"/>
                                                    This is the minimum wallet balance, seller needs to maintain to accept COD orders.
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Notify Seller</label><br/>
                                                    <input type="checkbox" name="ORDER_SELLER_WALLET_MINIMUM_NOTIFY" value="Y" <?= isset($config['ORDER_SELLER_WALLET_MINIMUM_NOTIFY']) ? 'checked' : '' ?>/>
                                                    If enabled, this will keep seller informed if balance goes below Minimum wallet balance required to accept COD orders.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Payment Method for COD</label>
                                                    <select name="ORDER_COD_PAYMENT_METHOD" class="form-control">
                                                        <?php foreach($pmethods as $pm) { ?>
                                                        <option value="<?= $pm['id'] ?>" <?= isset($config['ORDER_COD_PAYMENT_METHOD']) && $config['ORDER_COD_PAYMENT_METHOD'] == $pm['id'] ? 'selected' : ''  ?>><?= $pm['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    Select the Payment Method to be considered as COD (cash on delivery).
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Default COD Order Status</label>
                                                    <select name="ORDER_COD_DEFAULT_STATUS" class="form-control">
                                                        <?php foreach($ORDER_STATUSES as $k => $v) { ?>
                                                        <option value="<?= $k ?>" <?= isset($config['ORDER_COD_DEFAULT_STATUS']) && $config['ORDER_COD_DEFAULT_STATUS'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    If enabled, this will keep seller informed if balance goes below Minimum wallet balance required to accept COD orders.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Vendor Order Status</label>
                                                    <?php 
                                                    $ovss = isset($config['ORDER_SELLER_STATUSES']) ? explode(",", $config['ORDER_SELLER_STATUSES']) : array();
                                                    ?>
                                                    <table class="table table-bordered" style="margin-bottom: 0px;">
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="Payment Pending" <?= in_array('Payment Pending', $ovss) ? 'checked' : '' ?>/>
                                                                Payment Pending
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="Payment Confirmed" <?= in_array('Payment Confirmed', $ovss) ? 'checked' : '' ?>/>
                                                                Payment Confirmed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="Cash on Delivery" <?= in_array('Cash on Delivery', $ovss) ? 'checked' : '' ?>/>
                                                                Cash on Delivery
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="Approved" <?= in_array('Approved', $ovss) ? 'checked' : '' ?>/>
                                                                Approved
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="In Process" <?= in_array('In Process', $ovss) ? 'checked' : '' ?>/>
                                                                In Process
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="Shipped" <?= in_array('Shipped', $ovss) ? 'checked' : '' ?>/>
                                                                Shipped
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="Delivered" <?= in_array('Delivered', $ovss) ? 'checked' : '' ?>/>
                                                                Delivered
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="Return Requested" <?= in_array('Return Requested', $ovss) ? 'checked' : '' ?>/>
                                                                Return Requested
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="Completed" <?= in_array('Completed', $ovss) ? 'checked' : '' ?>/>
                                                                Completed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="Cancelled" <?= in_array('Cancelled', $ovss) ? 'checked' : '' ?>/>
                                                                Cancelled
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SELLER_STATUSES[]" value="Refunded/Completed" <?= in_array('Refunded/Completed', $ovss) ? 'checked' : '' ?>/>
                                                                Refunded/Completed
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <i>Set the order status the customer's order must reach before the order starts displaying to Sellers.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Buyer Order Status</label>
                                                    <?php 
                                                    $obss = isset($config['ORDER_BUYER_STATUSES']) ? explode(",", $config['ORDER_BUYER_STATUSES']) : array();
                                                    ?>
                                                    <table class="table table-bordered" style="margin-bottom: 0px;">
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="Payment Pending" <?= in_array('Payment Pending', $obss) ? 'checked' : '' ?>/>
                                                                Payment Pending
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="Payment Confirmed" <?= in_array('Payment Confirmed', $obss) ? 'checked' : '' ?>/>
                                                                Payment Confirmed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="Cash on Delivery" <?= in_array('Cash on Delivery', $obss) ? 'checked' : '' ?>/>
                                                                Cash on Delivery
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="Approved" <?= in_array('Approved', $obss) ? 'checked' : '' ?>/>
                                                                Approved
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="In Process" <?= in_array('In Process', $obss) ? 'checked' : '' ?>/>
                                                                In Process
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="Shipped" <?= in_array('Shipped', $obss) ? 'checked' : '' ?>/>
                                                                Shipped
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="Delivered" <?= in_array('Delivered', $obss) ? 'checked' : '' ?>/>
                                                                Delivered
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="Return Requested" <?= in_array('Return Requested', $obss) ? 'checked' : '' ?>/>
                                                                Return Requested
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="Completed" <?= in_array('Completed', $obss) ? 'checked' : '' ?>/>
                                                                Completed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="Cancelled" <?= in_array('Cancelled', $obss) ? 'checked' : '' ?>/>
                                                                Cancelled
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_BUYER_STATUSES[]" value="Refunded/Completed" <?= in_array('Refunded/Completed', $obss) ? 'checked' : '' ?>/>
                                                                Refunded/Completed
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <i>Set the order status the customer's order must reach before the order starts displaying to Buyers</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Stock Subtraction Order Status (Processing)</label>
                                                    <?php 
                                                    $oss = isset($config['ORDER_SUBTRACT_STATUSES']) ? explode(",", $config['ORDER_SUBTRACT_STATUSES']) : array();
                                                    ?>
                                                    <table class="table table-bordered" style="margin-bottom: 0px;">
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="Payment Pending" <?= in_array('Payment Pending', $oss) ? 'checked' : '' ?>/>
                                                                Payment Pending
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="Payment Confirmed" <?= in_array('Payment Confirmed', $oss) ? 'checked' : '' ?>/>
                                                                Payment Confirmed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="Cash on Delivery" <?= in_array('Cash on Delivery', $oss) ? 'checked' : '' ?>/>
                                                                Cash on Delivery
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="Approved" <?= in_array('Approved', $oss) ? 'checked' : '' ?>/>
                                                                Approved
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="In Process" <?= in_array('In Process', $oss) ? 'checked' : '' ?>/>
                                                                In Process
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="Shipped" <?= in_array('Shipped', $oss) ? 'checked' : '' ?>/>
                                                                Shipped
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="Delivered" <?= in_array('Delivered', $oss) ? 'checked' : '' ?>/>
                                                                Delivered
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="Return Requested" <?= in_array('Return Requested', $oss) ? 'checked' : '' ?>/>
                                                                Return Requested
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="Completed" <?= in_array('Completed', $oss) ? 'checked' : '' ?>/>
                                                                Completed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="Cancelled" <?= in_array('Cancelled', $oss) ? 'checked' : '' ?>/>
                                                                Cancelled
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_SUBTRACT_STATUSES[]" value="Refunded/Completed" <?= in_array('Refunded/Completed', $oss) ? 'checked' : '' ?>/>
                                                                Refunded/Completed
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <i>Set the order status the customer's order must reach before the order starts stock subtraction.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Completed Order Status</label>
                                                    <?php 
                                                    $ocs = isset($config['ORDER_COMPLETED_STATUSES']) ? explode(",", $config['ORDER_COMPLETED_STATUSES']) : array();
                                                    ?>
                                                    <table class="table table-bordered" style="margin-bottom: 0px;">
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="Payment Pending" <?= in_array('Payment Pending', $ocs) ? 'checked' : '' ?>/>
                                                                Payment Pending
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="Payment Confirmed" <?= in_array('Payment Confirmed', $ocs) ? 'checked' : '' ?>/>
                                                                Payment Confirmed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="Cash on Delivery" <?= in_array('Cash on Delivery', $ocs) ? 'checked' : '' ?>/>
                                                                Cash on Delivery
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="Approved" <?= in_array('Approved', $ocs) ? 'checked' : '' ?>/>
                                                                Approved
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="In Process" <?= in_array('In Process', $ocs) ? 'checked' : '' ?>/>
                                                                In Process
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="Shipped" <?= in_array('Shipped', $ocs) ? 'checked' : '' ?>/>
                                                                Shipped
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="Delivered" <?= in_array('Delivered', $ocs) ? 'checked' : '' ?>/>
                                                                Delivered
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="Return Requested" <?= in_array('Return Requested', $ocs) ? 'checked' : '' ?>/>
                                                                Return Requested
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="Completed" <?= in_array('Completed', $ocs) ? 'checked' : '' ?>/>
                                                                Completed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="Cancelled" <?= in_array('Cancelled', $ocs) ? 'checked' : '' ?>/>
                                                                Cancelled
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_COMPLETED_STATUSES[]" value="Refunded/Completed" <?= in_array('Refunded/Completed', $ocs) ? 'checked' : '' ?>/>
                                                                Refunded/Completed
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <i>Set the order status the customer's order must reach before they are considered completed and payment released to vendors.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Feedback ready Order Status</label>
                                                    <?php 
                                                    $ofs = isset($config['ORDER_FEEDBACK_STATUSES']) ? explode(",", $config['ORDER_FEEDBACK_STATUSES']) : array();
                                                    ?>
                                                    <table class="table table-bordered" style="margin-bottom: 0px;">
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="Payment Pending" <?= in_array('Payment Pending', $ofs) ? 'checked' : '' ?>/>
                                                                Payment Pending
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="Payment Confirmed" <?= in_array('Payment Confirmed', $ofs) ? 'checked' : '' ?>/>
                                                                Payment Confirmed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="Cash on Delivery" <?= in_array('Cash on Delivery', $ofs) ? 'checked' : '' ?>/>
                                                                Cash on Delivery
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="Approved" <?= in_array('Approved', $ofs) ? 'checked' : '' ?>/>
                                                                Approved
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="In Process" <?= in_array('In Process', $ofs) ? 'checked' : '' ?>/>
                                                                In Process
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="Shipped" <?= in_array('Shipped', $ofs) ? 'checked' : '' ?>/>
                                                                Shipped
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="Delivered" <?= in_array('Delivered', $ofs) ? 'checked' : '' ?>/>
                                                                Delivered
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="Return Requested" <?= in_array('Return Requested', $ofs) ? 'checked' : '' ?>/>
                                                                Return Requested
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="Completed" <?= in_array('Completed', $ofs) ? 'checked' : '' ?>/>
                                                                Completed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="Cancelled" <?= in_array('Cancelled', $ofs) ? 'checked' : '' ?>/>
                                                                Cancelled
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_FEEDBACK_STATUSES[]" value="Refunded/Completed" <?= in_array('Refunded/Completed', $ofs) ? 'checked' : '' ?>/>
                                                                Refunded/Completed
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <i>Set the order status the customer's order must reach before they are allowed to review the orders.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Allow Order Cancellation by Buyers</label>
                                                    <?php 
                                                    $ocbs = isset($config['ORDER_CANCELLATION_BUYER_STATUSES']) ? explode(",", $config['ORDER_CANCELLATION_BUYER_STATUSES']) : array();
                                                    ?>
                                                    <table class="table table-bordered" style="margin-bottom: 0px;">
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="Payment Pending" <?= in_array('Payment Pending', $ocbs) ? 'checked' : '' ?>/>
                                                                Payment Pending
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="Payment Confirmed" <?= in_array('Payment Confirmed', $ocbs) ? 'checked' : '' ?>/>
                                                                Payment Confirmed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="Cash on Delivery" <?= in_array('Cash on Delivery', $ocbs) ? 'checked' : '' ?>/>
                                                                Cash on Delivery
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="Approved" <?= in_array('Approved', $ocbs) ? 'checked' : '' ?>/>
                                                                Approved
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="In Process" <?= in_array('In Process', $ocbs) ? 'checked' : '' ?>/>
                                                                In Process
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="Shipped" <?= in_array('Shipped', $ocbs) ? 'checked' : '' ?>/>
                                                                Shipped
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="Delivered" <?= in_array('Delivered', $ocbs) ? 'checked' : '' ?>/>
                                                                Delivered
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="Return Requested" <?= in_array('Return Requested', $ocbs) ? 'checked' : '' ?>/>
                                                                Return Requested
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="Completed" <?= in_array('Completed', $ocbs) ? 'checked' : '' ?>/>
                                                                Completed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="Cancelled" <?= in_array('Cancelled', $ocbs) ? 'checked' : '' ?>/>
                                                                Cancelled
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_CANCELLATION_BUYER_STATUSES[]" value="Refunded/Completed" <?= in_array('Refunded/Completed', $ocbs) ? 'checked' : '' ?>/>
                                                                Refunded/Completed
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <i>Set the order status the customer's order must reach before they are allowed to place cancellation request on orders.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Allow Return/Exchange</label>
                                                    <?php 
                                                    $orbs = isset($config['ORDER_RETURN_BUYER_STATUSES']) ? explode(",", $config['ORDER_RETURN_BUYER_STATUSES']) : array();
                                                    ?>
                                                    <table class="table table-bordered" style="margin-bottom: 0px;">
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="Payment Pending" <?= in_array('Payment Pending', $orbs) ? 'checked' : '' ?>/>
                                                                Payment Pending
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="Payment Confirmed" <?= in_array('Payment Confirmed', $orbs) ? 'checked' : '' ?>/>
                                                                Payment Confirmed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="Cash on Delivery" <?= in_array('Cash on Delivery', $orbs) ? 'checked' : '' ?>/>
                                                                Cash on Delivery
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="Approved" <?= in_array('Approved', $orbs) ? 'checked' : '' ?>/>
                                                                Approved
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="In Process" <?= in_array('In Process', $orbs) ? 'checked' : '' ?>/>
                                                                In Process
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="Shipped" <?= in_array('Shipped', $orbs) ? 'checked' : '' ?>/>
                                                                Shipped
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="Delivered" <?= in_array('Delivered', $orbs) ? 'checked' : '' ?>/>
                                                                Delivered
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="Return Requested" <?= in_array('Return Requested', $orbs) ? 'checked' : '' ?>/>
                                                                Return Requested
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="Completed" <?= in_array('Completed', $orbs) ? 'checked' : '' ?>/>
                                                                Completed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="Cancelled" <?= in_array('Cancelled', $orbs) ? 'checked' : '' ?>/>
                                                                Cancelled
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_RETURN_BUYER_STATUSES[]" value="Refunded/Completed" <?= in_array('Refunded/Completed', $orbs) ? 'checked' : '' ?>/>
                                                                Refunded/Completed
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <i>Set the order status the customer's order must reach before they are allowed to place return/exchange request on orders.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Purchases Calculation (For Buyers)</label>
                                                    <?php 
                                                    $opbs = isset($config['ORDER_PURCHASE_BUYER_STATUSES']) ? explode(",", $config['ORDER_PURCHASE_BUYER_STATUSES']) : array();
                                                    ?>
                                                    <table class="table table-bordered" style="margin-bottom: 0px;">
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="Payment Pending" <?= in_array('Payment Pending', $opbs) ? 'checked' : '' ?>/>
                                                                Payment Pending
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="Payment Confirmed" <?= in_array('Payment Confirmed', $opbs) ? 'checked' : '' ?>/>
                                                                Payment Confirmed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="Cash on Delivery" <?= in_array('Cash on Delivery', $opbs) ? 'checked' : '' ?>/>
                                                                Cash on Delivery
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="Approved" <?= in_array('Approved', $opbs) ? 'checked' : '' ?>/>
                                                                Approved
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="In Process" <?= in_array('In Process', $opbs) ? 'checked' : '' ?>/>
                                                                In Process
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="Shipped" <?= in_array('Shipped', $opbs) ? 'checked' : '' ?>/>
                                                                Shipped
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="Delivered" <?= in_array('Delivered', $opbs) ? 'checked' : '' ?>/>
                                                                Delivered
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="Return Requested" <?= in_array('Return Requested', $opbs) ? 'checked' : '' ?>/>
                                                                Return Requested
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="Completed" <?= in_array('Completed', $opbs) ? 'checked' : '' ?>/>
                                                                Completed
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="Cancelled" <?= in_array('Cancelled', $opbs) ? 'checked' : '' ?>/>
                                                                Cancelled
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="ORDER_PURCHASE_BUYER_STATUSES[]" value="Refunded/Completed" <?= in_array('Refunded/Completed', $opbs) ? 'checked' : '' ?>/>
                                                                Refunded/Completed
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <i>Set the order status the customer's order must reach before they are are considered in buyer's purchase.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Activate Auto Order Completion</label><br/>
                                                    <input type="radio" name="ORDER_AUTO_COMPLETE" value="N" <?= isset($config['ORDER_AUTO_COMPLETE']) && $config['ORDER_AUTO_COMPLETE'] == 'N' ? 'checked' : '' ?>/> No
                                                    <input type="radio" name="ORDER_AUTO_COMPLETE" value="Y" <?= isset($config['ORDER_AUTO_COMPLETE']) && $config['ORDER_AUTO_COMPLETE'] == 'Y' ? 'checked' : '' ?>/> Yes
                                                    <br/>
                                                    <i>Orders will be auto completed on Nth day (selected in next step) from the date of item delivery. Please don't turn ON if you wish to manually control it.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Auto Order Completion (Days)</label>
                                                    <input class="form-control" name="ORDER_AUTO_COMPLETE_DAYS" value="<?= isset($config['ORDER_AUTO_COMPLETE_DAYS']) ? $config['ORDER_AUTO_COMPLETE_DAYS'] : '' ?>" placeholder="Order Auto Complete Days">
                                                    <i> Order will be auto completed Nth day specified here, from the date of delivery. Specify N days here if you wish to accept refund/return requests for N days after the item delivery else keep it zero. (Only when "Activate Auto Order Completion" is enabled)</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="updateordersettings" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="account">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Administrator Approval [Signup] After Registration</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_ADMIN_APPROVE" value="Y" <?= isset($config['ACCOUNT_ADMIN_APPROVE']) && $config['ACCOUNT_ADMIN_APPROVE'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, admin need to approve each user (buyer, seller & advertiser) after registration (User cannot login until admin approves)</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Email Verification After Registration</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_EMAIL_VERIFICATION" value="Y" <?= isset($config['ACCOUNT_EMAIL_VERIFICATION']) && $config['ACCOUNT_EMAIL_VERIFICATION'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, user (buyer, seller & advertiser) need to verify their email address provided during registration. (User cannot login until email address is verified)</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Automatic Email Verification (Through API)</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_EMAIL_VERIFY_THROUGH_API" value="Y" <?= isset($config['ACCOUNT_EMAIL_VERIFY_THROUGH_API']) && $config['ACCOUNT_EMAIL_VERIFY_THROUGH_API'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, user (buyer, seller & advertiser) email address will be verified automatically during registration.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable OTP Verification</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_OTP_VERIFICATION" value="Y" <?= isset($config['ACCOUNT_OTP_VERIFICATION']) && $config['ACCOUNT_OTP_VERIFICATION'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, user (buyer, seller & advertiser) need to verify their mobile provided during registration or update.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Auto Login After Registration</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_AUTO_LOGIN" value="Y" <?= isset($config['ACCOUNT_AUTO_LOGIN']) && $config['ACCOUNT_AUTO_LOGIN'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, users (buyer, seller & advertiser) will be automatically logged-in after registration. (Only when "Email Verification" & "Admin Approval" is disabled)</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Notify Administrator on Each Registration</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_NOTIFICATION_ADMIN" value="Y" <?= isset($config['ACCOUNT_NOTIFICATION_ADMIN']) && $config['ACCOUNT_NOTIFICATION_ADMIN'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, notification mail will be sent to administrator on each registration.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Sending Welcome Mail After Registration</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_WELCOME_EMAIL" value="Y" <?= isset($config['ACCOUNT_WELCOME_EMAIL']) && $config['ACCOUNT_WELCOME_EMAIL'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, users (buyer, seller & advertiser) will receive a welcome mail after registration.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Auto-Logout After Password Change</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_AUTO_LOGOUT_AFTER_PWD_CHANGE" value="Y" <?= isset($config['ACCOUNT_AUTO_LOGOUT_AFTER_PWD_CHANGE']) && $config['ACCOUNT_AUTO_LOGOUT_AFTER_PWD_CHANGE'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, users (buyer, seller & advertiser) will be asked to log-in again.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Activate Separate Seller Sign Up Form</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_SEPARATE_SIGN_UP" value="Y" <?= isset($config['ACCOUNT_SEPARATE_SIGN_UP']) && $config['ACCOUNT_SEPARATE_SIGN_UP'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, buyers and seller will have a separate sign up form.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Administrator Approval On Seller Request</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_ADMIN_APPROVE_SELLER" value="Y" <?= isset($config['ACCOUNT_ADMIN_APPROVE_SELLER']) && $config['ACCOUNT_ADMIN_APPROVE_SELLER'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, admin need to approve Seller's request after registration (Seller rights will not be accessible until admin approves, only when "Activate Separate Seller Sign Up Form" is enabled)</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Buyers can see Seller Tab</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_BUYER_SEE_SELLER_TAB" value="Y" <?= isset($config['ACCOUNT_BUYER_SEE_SELLER_TAB']) && $config['ACCOUNT_BUYER_SEE_SELLER_TAB'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, buyers will be able to see Seller tab.(only when "Activate Separate Seller Sign Up Form" is enabled)</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Facebook Login</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_FACEBOOK_LOGIN" value="Y" <?= isset($config['ACCOUNT_FACEBOOK_LOGIN']) && $config['ACCOUNT_FACEBOOK_LOGIN'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, users (buyer, seller & advertiser) will be able to login using facebook account. Please define settings for facebook login if enabled under "Third Party APIs" Tab.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Google Plus Login</label><br/>
                                                    <input type="checkbox" name="ACCOUNT_GOOGLE_LOGIN" value="Y" <?= isset($config['ACCOUNT_GOOGLE_LOGIN']) && $config['ACCOUNT_GOOGLE_LOGIN'] == 'Y' ? 'checked' : '' ?>>
                                                    <i> On enabling this feature, users (buyer, seller & advertiser) will be able to login using google plus account. Please define settings for google login if enabled under "Third Party APIs" Tab.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Account Terms</label><br/>
                                                    <select name="ACCOUNT_TERM_PAGE" class="form-control">
                                                        <?php foreach ($pages as $page) { ?>
                                                        <option value="<?= $page['id'] ?>" <?= isset($config['ACCOUNT_TERM_PAGE']) && $config['ACCOUNT_TERM_PAGE'] == $page['id'] ? 'selected' : '' ?>><?= $page['post_title'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <i> Forces people to agree to terms before an account can be created.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">                                        
                                                    <label>Max Login Attempts</label><br/>
                                                    <input type="number" class="form-control" name="ACCOUNT_MAX_LOGIN_ATTEMPTS" value="<?= isset($config['ACCOUNT_MAX_LOGIN_ATTEMPTS']) ? $config['ACCOUNT_MAX_LOGIN_ATTEMPTS'] : '' ?>">
                                                    <i> Maximum login attempts allowed before the account is locked for 1 hour.</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="updateaccountsettings" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="livechat">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Enable Live Chat</label><br/>
                                                    <input type="radio" name="LIVE_CHAT_ENABLE" value="N" <?= isset($config['LIVE_CHAT_ENABLE']) && $config['LIVE_CHAT_ENABLE'] == 'N' ? 'checked' : '' ?>/> No
                                                    <input type="radio" name="LIVE_CHAT_ENABLE" value="Y" <?= isset($config['LIVE_CHAT_ENABLE']) && $config['LIVE_CHAT_ENABLE'] == 'Y' ? 'checked' : '' ?>/> Yes
                                                    <br/>
                                                    <i>Enable 3rd Party Live Chat.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Live Chat Code</label>
                                                    <textarea class="form-control" name="LIVE_CHAT_CODE" placeholder="Live Chat Code"><?= isset($config['LIVE_CHAT_CODE']) ? $config['LIVE_CHAT_CODE'] : '' ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="updatelivechatsettings" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="thirdparty">
                                        <h4>Social Login</h4>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Google Plus Developer Key</label><br/>
                                                    <input type="text" class="form-control" name="API_GOOGLE_KEY" value="<?= isset($config['API_GOOGLE_KEY']) ? $config['API_GOOGLE_KEY'] : '' ?>"/>
                                                    <i>This is the google plus developer key.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Google Plus Client ID</label>
                                                    <input type="text" class="form-control" name="API_GOOGLE_CLIENT_ID" value="<?= isset($config['API_GOOGLE_CLIENT_ID']) ? $config['API_GOOGLE_CLIENT_ID'] : '' ?>" placeholder="Google Client ID">
                                                    <i>This is the application Client Id used to Login.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Google Plus Client Secret</label>
                                                    <input type="text" class="form-control" name="API_GOOGLE_CLIENT_SECRET" value="<?= isset($config['API_GOOGLE_CLIENT_SECRET']) ? $config['API_GOOGLE_CLIENT_SECRET'] : '' ?>" placeholder="Google Client Secret">
                                                    <i>This is the Google Plid client secret key used for authentication.</i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <h4>Google ReCaptcha</h4>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Secret Key</label><br/>
                                                    <input type="text" class="form-control" name="API_GOOGLE_RECAPTCHA_SECRET_KEY" value="<?= isset($config['API_GOOGLE_RECAPTCHA_SECRET_KEY']) ? $config['API_GOOGLE_RECAPTCHA_SECRET_KEY'] : '' ?>"/>
                                                    <i>This is the Recaptcha secret key used in generating captcha.</i>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>Site Key</label>
                                                    <input type="text" class="form-control" name="API_GOOGLE_RECAPTCHA_SITE_KEY" value="<?= isset($config['API_GOOGLE_RECAPTCHA_SITE_KEY']) ? $config['API_GOOGLE_RECAPTCHA_SITE_KEY'] : '' ?>" placeholder="Site Key">
                                                    <i>This is the Recaptcha site key used in generating captcha.</i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <h4>CurrencyLayer</h4>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">                                        
                                                    <label>API Access Key</label><br/>
                                                    <input type="text" class="form-control" name="API_CURRENCYLAYER_ACCESS_KEY" value="<?= isset($config['API_CURRENCYLAYER_ACCESS_KEY']) ? $config['API_CURRENCYLAYER_ACCESS_KEY'] : '' ?>" placeholder="Access Key"/>
                                                    <i>This is the currencylayer access key used in converting currency.</i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="submit" name="updateapisettings" value="Update" class="btn btn-primary"/>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
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
                        $("#EMAIL_TEMPLATE_LOGO").val(data.file_url);
                        $("#EMAIL_TEMPLATE_LOGO_IMG").attr("src", data.file_url);
                        $("#uploadingspanmsg").html("");
                    }  
                }).fail(function(data){
                    //any message
                });  
            });
            
            $(".countryforaddress").change(function (e) {
                e.preventDefault();
                var selectedElement = this;
                var country_id = $(this).val();
                var action = "<?= $sys['site_url'] ?>/requests.php?action=get-states&country_id=" + country_id;

                $.ajax({
                    type: 'POST',
                    url: action,
                    data: null,
                    /*THIS MUST BE DONE FOR FILE UPLOADING*/
                    contentType: false,
                    processData: false,
                }).done(function (data) {
                    if (data.code === '<?= SUCCESS_RESPOSE_CODE ?>') {
                        $(selectedElement).closest("form").find(".stateforaddress").html(data.html);                            
                    }
                }).fail(function (data) {
                    //any message
                });
            });
        </script>
    </body>
</html>