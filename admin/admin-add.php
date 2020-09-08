<?php require_once 'check_login_status.php'; ?>
<?php
if (!Sys_isUserHavePermission(STAFF_MEMBERS_SECTION, Sys_getAdminLoggedId())) {
    header("location: admin-users");
}

$savemsg = "";
//Add User
if (isset($_POST['save']) && Sys_isUserHavePermission(STAFF_MEMBERS_SECTION, Sys_getAdminLoggedId())) {        
    $user['email'] = filter_var(trim($_POST['email']),FILTER_SANITIZE_STRING);
    $user['username'] = filter_var(trim($_POST['username']),FILTER_SANITIZE_STRING);    
    $user['password'] = trim($_POST['password']);
    $user['displayname'] = filter_var(trim($_POST['displayname']),FILTER_SANITIZE_STRING);        
    $user['role'] = ROLE_ADMIN;
    $user['status'] = trim($_POST['active']);    
    $meta['First_Name'] = filter_var(trim($_POST['firstname']),FILTER_SANITIZE_STRING);
    $meta['Last_Name'] = filter_var(trim($_POST['lastname']),FILTER_SANITIZE_STRING);
    $meta['permissions'] = implode(",", $_POST['user_permissions']);        
    $user['metas'] = $meta;
    
    $loguserinfo = "[email=" . $user['email'] . ",username=" . $user['username'] . "]";
    
    if (!Sys_addUser($user)) {
        Sys_addLog(array('log' => "Admin User Added ".$loguserinfo, 'user_id' => Sys_getAdminLoggedId(), 'user_type' => "A"));
        $savemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    } else {
        Sys_addLog(array('log' => "Error Adding Admin User ".$loguserinfo, 'user_id' => Sys_getAdminLoggedId(), 'user_type' => "A"));
        $savemsg = '<div class="alert alert-success">Added Successfully</div>';
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Add Admin User - Admin</title>
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
                        Add Admin User
                        <small>Add new admin</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $sys['config']['site_url']; ?>/admin"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active"><a href="<?php echo $sys['config']['site_url'] ?>/admin/admin-users">Admin Users</a></li>
                        <li class="active"><a href="#">Add Admin</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Add Admin</h3>
                            <div class="btn-group pull-right" data-toggle="btn-toggle">
                                <button type="button" id="activebid" class="btn btn-default btn-sm active">active</button>
                                <button type="button" id="inactivebid" class="btn btn-default btn-sm">inactive</button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="" method="post">
                            <div class="box-body">
                                <input type="hidden" name="active" id="activeid" value="1"/>                                
                                <div class="row">                                                                           
                                    <div class="col-md-6">
                                        <div class="form-group"> 
                                            <label for="firstnameid">First Name</label>
                                            <input type="text" class="form-control" id="firstnameid" name="firstname" placeholder="First Name"/>
                                        </div>                                        
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastnameid">Last Name</label>
                                            <input type="text" class="form-control" id="lastnameid" name="lastname" placeholder="Last Name"/>
                                        </div>                                            
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">                                        
                                            <label for="emailid">Email address</label>
                                            <input type="email" class="form-control" id="emailid" name="email" placeholder="Email"/>
                                        </div>
                                    </div>
                                </div>                                    
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="usernameid">Username</label>
                                            <input type="text" class="form-control" id="usernameid" name="username" placeholder="Username"/>
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
                                            <input type="text" class="form-control" id="displaynameid" name="displayname" placeholder="Display Name"/>
                                        </div>
                                    </div>
                                </div>                                                                    
                                <div class="col-md-12">
                                    <h4 class="box-title"><b>Permissions</b> <input type="checkbox" id="selectallcb"/> Select All</h4>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo DASHBOARD_SECTION ?>" title="Permissions" type="checkbox"> Dashboard</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MANAGE_SHOPS_SECTION ?>" title="Permissions" type="checkbox"> Manage Shops</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PRODUCT_BRANDS_SECTION ?>" title="Permissions" type="checkbox"> Product Brands</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PRODUCT_CATEGORIES_SECTION ?>" title="Permissions" type="checkbox"> Product Categories</label></td>
                                            </tr>
                                            <tr>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MANAGE_PRODUCTS_SECTION ?>" title="Permissions" type="checkbox"> Manage Products</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PRODUCT_REVIEWS_SECTION ?>" title="Permissions" type="checkbox"> Product Reviews</label></td>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PRODUCT_TAGS_SECTION ?>" title="Permissions" type="checkbox"> Product Tags</label></td>
                                            </tr>
                                            <tr>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo ADMIN_OPTIONS_SECTION ?>" title="Permissions" type="checkbox"> Product Options</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_OPTIONS_SECTION ?>" title="Permissions" type="checkbox"> Seller Options</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo FILTERS_SECTION ?>" title="Permissions" type="checkbox"> Filters</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo ATTRIBUTES_SPECIFICATIONS_SECTION ?>" title="Permissions" type="checkbox"> Attributes/Specifications</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MANAGE_BUYERS_SECTION ?>" title="Permissions" type="checkbox"> Customers/Users</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BUYER_ORDER_CANCELLATION_REQUESTS_SECTION ?>" title="Permissions" type="checkbox"> Order Cancellation Requests</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BUYER_FUNDS_WITHDRAWAL_REQUESTS_SECTION ?>" title="Permissions" type="checkbox"> Funds Withdrawal Requests</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MANAGE_SELLERS_SECTION ?>" title="Permissions" type="checkbox"> Manage Sellers</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_ORDER_CANCELLATION_REQUESTS_SECTION ?>" title="Permissions" type="checkbox"> Seller Order Cancellation Requests</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_FUNDS_WITHDRAWAL_REQUESTS_SECTION ?>" title="Permissions" type="checkbox"> Seller Funds Withdrawal Requests</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_APPROVAL_REQUESTS_SECTION ?>" title="Permissions" type="checkbox"> Seller Approval Requests</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_APPROVAL_FORM_SECTION ?>" title="Permissions" type="checkbox"> Seller Approval Form</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SELLER_REQUESTS_SECTION ?>" title="Permissions" type="checkbox"> Seller Requests</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo AFFILIATE_MODULE_SECTION ?>" title="Permissions" type="checkbox"> Affiliate Module</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo COLLECTIONS_SECTION ?>" title="Permissions" type="checkbox"> Collections</label></td> 
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo NAVIGATIONS_SECTION ?>" title="Permissions" type="checkbox"> Navigations</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo CONTENT_PAGES_SECTION ?>" title="Permissions" type="checkbox"> Content Pages</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo CONTENT_BLOCK_SECTION; ?>" title="Permissions" type="checkbox"> Content Block</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo LANGUAGE_LABELS_SECTION; ?>" title="Permissions" type="checkbox"> Language Labels</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SLIDES_MANAGEMENT_SECTION; ?>" title="Permissions" type="checkbox"> Slides Management</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BANNER_MANAGEMENT_SECTION; ?>" title="Permissions" type="checkbox"> Banner Management</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo EMPTY_CART_ITEMS_SECTION; ?>" title="Permissions" type="checkbox"> Empty Cart Items </label></td>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo FAQ_CATEGORIES_SECTION; ?>" title="Permissions" type="checkbox"> FAQ Categories</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo FAQs_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox"> FAQ Management</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo TESTIMONIALS_SECTION; ?>" title="Permissions" type="checkbox"> Testimonials</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo REPORT_REASONS_SECTION; ?>" title="Permissions" type="checkbox"> Report Reasons</label></td>
                                            </tr>
                                            <tr>                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo CANCEL_REASONS_SECTION; ?>" title="Permissions" type="checkbox"> Cancel Reasons</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo RETURN_REASONS_SECTION; ?>" title="Permissions" type="checkbox"> Return Reasons</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SHIPPING_COMPANIES_SECTION; ?>" title="Permissions" type="checkbox"> Shipping Companies</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SHIPPING_DURATIONS_SECTION ?>" title="Permissions" type="checkbox"> Shipping Durations</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo DISCOUNT_COUPONS_SECTION ?>" title="Permissions" type="checkbox"> Discount Coupons</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SOCIAL_PLATFORMS_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox"> Social Platforms Management</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo COUNTRY_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox"> Countries Management</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo ZONE_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox"> Zone Management</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo STATE_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox"> States Management</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo CURRENCY_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox"> Currency Management</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo GENERAL_SETTINGS_SECTION ?>" title="Permissions" type="checkbox"> General Settings</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo COMMISSION_SETTINGS_SECTION ?>" title="Permissions" type="checkbox"> Commission Settings</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo AFFILIATE_COMMISSION_SETTING_SECTION ?>" title="Permissions" type="checkbox"> Affiliate Commission Settings</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo THEME_SETTINGS_SECTION ?>" title="Permissions" type="checkbox"> Theme Settings</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PAYMENT_METHODS_SECTION ?>" title="Permissions" type="checkbox"> Payment Methods</label></td>                                                
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo EMAIL_TEMPLATE_SETTINGS_SECTION ?>" title="Permissions" type="checkbox"> Manage Email Templates</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo DATABASE_BACKUP_RESTORE_SECTION ?>" title="Permissions" type="checkbox"> Database Backup &amp; Restore</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SERVER_INFO_SECTION ?>" title="Permissions" type="checkbox"> View Server Info</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo CUSTOMER_ORDERS_SECTION ?>" title="Permissions" type="checkbox"> Customer Order</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo VENDOR_ORDERS_SECTION ?>" title="Permissions" type="checkbox"> Vendor Orders</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PAYPAL_ADAPTIVE_PAYMENTS_SECTION ?>" title="Permissions" type="checkbox"> Paypal Adaptive Payments</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo RETURN_REQUESTS_SECTION ?>" title="Permissions" type="checkbox"> Return Requests</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo REPORTS_SECTION ?>" title="Permissions" type="checkbox"> Reports</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SUBSCRIPTION_PAYMENT_METHODS_SECTION ?>" title="Permissions" type="checkbox"> Subscription Payment Methods</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SUBSCRIPTION_PACKAGES_SECTION ?>" title="Permissions" type="checkbox"> Subscription Packages</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SUBSCRIPTION_DISCOUNT_COUPONS_SECTION ?>" title="Permissions" type="checkbox"> Subscription Discount Coupons</label></td>
                                            </tr>                                            
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SUBSCRIPTION_ORDERS_SECTION ?>" title="Permissions" type="checkbox"> Subscription Orders</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BULK_IMPORT_EXPORT_SECTION ?>" title="Permissions" type="checkbox"> Bulk Import/Export</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SMART_RECOMMENDATIONS_WEIGHTAGES_SECTION; ?>" title="Permissions" type="checkbox"> Smart Recommendations - Weightages</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo SMART_RECOMMENDATIONS_PRODUCTS_SECTION; ?>" title="Permissions" type="checkbox"> Smart Recommendations - Products</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PRODUCTS_BROWSING_HISTORY_SECTION; ?>" title="Permissions" type="checkbox"> Product Browsing History</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">                                                                                                
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MANAGE_ADVERTISERS_SECTION ?>" title="Permissions" type="checkbox"> Manage Advertisers</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PPC_PAYMENT_METHODS_SECTION ?>" title="Permissions" type="checkbox"> PPC Payment Methods</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo PPC_PROMOTIONS_SECTION ?>" title="Permissions" type="checkbox"> PPC Promotions</label></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BLOG_CATEGORIES_SECTION ?>" title="Permissions" type="checkbox"> Blog Categories</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BLOG_POSTS_SECTION ?>" title="Permissions" type="checkbox"> Blog Posts</label></td>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BLOG_TAGS_SECTION ?>" title="Permissions" type="checkbox"> Blog Contributions</label></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver;">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo BLOG_COMMENTS_SECTION ?>" title="Permissions" type="checkbox"> Blog Comments</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr style="border-bottom: solid thin silver">
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo MESSAGES_SECTION ?>" title="Permissions" type="checkbox"> Messages</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><label><input name="user_permissions[]" id="user_permissions[]" value="<?php echo STAFF_MEMBERS_SECTION ?>" title="Permissions" type="checkbox"> Staff Members</label></td>
                                                <td></td>
                                                <td></td>
                                            </tr>                                            
                                        </tbody>
                                    </table>
                                </div><!-- /.box-right -->
                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <?php echo $savemsg; ?>
                                <button type="submit" class="btn btn-primary" name="save">Save</button>
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
            $("#activebid").click(function () {
                $("#activeid").val("1");
            });

            $("#inactivebid").click(function () {
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