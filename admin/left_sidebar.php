<?php
$page = basename($_SERVER['SCRIPT_NAME']);

$posts_menus = array('posts.php', 'post-add.php', 'post-edit.php', 'tags.php', 'tag-add.php', 'tag-edit.php');
$media_menus = array('media.php', 'media-add.php', 'media-edit.php');
$comments_menus = array('comments.php', 'comment-add.php', 'comment-edit.php');
$apearance_menus = array('themes.php', 'theme-options.php', 'menus.php');
$users_menus = array('users.php', 'user-add.php', 'user-edit.php', 'profile.php');

$catalog_menus = array(
    'shops.php', 'shop-edit.php', 
    'product-brands.php', 'product-brand-add.php', 'product-brand-edit.php', 
    'product-categories.php', 'product-category-add.php', 'product-category-edit.php', 
    'products.php', 'product-add.php', 'product-edit.php', 
    'product-reviews.php', 'product-review-edit.php', 
    'product-tags.php', 'product-tag-add.php', 'product-tag-edit.php', 
    'product-options.php', 'product-option-add.php', 'product-option-edit.php', 
    'seller-options.php', 'seller-option-add.php', 'seller-option-edit.php',
    'product-option-add.php', 'product-option-edit.php', 
    'filters.php', 'filter-add.php', 'filter-edit.php',
    'attributes.php', 'attribute-add.php', 'attribute-edit.php', 'attributes-terms.php');

$buyers_sellers_menus = array(
    'customers.php', 'customer-edit.php', 'orders-cancellation-requests.php', 
    'funds-withdrawal-requests.php', 'seller-approval-requests.php', 'seller-approval-form.php', 
    'seller-requests.php');

$affilate_menus = array('affiliates.php');

$cms_menus = array(
    'collections.php', 'collections-add.php', 'collections-edit.php', 
    'blocks.php', 
    'labels.php', 
    'slides.php', 
    'banner.php', 
    'empty-cart-items.php', 
    'faq-categories.php', 
    'faqs.php', 
    'testimonials.php',  
    'coupons.php');

$settings_menus = array(
    'settings-general.php', 'settings-discussion.php', 'settings-permalinks.php',
    'settings-countries.php', 'settings-country-edit.php', 'settings-zones.php', 'settings-zone-edit.php', 
    'settings-states.php', 'settings-state-edit.php', 
    'settings-currencies.php', 'settings-currency-edit.php', 
    'settings-portal.php',
    'settings-reasons.php', 'settings-reason-edit.php',
    'settings-shipping-companies.php', 'settings-shipping-company-edit.php', 
    'settings-shipping-durations.php', 'settings-shipping-duration-edit.php', 
    'settings-commission.php', 'settings-affiliate-commission.php',
    'settings-payment-methods.php', 'settings-payment-method-edit.php', 'settings-payment-method-fields.php',
    'settings-email-templates.php', 'settings-email-template-edit.php', 
    'settings-db-backup-restore.php', 'settings-server-info');
$settings_shipping_menus = array(
    'settings-shipping-companies.php', 'settings-shipping-company-edit.php', 
    'settings-shipping-durations.php', 'settings-shipping-duration-edit.php',
);

$loggeduser = getUser(getUserLoggedId());    
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">                      
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= isset($loggeduser['metas']['photo']) && !empty($loggeduser['metas']['photo']) ? $sys['site_url'] . "/" . $loggeduser['metas']['photo'] : "https://placehold.it/96x96" ?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p><?= $_SESSION['display_name']; ?></p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- search form (Optional) -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
                <span class="input-group-btn">
                    <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
        <!-- /.search form -->
        
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">HEADER</li>
            <!-- Optionally, you can add icons to the links -->
            <?php if(isUserHavePermission(DASHBOARD_SECTION, getUserLoggedId())) { ?>
            <li class="<?= $page == 'dashboard.php' ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/dashboard.php' ?>"><i class='fa fa-dashboard'></i> <span>Dashboard</span></a></li>
            <?php } ?>
            <?php if(isUserHavePermission(MANAGE_POSTS_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?= in_array($page, $posts_menus) && !isset($_REQUEST['type']) || isset($_REQUEST['type']) && $_REQUEST['type'] == "post" ? "active" : "" ?>">
                <a href="#"><i class='fa fa-edit'></i> <span>Posts</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">                    
                    <li class="<?= in_array($page, array('posts.php', 'post-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/posts.php'; ?>">All Posts</a></li>
                    <li class="<?= in_array($page, array('post-add.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/post-add.php'; ?>">Add New</a></li>
                    <li class="<?= in_array($page, array('tags.php')) && $_REQUEST['taxonomy'] == "category" ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/tags.php?taxonomy=category'; ?>">Categories</a></li>
                    <li class="<?= in_array($page, array('tags.php')) && $_REQUEST['taxonomy'] == "tag" ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/tags.php?taxonomy=tag'; ?>">Tags</a></li>                    
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(MANAGE_MEDIA_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?= in_array($page, $media_menus) ? "active" : "" ?>">
                <a href="#"><i class='fa fa-film'></i> <span>Media</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">                    
                    <li class="<?= in_array($page, array('media.php', 'media-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/media.php'; ?>">Library</a></li>
                    <li class="<?= in_array($page, array('media-add.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/media-add.php'; ?>">Add New</a></li>
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(MANAGE_PAGES_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?= in_array($page, $posts_menus) && isset($_REQUEST['type']) && $_REQUEST['type'] == "page" ? "active" : "" ?>">
                <a href="#"><i class='fa fa-files-o'></i> <span>Pages</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">                    
                    <li class="<?= in_array($page, array('posts.php', 'post-edit.php')) && isset($_REQUEST['type']) && $_REQUEST['type'] == "page" ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/posts.php?type=page'; ?>">All Pages</a></li>
                    <li class="<?= in_array($page, array('post-add.php')) && isset($_REQUEST['type']) && $_REQUEST['type'] == "page" ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/post-add.php?type=page'; ?>">Add New</a></li>
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(MANAGE_COMMENTS_SECTION, getUserLoggedId())) { ?>
            <li class="<?= in_array($page, $comments_menus) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/comments.php'; ?>"><i class='fa fa-comment'></i> <span>Comments</span></a></li>
            <?php } ?>
            <?php if(isUserHavePermission(MANAGE_SHOPS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(PRODUCT_BRANDS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(PRODUCT_CATEGORIES_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(MANAGE_PRODUCTS_SECTION, getUserLoggedId()) || 
                    isUserHavePermission(PRODUCT_REVIEWS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(PRODUCT_TAGS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(PRODUCT_OPTIONS_SECTION, getUserLoggedId()) || 
                    isUserHavePermission(SELLER_OPTIONS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(FILTERS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?= in_array($page, $catalog_menus) ? 'active' : '' ?>">
                <a href="#"><i class='fa fa-shopping-cart'></i> <span>Catalog</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php if(isUserHavePermission(MANAGE_SHOPS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('shops.php', 'shop-edit.php')) ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/shops.php' ?>">Shops</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(PRODUCT_BRANDS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('product-brands.php', 'product-brand-add.php', 'product-brand-edit.php')) ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/product-brands.php' ?>">Product Brands</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(PRODUCT_CATEGORIES_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('product-categories.php', 'product-category-add.php', 'product-category-edit.php')) ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/product-categories.php' ?>">Product Categories</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(MANAGE_PRODUCTS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('products.php', 'product-add.php', 'product-edit.php')) ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/products.php' ?>">Products</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(PRODUCT_REVIEWS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('product-reviews.php', 'product-review-edit.php')) ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/product-reviews.php' ?>">Product Reviews</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(PRODUCT_TAGS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('product-tags.php', 'product-tag-add.php', 'product-tag-edit.php')) ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/product-tags.php' ?>">Product Tags</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(PRODUCT_OPTIONS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('product-options.php', 'product-option-add.php', 'product-option-edit.php')) ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/product-options.php' ?>">Product Options</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(SELLER_OPTIONS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('seller-options.php', 'seller-option-add.php', 'seller-option-edit.php')) ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/seller-options.php' ?>">Seller Options</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(FILTERS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('filters.php', 'filter-add.php', 'filter-edit.php')) ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/filters.php' ?>">Filters</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(ATTRIBUTES_SPECIFICATIONS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('attributes.php', 'attribute-add.php', 'attribute-edit.php', 'attributes-terms.php')) ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/attributes.php' ?>">Attributes</a></li>
                    <?php } ?>
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId()) || 
                    isUserHavePermission(ORDER_CANCELLATION_REQUESTS_SECTION, getUserLoggedId()) ||                    
                    isUserHavePermission(FUNDS_WITHDRAWAL_REQUESTS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(SELLER_APPROVAL_REQUESTS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(SELLER_APPROVAL_FORM_SECTION, getUserLoggedId()) || 
                    isUserHavePermission(SELLER_REQUESTS_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?= in_array($page, $buyers_sellers_menus) ? 'active' : '' ?>">
                <a href="#"><i class='fa fa-users'></i> <span>Buyers/Sellers</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php if(isUserHavePermission(MANAGE_BUYERS_SELLERS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'customers.php' || $page == 'customer-edit.php' ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/customers.php' ?>">Manage Buyers/Sellers</a></li>
                    <?php } ?>    
                    <?php if(isUserHavePermission(SELLER_APPROVAL_REQUESTS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'seller-approval-requests.php' ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/seller-approval-requests.php'; ?>">Seller Approval Requests</a></li>
                    <?php } ?>  
                    <?php if(isUserHavePermission(SELLER_APPROVAL_FORM_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'seller-approval-form.php' ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/seller-approval-form.php'; ?>">Seller Approval Form</a></li>
                    <?php } ?>  
                    <?php if(isUserHavePermission(SELLER_REQUESTS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'seller-requests.php' ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/seller-requests.php'; ?>">Seller Requests</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(FUNDS_WITHDRAWAL_REQUESTS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'funds-withdrawal-requests.php' ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/funds-withdrawal-requests.php' ?>">Funds Withdrawal Requests</a></li>
                    <?php } ?>
                </ul>
            </li>
            <?php } ?>
            <?php if(isUserHavePermission(AFFILIATE_MODULE_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?php if (in_array($page, $affiliate_menus)) { ?> active <?php } ?>">
                <a href="#"><i class='fa fa-suitcase'></i> <span>Affiliates</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php if(isUserHavePermission(AFFILIATE_MODULE_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'affiliates') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/affiliates'; ?>">Affiliate Users</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(AFFILIATE_MODULE_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'affiliate-fund-withrawal-requests') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/affiliate-fund-withrawal-requests'; ?>">Funds Withdrawal Requests</a></li>
                    <?php } ?>
                </ul>
            </li>
            <?php } ?>            
            <?php if(isUserHavePermission(COLLECTIONS_SECTION, getUserLoggedId()) || 
                    isUserHavePermission(CONTENT_BLOCK_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(LANGUAGE_LABELS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(SLIDES_MANAGEMENT_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(BANNER_MANAGEMENT_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(EMPTY_CART_ITEMS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(FAQ_CATEGORIES_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(FAQs_MANAGEMENT_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(TESTIMONIALS_SECTION, getUserLoggedId()) ||
                    isUserHavePermission(DISCOUNT_COUPONS_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?php if (in_array($page, $cms_menus)) { ?> active <?php } ?>">
                <a href="#"><i class='fa fa-bolt'></i> <span>CMS</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php if(isUserHavePermission(COLLECTIONS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if (in_array($page, array('collections', 'collections-add', 'collections-edit'))) { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/collections'; ?>">Collections Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(CONTENT_BLOCK_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'blocks') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/blocks'; ?>">Content Block</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(LANGUAGE_LABELS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'labels') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/labels'; ?>">Language Labels</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(SLIDES_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'slides') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/slides'; ?>">Slides Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(BANNER_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'banner') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/banner'; ?>">Banner Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(EMPTY_CART_ITEMS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'empty-cart-items') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/empty-cart-items'; ?>">Empty Cart Items Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(FAQ_CATEGORIES_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'faq-categories') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/faq-categories'; ?>">FAQ Category Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(FAQs_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'faqs') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/faqs'; ?>">FAQs Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(TESTIMONIALS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'testimonials') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/testimonials'; ?>">Testimonials Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(DISCOUNT_COUPONS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'coupons') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/coupons'; ?>">Discount Coupons</a></li>
                    <?php } ?>
                </ul>
            </li>            
            <?php } ?>                       
            <?php if(isUserHavePermission(ORDERS_SECTION, getUserLoggedId()) || isUserHavePermission(ORDERS_RETURN_REQUESTS_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?= in_array($page, array('orders.php', 'return-requests.php')) ? 'active' : '' ?>">
                <a href="#"><i class='fa fa-cart-arrow-down'></i> <span>Orders</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php if(isUserHavePermission(ORDERS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'orders.php' ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/orders.php'; ?>">Orders</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(ORDERS_RETURN_REQUESTS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'orders-return-requests.php' ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/orders-return-requests.php'; ?>">Return Requests</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(ORDERS_CANCELLATION_REQUESTS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'orders-cancellation-requests.php' ? 'active' : '' ?>"><a href="<?= $sys['site_url'].'/admin/orders-cancellation-requests.php' ?>">Cancellation Requests</a></li>
                    <?php } ?>
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(REPORTS_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?php if (in_array($page, array('report-sales', 'report-users', 'report-products', 'report-shops', 'report-tax', 'report-commissions', 'report-affiliates', 'report-advertisers', 'report-promotions', 'report-subscriptions'))) { ?> active <?php } ?>">
                <a href="#"><i class='fa fa-bar-chart'></i> <span>Reports</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">                    
                    <li class="<?php if ($page == 'report-sales') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/report-sales'; ?>">Sales</a></li>                                        
                    <li class="<?php if ($page == 'report-users') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/report-users'; ?>">Users</a></li>                    
                    <li class="<?php if ($page == 'report-products') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/report-products'; ?>">Products</a></li>
                    <li class="<?php if ($page == 'report-shops') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/report-shops'; ?>">Shops</a></li>
                    <li class="<?php if ($page == 'report-tax') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/report-tax'; ?>">Tax</a></li>
                    <li class="<?php if ($page == 'report-commissions') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/report-commissions'; ?>">Commissions</a></li>
                    <li class="<?php if ($page == 'report-affiliates') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/report-affiliates'; ?>">Affiliates</a></li>
                    <li class="<?php if ($page == 'report-advertisers') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/report-advertisers'; ?>">Advertisers</a></li>
                    <li class="<?php if ($page == 'report-promotions') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/report-promotions'; ?>">Promotions</a></li>
                    <li class="<?php if ($page == 'report-subscriptions') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/report-subscriptions'; ?>">Subscriptions</a></li>
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(SUBSCRIPTION_PAYMENT_METHODS_SECTION, getUserLoggedId()) || isUserHavePermission(SUBSCRIPTION_PACKAGES_SECTION, getUserLoggedId()) || isUserHavePermission(SUBSCRIPTION_DISCOUNT_COUPONS_SECTION, getUserLoggedId()) || isUserHavePermission(SUBSCRIPTION_ORDERS_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?php if (in_array($page, array('subscription-payment-methods','subscription-packages', 'subscription-coupons', 'subscription-orders'))) { ?> active <?php } ?>">
                <a href="#"><i class='fa fa-rss'></i> <span>Subscription</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php if(isUserHavePermission(SUBSCRIPTION_PAYMENT_METHODS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'subscription-payment-methods') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/subscription-payment-methods'; ?>">Payment Methods</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(SUBSCRIPTION_PACKAGES_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'subscription-packages') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/subscription-packages'; ?>">Packages</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(SUBSCRIPTION_DISCOUNT_COUPONS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'subscription-coupons') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/subscription-coupons'; ?>">Coupons</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(SUBSCRIPTION_ORDERS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'subscription-orders') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/subscription-orders'; ?>">Orders</a></li>
                    <?php } ?>
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(BULK_IMPORT_EXPORT_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?php if (in_array($page, array('export', 'import', 'import-export-settings'))) { ?> active <?php } ?>">
                <a href="#"><i class='fa fa-file-archive-o'></i> <span>Export/Import</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">                    
                    <li class="<?php if ($page == 'export') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/export'; ?>">Export</a></li>
                    <li class="<?php if ($page == 'import') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/import'; ?>">Import</a></li>                                        
                    <li class="<?php if ($page == 'import-export-settings') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/import-export-settings'; ?>">Settings</a></li>                    
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(SMART_RECOMMENDATIONS_WEIGHTAGES_SECTION, getUserLoggedId()) || isUserHavePermission(SMART_RECOMMENDATIONS_PRODUCTS_SECTION, getUserLoggedId()) ||isUserHavePermission(PRODUCTS_BROWSING_HISTORY_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?php if (in_array($page, array('weightages','recommended-products', 'products-browsing-history'))) { ?> active <?php } ?>">
                <a href="#"><i class='fa fa-thumbs-o-up'></i> <span>Smart Recommendations</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php if(isUserHavePermission(SMART_RECOMMENDATIONS_WEIGHTAGES_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'weightages') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/weightages'; ?>">Manage Weightages</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(SMART_RECOMMENDATIONS_PRODUCTS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'recommended-products') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/recommended-products'; ?>">Manage Recommendations</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(PRODUCTS_BROWSING_HISTORY_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'products-browsing-history') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/products-browsing-history'; ?>">Products Browsing History</a></li>
                    <?php } ?>                   
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(MANAGE_ADVERTISERS_SECTION, getUserLoggedId()) || isUserHavePermission(PPC_PAYMENT_METHODS_SECTION, getUserLoggedId()) || isUserHavePermission(PPC_PROMOTIONS_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?php if (in_array($page, array('advertisers','ppc-payment-methods', 'ppc-promotions'))) { ?> active <?php } ?>">
                <a href="#"><i class='fa fa-desktop'></i> <span>PPC Management</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <?php if(isUserHavePermission(MANAGE_ADVERTISERS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'advertisers') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/advertisers'; ?>">Advertisers</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(PPC_PAYMENT_METHODS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'ppc-payment-methods') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/ppc-payment-methods'; ?>">PPC Payment Methods</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(PPC_PROMOTIONS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?php if ($page == 'ppc-promotions') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/ppc-promotions'; ?>">PPC Promotions</a></li>
                    <?php } ?>                   
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(MESSAGES_SECTION, getUserLoggedId())) { ?>
            <li class="<?php if ($page == 'messages') { echo 'active'; } ?>"><a href="<?php echo $sys['site_url'].'/admin/messages'; ?>"><i class='fa fa-envelope'></i> <span>Messages</span></a></li>
            <?php } ?>
            <?php if(isUserHavePermission(MANAGE_APPEARANCE_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?= in_array($page, $apearance_menus) ? "active" : "" ?>">
                <a href="#"><i class='fa fa-paint-brush'></i> <span>Appearance</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">                    
                    <li class="<?= in_array($page, array('themes.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/themes.php'; ?>">Themes</a></li>
                    <?php if(isThemeOptionsExists()) { ?>
                    <li class="<?= in_array($page, array('theme-options.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/theme-options.php'; ?>">Theme Options</a></li>
                    <?php } ?>
                    <li class="<?= in_array($page, array('menus.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/menus.php'; ?>">Menus</a></li>
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(MANAGE_USERS_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?= in_array($page, $users_menus) ? "active" : "" ?>">
                <a href="#"><i class='fa fa-users'></i> <span>Users</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">                    
                    <li class="<?= in_array($page, array('users.php', 'user-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/users.php'; ?>">All Users</a></li>
                    <li class="<?= in_array($page, array('user-add.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/user-add.php'; ?>">Add New</a></li>
                    <li class="<?= in_array($page, array('profile.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/profile.php'; ?>">Your Profile</a></li>
                </ul>
            </li>            
            <?php } ?>
            <?php if(isUserHavePermission(MANAGE_SETTINGS_SECTION, getUserLoggedId())) { ?>
            <li class="treeview <?= in_array($page, $settings_menus) ? "active" : "" ?>">
                <a href="#"><i class='fa fa-wrench'></i> <span>Settings</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">                    
                    <li class="<?= in_array($page, array('settings-general.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-general.php'; ?>">General</a></li>                    
                    <li class="<?= in_array($page, array('settings-discussion.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-discussion.php'; ?>">Discussion</a></li>                    
                    <li class="<?= in_array($page, array('settings-permalinks.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-permalinks.php'; ?>">Permalinks</a></li>
                    <?php if(isUserHavePermission(COUNTRY_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('settings-countries.php', 'settings-country-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'] . '/admin/settings-countries.php' ?>">Country Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(ZONE_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('settings-zones.php', 'settings-zone-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-zones.php' ?>">Zone Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(STATE_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('settings-states.php', 'settings-state-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-states.php' ?>">States Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(CURRENCY_MANAGEMENT_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('settings-currencies.php', 'settings-currency-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'] . '/admin/settings-currencies.php' ?>">Currency Management</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(PORTAL_SETTINGS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'settings-portal.php' ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-portal.php' ?>">Portal</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(REASONS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('settings-reasons.php', 'settings-reason-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-reasons.php' ?>">Reasons</a></li>
                    <?php } ?>
                    <?php if (isUserHavePermission(SHIPPING_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, $settings_shipping_menus) ? "active" : "" ?>">
                        <a href="#"><span>Shipping</span> <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <li class="<?= in_array($page, array('settings-shipping-companies.php', 'settings-shipping-company-add.php', 'settings-shipping-company-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'] . '/admin/settings-shipping-companies.php'; ?>">Companies</a></li>
                            <li class="<?= in_array($page, array('settings-shipping-durations.php', 'settings-shipping-duration-add.php', 'settings-shipping-duration-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'] . '/admin/settings-shipping-durations.php'; ?>">Duration Labels</a></li>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if(isUserHavePermission(COMMISSION_SETTINGS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'settings-commission' ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-commission.php' ?>">Commissions Settings</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(AFFILIATE_COMMISSION_SETTING_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'settings-affiliate-commission' ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-affiliate-commission.php' ?>">Affiliate Commissions Settings</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(PAYMENT_METHODS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('settings-payment-methods.php', 'settings-payment-method-edit.php', 'settings-payment-method-fields.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-payment-methods.php' ?>">Payment Methods</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(EMAIL_TEMPLATE_SETTINGS_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= in_array($page, array('settings-email-templates.php', 'settings-email-template-edit.php')) ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-email-templates.php' ?>">Email Templates</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(DATABASE_BACKUP_RESTORE_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'settings-db-backup-restore.php' ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-db-backup-restore.php' ?>">Database Backup & Restore</a></li>
                    <?php } ?>
                    <?php if(isUserHavePermission(SERVER_INFO_SECTION, getUserLoggedId())) { ?>
                    <li class="<?= $page == 'settings-server-info.php' ? 'active' : "" ?>"><a href="<?= $sys['site_url'].'/admin/settings-server-info.php' ?>">Server Info</a></li>
                    <?php } ?>
                </ul>
            </li>            
            <?php } ?>
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>