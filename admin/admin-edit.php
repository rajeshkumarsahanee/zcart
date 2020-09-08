<?php require_once 'check_login_status.php'; ?>
<?php
if (!Sys_isUserHavePermission(STAFF_MEMBERS_SECTION, Sys_getAdminLoggedId())) {
    header("location: admin-users");
}
if(isset($_REQUEST['id']) && trim($_REQUEST['id']) == '1') {    
    header("location: admin-users");
}
$updatemsg = "";
//Update User
if (isset($_POST['update']) && Sys_isUserHavePermission(STAFF_MEMBERS_SECTION, Sys_getAdminLoggedId())) {        
    $user['id'] = filter_var(trim($_POST['id']),FILTER_SANITIZE_NUMBER_INT);
    $user['email'] = filter_var(trim($_POST['email']),FILTER_SANITIZE_STRING);
    $user['username'] = filter_var(trim($_POST['username']),FILTER_SANITIZE_STRING);    
    $user['password'] = trim($_POST['password']);
    $user['displayname'] = filter_var(trim($_POST['displayname']),FILTER_SANITIZE_STRING);        
    $user['status'] = trim($_POST['active']);    
    $meta['First_Name'] = filter_var(trim($_POST['firstname']),FILTER_SANITIZE_STRING);
    $meta['Last_Name'] = filter_var(trim($_POST['lastname']),FILTER_SANITIZE_STRING);
    $meta['permissions'] = implode(",", $_POST['user_permissions']);        
    $user['metas'] = $meta;
    
    $loguserinfo = "[userid=" . $user['id'] . ",email=" . $user['email'] . ",username=" . $user['username'] . "]";
    
    if (Sys_updateUser($user)) {
        Sys_addLog(array('log' => "Admin User Updated ".$loguserinfo, 'user_id' => Sys_getAdminLoggedId(), 'user_type' => "A"));
        $updatemsg = '<div class="alert alert-success">Updated Successfully</div>';        
    } else {
        Sys_addLog(array('log' => "Error Updating Admin User ".$loguserinfo, 'user_id' => Sys_getAdminLoggedId(), 'user_type' => "A"));
        $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

if (isset($_REQUEST['id']) && trim($_REQUEST['id']) != '') {
    $user = Sys_getUser(trim($_REQUEST['id']));
    if ($user == null) {
        header("location: users");
    }
} else {
    header("location: users");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Admin - Admin</title>
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
                        Edit Admin
                        <small>Edit admin</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active"><a href="admin-users">Admin Users</a></li>
                        <li class="active"><a href="#">Edit User</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $user['display_name']; ?></h3>
                            <div class="btn-group pull-right" data-toggle="btn-toggle">
                                <button type="button" id="activebid" class="btn btn-default btn-sm <?php if($user['status'] == '1') echo 'active'; ?>">active</button>
                                <button type="button" id="inactivebid" class="btn btn-default btn-sm <?php if($user['status'] == '0') echo 'active'; ?>">inactive</button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="" method="post">
                            <div class="box-body">
                                <input type="hidden" name="active" id="activeid" value="<?php echo $user['status']; ?>"/>
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>"/>
                                <div class="row">                                                                           
                                    <div class="col-md-6">
                                        <div class="form-group"> 
                                            <label for="firstnameid">First Name</label>
                                            <input type="text" class="form-control" id="firstnameid" name="firstname" value="<?php if(isset($user['metas']['First_Name'])) { echo $user['metas']['First_Name']; } ?>" placeholder="First Name"/>
                                        </div>                                        
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastnameid">Last Name</label>
                                            <input type="text" class="form-control" id="lastnameid" name="lastname" value="<?php if(isset($user['metas']['Last_Name'])) { echo $user['metas']['Last_Name']; } ?>" placeholder="Last Name"/>
                                        </div>                                            
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">                                        
                                            <label for="emailid">Email address</label>
                                            <input type="email" class="form-control" id="emailid" name="email" value="<?php echo $user['email']; ?>"  placeholder="Email"/>
                                        </div>
                                    </div>
                                </div>                                    
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="usernameid">Username</label>
                                            <input type="text" class="form-control" id="usernameid" name="username" value="<?php echo $user['username']; ?>" placeholder="Username" readonly/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="passwordid">Password</label>
                                            <input type="password" class="form-control" id="passwordid" name="password" placeholder="Password"/>
                                        </div>
                                    </div>
                                </div>                                                                        
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="displaynameid">Display Name</label>
                                            <input type="text" class="form-control" id="displaynameid" name="displayname" value="<?php echo $user['display_name']; ?>" placeholder="Display Name"/>
                                        </div>
                                    </div>
                                </div>                                                                    
                                <div class="col-md-12">
                                    <h4 class="box-title"><b>Permissions</b> <input type="checkbox" id="selectallcb"/> Select All</h4>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo DASHBOARD_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(DASHBOARD_SECTION, $user['id'])) { echo 'checked'; } ?>> Dashboard</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MANAGE_SHOPS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(MANAGE_SHOPS_SECTION, $user['id'])) { echo 'checked'; } ?>> Manage Shops</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PRODUCT_BRANDS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(PRODUCT_BRANDS_SECTION, $user['id'])) { echo 'checked'; } ?>> Product Brands</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PRODUCT_CATEGORIES_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(PRODUCT_CATEGORIES_SECTION, $user['id'])) { echo 'checked'; } ?>> Product Categories</label></td>
                                            </tr>
                                            <tr>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MANAGE_PRODUCTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(MANAGE_PRODUCTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Manage Products</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PRODUCT_REVIEWS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(PRODUCT_REVIEWS_SECTION, $user['id'])) { echo 'checked'; } ?>> Product Reviews</label></td>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PRODUCT_TAGS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(PRODUCT_TAGS_SECTION, $user['id'])) { echo 'checked'; } ?>> Product Tags</label></td>
                                            </tr>
                                            <tr>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo ADMIN_OPTIONS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(ADMIN_OPTIONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Product Options</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_OPTIONS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SELLER_OPTIONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Seller Options</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo FILTERS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(FILTERS_SECTION, $user['id'])) { echo 'checked'; } ?>> Filters</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo ATTRIBUTES_SPECIFICATIONS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Attributes/Specifications</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MANAGE_BUYERS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(MANAGE_BUYERS_SECTION, $user['id'])) { echo 'checked'; } ?>> Customers/Users</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BUYER_ORDER_CANCELLATION_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(BUYER_ORDER_CANCELLATION_REQUESTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Order Cancellation Requests</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BUYER_FUNDS_WITHDRAWAL_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(BUYER_FUNDS_WITHDRAWAL_REQUESTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Funds Withdrawal Requests</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MANAGE_SELLERS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(MANAGE_SELLERS_SECTION, $user['id'])) { echo 'checked'; } ?>> Manage Sellers</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_ORDER_CANCELLATION_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SELLER_ORDER_CANCELLATION_REQUESTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Seller Order Cancellation Requests</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_FUNDS_WITHDRAWAL_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SELLER_FUNDS_WITHDRAWAL_REQUESTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Seller Funds Withdrawal Requests</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_APPROVAL_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SELLER_APPROVAL_REQUESTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Seller Approval Requests</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_APPROVAL_FORM_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SELLER_APPROVAL_FORM_SECTION, $user['id'])) { echo 'checked'; } ?>> Seller Approval Form</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SELLER_REQUESTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Seller Requests</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo AFFILIATE_MODULE_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(AFFILIATE_MODULE_SECTION, $user['id'])) { echo 'checked'; } ?>> Affiliate Module</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo COLLECTIONS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(COLLECTIONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Collections</label></td> 
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo NAVIGATIONS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(NAVIGATIONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Navigations</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo CONTENT_PAGES_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(CONTENT_PAGES_SECTION, $user['id'])) { echo 'checked'; } ?>> Content Pages</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo CONTENT_BLOCK_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(CONTENT_BLOCK_SECTION, $user['id'])) { echo 'checked'; } ?>> Content Block</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo LANGUAGE_LABELS_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(LANGUAGE_LABELS_SECTION, $user['id'])) { echo 'checked'; } ?>> Language Labels</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SLIDES_MANAGEMENT_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SLIDES_MANAGEMENT_SECTION, $user['id'])) { echo 'checked'; } ?>> Slides Management</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BANNER_MANAGEMENT_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(BANNER_MANAGEMENT_SECTION, $user['id'])) { echo 'checked'; } ?>> Banner Management</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo EMPTY_CART_ITEMS_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(EMPTY_CART_ITEMS_SECTION, $user['id'])) { echo 'checked'; } ?>> Empty Cart Items </label></td>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo FAQ_CATEGORIES_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(FAQ_CATEGORIES_SECTION, $user['id'])) { echo 'checked'; } ?>> FAQ Categories</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo FAQs_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(FAQs_MANAGEMENT_SECTION, $user['id'])) { echo 'checked'; } ?>> FAQ Management</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo TESTIMONIALS_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(TESTIMONIALS_SECTION, $user['id'])) { echo 'checked'; } ?>> Testimonials</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo REPORT_REASONS_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(REPORT_REASONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Report Reasons</label></td>
                                            </tr>
                                            <tr>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo CANCEL_REASONS_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(CANCEL_REASONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Cancel Reasons</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo RETURN_REASONS_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(RETURN_REASONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Return Reasons</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SHIPPING_COMPANIES_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SHIPPING_COMPANIES_SECTION, $user['id'])) { echo 'checked'; } ?>> Shipping Companies</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SHIPPING_DURATIONS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SHIPPING_DURATIONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Shipping Durations</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo DISCOUNT_COUPONS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(DISCOUNT_COUPONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Discount Coupons</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SOCIAL_PLATFORMS_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SOCIAL_PLATFORMS_MANAGEMENT_SECTION, $user['id'])) { echo 'checked'; } ?>> Social Platforms Management</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo COUNTRY_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(COUNTRY_MANAGEMENT_SECTION, $user['id'])) { echo 'checked'; } ?>> Countries Management</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo ZONE_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(ZONE_MANAGEMENT_SECTION, $user['id'])) { echo 'checked'; } ?>> Zone Management</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo STATE_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(STATE_MANAGEMENT_SECTION, $user['id'])) { echo 'checked'; } ?>> States Management</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo CURRENCY_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(CURRENCY_MANAGEMENT_SECTION, $user['id'])) { echo 'checked'; } ?>> Currency Management</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo GENERAL_SETTINGS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(GENERAL_SETTINGS_SECTION, $user['id'])) { echo 'checked'; } ?>> General Settings</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo COMMISSION_SETTINGS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(COMMISSION_SETTINGS_SECTION, $user['id'])) { echo 'checked'; } ?>> Commission Settings</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo AFFILIATE_COMMISSION_SETTING_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(AFFILIATE_COMMISSION_SETTING_SECTION, $user['id'])) { echo 'checked'; } ?>> Affiliate Commission Settings</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo THEME_SETTINGS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(THEME_SETTINGS_SECTION, $user['id'])) { echo 'checked'; } ?>> Theme Settings</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PAYMENT_METHODS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(PAYMENT_METHODS_SECTION, $user['id'])) { echo 'checked'; } ?>> Payment Methods</label></td>                                                
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo EMAIL_TEMPLATE_SETTINGS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(EMAIL_TEMPLATE_SETTINGS_SECTION, $user['id'])) { echo 'checked'; } ?>> Manage Email Templates</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo DATABASE_BACKUP_RESTORE_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(DATABASE_BACKUP_RESTORE_SECTION, $user['id'])) { echo 'checked'; } ?>> Database Backup &amp; Restore</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SERVER_INFO_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SERVER_INFO_SECTION, $user['id'])) { echo 'checked'; } ?>> View Server Info</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo CUSTOMER_ORDERS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(CUSTOMER_ORDERS_SECTION, $user['id'])) { echo 'checked'; } ?>> Customer Order</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo VENDOR_ORDERS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(VENDOR_ORDERS_SECTION, $user['id'])) { echo 'checked'; } ?>> Vendor Orders</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PAYPAL_ADAPTIVE_PAYMENTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(PAYPAL_ADAPTIVE_PAYMENTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Paypal Adaptive Payments</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo RETURN_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(RETURN_REQUESTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Return Requests</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo REPORTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(REPORTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Reports</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SUBSCRIPTION_PAYMENT_METHODS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SUBSCRIPTION_PAYMENT_METHODS_SECTION, $user['id'])) { echo 'checked'; } ?>> Subscription Payment Methods</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SUBSCRIPTION_PACKAGES_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SUBSCRIPTION_PACKAGES_SECTION, $user['id'])) { echo 'checked'; } ?>> Subscription Packages</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SUBSCRIPTION_DISCOUNT_COUPONS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SUBSCRIPTION_DISCOUNT_COUPONS_SECTION, $user['id'])) { echo 'checked'; } ?>> Subscription Discount Coupons</label></td>
                                            </tr>                                            
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SUBSCRIPTION_ORDERS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SUBSCRIPTION_ORDERS_SECTION, $user['id'])) { echo 'checked'; } ?>> Subscription Orders</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BULK_IMPORT_EXPORT_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(BULK_IMPORT_EXPORT_SECTION, $user['id'])) { echo 'checked'; } ?>> Bulk Import/Export</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SMART_RECOMMENDATIONS_WEIGHTAGES_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SMART_RECOMMENDATIONS_WEIGHTAGES_SECTION, $user['id'])) { echo 'checked'; } ?>> Smart Recommendations - Weightages</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SMART_RECOMMENDATIONS_PRODUCTS_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(SMART_RECOMMENDATIONS_PRODUCTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Smart Recommendations - Products</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PRODUCTS_BROWSING_HISTORY_SECTION; ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(PRODUCTS_BROWSING_HISTORY_SECTION, $user['id'])) { echo 'checked'; } ?>> Product Browsing History</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">                                                                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MANAGE_ADVERTISERS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(MANAGE_ADVERTISERS_SECTION, $user['id'])) { echo 'checked'; } ?>> Manage Advertisers</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PPC_PAYMENT_METHODS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(PPC_PAYMENT_METHODS_SECTION, $user['id'])) { echo 'checked'; } ?>> PPC Payment Methods</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PPC_PROMOTIONS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(PPC_PROMOTIONS_SECTION, $user['id'])) { echo 'checked'; } ?>> PPC Promotions</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BLOG_CATEGORIES_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(BLOG_CATEGORIES_SECTION, $user['id'])) { echo 'checked'; } ?>> Blog Categories</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BLOG_POSTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(BLOG_POSTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Blog Posts</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BLOG_TAGS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(BLOG_TAGS_SECTION, $user['id'])) { echo 'checked'; } ?>> Blog Tags</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BLOG_COMMENTS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(BLOG_COMMENTS_SECTION, $user['id'])) { echo 'checked'; } ?>> Blog Comments</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MESSAGES_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(MESSAGES_SECTION, $user['id'])) { echo 'checked'; } ?>> Messages</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo STAFF_MEMBERS_SECTION ?>" title="Permissions" type="checkbox" <?php if(Sys_isUserHavePermission(STAFF_MEMBERS_SECTION, $user['id'])) { echo 'checked'; } ?>> Staff Members</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>                                            
                                        </tbody>
                                    </table>
                                </div><!-- /.box-right -->
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <?php echo $updatemsg; ?>
                                <button type="submit" class="btn btn-primary" name="update">Update</button>
                            </div>
                        </form>
                    </div><!-- /.box -->

                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?php include 'footer.php'; ?>    

        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>             
        <script>
            $("#activebid").click(function(){
               $("#activeid").val("1"); 
            });
            
            $("#inactivebid").click(function(){
               $("#activeid").val("0"); 
            });
                        
            $("#selectallcb").change(function(){               
                $("input:checkbox").not(this).prop("checked", false);
                if($(this).is(':checked')) {
                   $("input:checkbox").not(this).prop("checked", true);
                }
            });
        </script>
    </body>
</html>