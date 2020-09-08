<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
if (!isset($_REQUEST['id']) || trim($_REQUEST['id']) == '1' || !isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {
    header("location: users.php");
}

$updatemsg = "";
//Update User
if (isset($_POST['update']) && isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) {        
    $user_id = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $permissions = implode(",", $_POST['permissions']);
    
    if (updateUserMeta($user_id, "permissions", $permissions)) {        
        $updatemsg = '<div class="alert alert-success">Updated Successfully</div>';        
    } else {        
        $updatemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

if (isset($_REQUEST['id']) && trim($_REQUEST['id']) != '') {
    $user = getUser(trim($_REQUEST['id']));
    if ($user == null) {
        header("location: users.php");
    }
} else {
    header("location: users.php");
}
$permissions = isset($user['metas']['permissions']) ? explode(",", $user['metas']['permissions']) : array();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit User - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link rel="stylesheet" href="<?= $sys['site_url']; ?>/admin/plugins/iCheck/flat/blue.css">
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
                        Edit <?= $user['display_name'] ?>'s Permissions
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="dashboard"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active"><a href="users.php">Users</a></li>
                        <li class="active"><a href="#">Edit User Permissions</a></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="box box-primary">
                        <!-- form start -->
                        <form role="form" action="" method="post">
                            <div class="box-body">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>"/>                                                                    
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Permissions</label>
                                            <table class="table table-bordered">
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= DASHBOARD_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(DASHBOARD_SECTION, $permissions) ? "checked" : "" ?>> Dashboard</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= MANAGE_POSTS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(MANAGE_POSTS_SECTION, $permissions) ? "checked" : "" ?>> Manage Posts</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= MANAGE_MEDIA_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(MANAGE_MEDIA_SECTION, $permissions) ? "checked" : "" ?>> Manage Media</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= MANAGE_PAGES_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(MANAGE_PAGES_SECTION, $permissions) ? "checked" : "" ?>> Manage Pages</td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= MANAGE_COMMENTS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(MANAGE_COMMENTS_SECTION, $permissions) ? "checked" : "" ?>> Manage Comments</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= MANAGE_APPEARANCE_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(MANAGE_APPEARANCE_SECTION, $permissions) ? "checked" : "" ?>> Manage Appearance</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= MANAGE_USERS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(MANAGE_USERS_SECTION, $permissions) ? "checked" : "" ?>> Manage Users</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= MANAGE_SETTINGS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(MANAGE_SETTINGS_SECTION, $permissions) ? "checked" : "" ?>> Manage Settings</td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= MANAGE_SHOPS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(MANAGE_SHOPS_SECTION, $permissions) ? "checked" : "" ?>> Manage Shops</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= PRODUCT_BRANDS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(PRODUCT_BRANDS_SECTION, $permissions) ? "checked" : "" ?>> Manage Product Brands</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= PRODUCT_CATEGORIES_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(PRODUCT_CATEGORIES_SECTION, $permissions) ? "checked" : "" ?>> Manage Product Categories</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= MANAGE_PRODUCTS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(MANAGE_PRODUCTS_SECTION, $permissions) ? "checked" : "" ?>> Manage Products</td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= PRODUCT_REVIEWS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(PRODUCT_REVIEWS_SECTION, $permissions) ? "checked" : "" ?>> Manage Product Reviews</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= PRODUCT_TAGS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(PRODUCT_TAGS_SECTION, $permissions) ? "checked" : "" ?>> Manage Product Tags</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= PRODUCT_OPTIONS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(PRODUCT_OPTIONS_SECTION, $permissions) ? "checked" : "" ?>> Manage Product Options</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= SELLER_OPTIONS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(SELLER_OPTIONS_SECTION, $permissions) ? "checked" : "" ?>> Manage Seller Options</td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= FILTERS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(FILTERS_SECTION, $permissions) ? "checked" : "" ?>> Manage Filters</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= ATTRIBUTES_SPECIFICATIONS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(ATTRIBUTES_SPECIFICATIONS_SECTION, $permissions) ? "checked" : "" ?>> Manage Attributes</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= MANAGE_BUYERS_SELLERS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(MANAGE_BUYERS_SELLERS_SECTION, $permissions) ? "checked" : "" ?>> Manage Buyers/Sellers</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= SELLER_APPROVAL_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(SELLER_APPROVAL_REQUESTS_SECTION, $permissions) ? "checked" : "" ?>> Manage Seller Approval Requests</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= SELLER_APPROVAL_FORM_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(SELLER_APPROVAL_FORM_SECTION, $permissions) ? "checked" : "" ?>> Manage Seller Approval Form</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= SELLER_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(SELLER_REQUESTS_SECTION, $permissions) ? "checked" : "" ?>> Manage Seller Requests</td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= FUNDS_WITHDRAWAL_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(FUNDS_WITHDRAWAL_REQUESTS_SECTION, $permissions) ? "checked" : "" ?>> Manage Funds Withdrawal Requests</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= AFFILIATE_MODULE_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(AFFILIATE_MODULE_SECTION, $permissions) ? "checked" : "" ?>> Manage Affiliate Module</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= ORDERS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(ORDERS_SECTION, $permissions) ? "checked" : "" ?>> Manage Orders</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= ORDERS_RETURN_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(ORDERS_RETURN_REQUESTS_SECTION, $permissions) ? "checked" : "" ?>> Manage Orders Return Requests</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= ORDERS_CANCELLATION_REQUESTS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(ORDERS_CANCELLATION_REQUESTS_SECTION, $permissions) ? "checked" : "" ?>> Manage Orders Cancel Requests</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= REPORTS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(REPORTS_SECTION, $permissions) ? "checked" : "" ?>> Manage Reports</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= PORTAL_SETTINGS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(PORTAL_SETTINGS_SECTION, $permissions) ? "checked" : "" ?>> Manage Portal Settings</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= COUNTRY_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(COUNTRY_MANAGEMENT_SECTION, $permissions) ? "checked" : "" ?>> Manage Countries</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= ZONE_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(ZONE_MANAGEMENT_SECTION, $permissions) ? "checked" : "" ?>> Manage Zones</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= STATE_MANAGEMENT_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(STATE_MANAGEMENT_SECTION, $permissions) ? "checked" : "" ?>> Manage States</td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= REASONS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(REASONS_SECTION, $permissions) ? "checked" : "" ?>> Manage Reasons</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= SHIPPING_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(SHIPPING_SECTION, $permissions) ? "checked" : "" ?>> Manage Shipping</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= COMMISSION_SETTINGS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(COMMISSION_SETTINGS_SECTION, $permissions) ? "checked" : "" ?>> Manage Commissions</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= AFFILIATE_COMMISSION_SETTING_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(AFFILIATE_COMMISSION_SETTING_SECTION, $permissions) ? "checked" : "" ?>> Manage Affiliate Commissions</td>
                                                </tr>
                                                <tr>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= PAYMENT_METHODS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(PAYMENT_METHODS_SECTION, $permissions) ? "checked" : "" ?>> Manage Payment Methods</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= EMAIL_TEMPLATE_SETTINGS_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(EMAIL_TEMPLATE_SETTINGS_SECTION, $permissions) ? "checked" : "" ?>> Manage Email Templates</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= DATABASE_BACKUP_RESTORE_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(DATABASE_BACKUP_RESTORE_SECTION, $permissions) ? "checked" : "" ?>> Manage Database Backup/Restore</td>
                                                    <td><input name="permissions[]" id="permissions[]" value="<?= SERVER_INFO_SECTION ?>" title="Permissions" type="checkbox" <?= in_array(SERVER_INFO_SECTION, $permissions) ? "checked" : "" ?>> Check Server Info</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>                               
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
        <script src="<?= $sys['site_url']; ?>/admin/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript">    
            $('input[type="checkbox"]').iCheck({
              checkboxClass: 'icheckbox_flat-blue',
              radioClass: 'iradio_flat-blue'
            });
            $(".checkall").on("ifChanged", function(e){
                $("input[type='checkbox']").iCheck($(this).is(":checked") ? "check" : "uncheck");
            });
            $('input[type="checkbox"]').trigger("change");
        </script>  
    </body>
</html>