<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(MANAGE_PRODUCTS_SECTION, getUserLoggedId()) || !isset($_REQUEST['id']) || trim($_REQUEST['id']) == "") {
    header("location: products.php");
    exit();
}

$msg = "";
$savemsg = "";
$savevariantmsg = "";
$savevariantcsmsg = "";


if (isset($_POST['save'])) {
    $product['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);
    $product['type'] = filter_var(trim($_POST['type']), FILTER_SANITIZE_STRING);
    $product['sku'] = filter_var(trim($_POST['sku']), FILTER_SANITIZE_STRING);
    $product['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $product['model'] = filter_var(trim($_POST['model']), FILTER_SANITIZE_STRING);
    $product['brand'] = filter_var(trim($_POST['brand']), FILTER_SANITIZE_STRING);
    $product['slug'] = filter_var(trim($_POST['slug']), FILTER_SANITIZE_STRING);
    $product['short_description'] = htmlspecialchars(addslashes(trim($_POST['short_description'])));
    $product['long_description'] = htmlspecialchars(addslashes(trim($_POST['long_description'])));
    $product['categories'] = isset($_POST['categories']) ? $_POST['categories'] : array();
    $product['images'] = isset($_POST['images']) ? $_POST['images'] : array();
    $product['length_class'] = filter_var(trim($_POST['length_class']), FILTER_SANITIZE_STRING);
    $product['length'] = isset($_POST['length']) && $_POST['length'] != "" ? filter_var(trim($_POST['length']), FILTER_SANITIZE_STRING) : 0;
    $product['width'] = isset($_POST['width']) && $_POST['width'] != "" ? filter_var(trim($_POST['width']), FILTER_SANITIZE_STRING) : 0;
    $product['height'] = isset($_POST['height']) && $_POST['height'] != "" ? filter_var(trim($_POST['height']), FILTER_SANITIZE_STRING) : 0;
    $product['weight_class'] = filter_var(trim($_POST['weight_class']), FILTER_SANITIZE_STRING);
    $product['weight'] = isset($_POST['weight']) && $_POST['weight'] != "" ? filter_var(trim($_POST['weight']), FILTER_SANITIZE_STRING) : 0;
    $product['requires_shipping'] = filter_var(trim($_POST['requires_shipping']), FILTER_SANITIZE_STRING);
    $product['youtube_video'] = filter_var(trim($_POST['youtube_video']), FILTER_SANITIZE_STRING);
    $product['related_products'] = isset($_POST['related_products']) ? $_POST['related_products'] : array();
    $product['product_addons'] = isset($_POST['product_addons']) ? $_POST['product_addons'] : array();
    $product['views'] = isset($_POST['views']) ? filter_var(trim($_POST['views']), FILTER_SANITIZE_NUMBER_INT) : 0;
    $product['likes'] = isset($_POST['likes']) ? filter_var(trim($_POST['likes']), FILTER_SANITIZE_NUMBER_INT) : 0;
    $product['orders'] = isset($_POST['orders']) ? filter_var(trim($_POST['orders']), FILTER_SANITIZE_NUMBER_INT) : 0;
    $product['display_order'] = isset($_POST['display_order']) && !empty($_POST['display_order']) ? filter_var(trim($_POST['display_order']), FILTER_SANITIZE_NUMBER_INT) : 1;
    $product['featured_product'] = isset($_POST['featured_product']) ? 'Y' : 'N';
    $product['meta_title'] = filter_var(trim($_POST['meta_title']), FILTER_SANITIZE_STRING);
    $product['meta_keywords'] = filter_var(trim($_POST['meta_keywords']), FILTER_SANITIZE_STRING);
    $product['meta_description'] = htmlspecialchars(addslashes(trim($_POST['meta_description'])));
    $product['status'] = filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING);
    $product['added_by'] = getUserLoggedId();
    $product['updated_by'] = getUserLoggedId();
    $product['added_timestamp'] = date("Y-m-d H:i:s");
    $product['updated_timestamp'] = date("Y-m-d H:i:s");
    $product['shop_id'] = filter_var(trim($_POST['shop_id']), FILTER_SANITIZE_NUMBER_INT);

    $price['product_condition'] = filter_var(trim($_POST['product_condition']), FILTER_SANITIZE_STRING);
    $price['price'] = filter_var(trim($_POST['price']), FILTER_SANITIZE_STRING);
    $price['marketplace_fees'] = isset($_POST['marketplace_fees']) ? filter_var(trim($_POST['marketplace_fees']), FILTER_SANITIZE_STRING) : 0.00;
    $price['hsn_code'] = filter_var(trim($_POST['hsn_code']), FILTER_SANITIZE_STRING);
    $price['tax_code'] = filter_var(trim($_POST['tax_code']), FILTER_SANITIZE_STRING);
    $price['stock'] = filter_var(trim($_POST['stock']), FILTER_SANITIZE_STRING);
    $price['shipping_country'] = filter_var(trim($_POST['shipping_country']), FILTER_SANITIZE_STRING);
    $price['ship_free'] = isset($_POST['ship_free']) ? "Y" : "N";
    $price['min_order_qty'] = filter_var(trim($_POST['min_order_qty']), FILTER_SANITIZE_STRING);
    $price['substract_stock'] = filter_var(trim($_POST['substract_stock']), FILTER_SANITIZE_STRING);
    $price['track_inventory'] = filter_var(trim($_POST['track_inventory']), FILTER_SANITIZE_STRING);
    $price['alert_stock_level'] = filter_var(trim($_POST['alert_stock_level']), FILTER_SANITIZE_STRING);
    $price['in_stock'] = filter_var(trim($_POST['in_stock']), FILTER_SANITIZE_STRING);
    $price['date_available'] = filter_var(trim($_POST['date_available']), FILTER_SANITIZE_STRING);
    $price['enable_cod'] = isset($_POST['enable_cod']) ? "Y" : "N";

    $product['tags'] = isset($_POST['tags']) ? $_POST['tags'] : array();
    $product['price'] = $price;
    $product['shippings'] = isset($_POST['shippings']) ? $_POST['shippings'] : array();
    $product['filters'] = isset($_POST['filters']) ? $_POST['filters'] : array();

    $specifications = array();
    if (isset($_POST['specifications'])) {
        foreach ($_POST['specifications'] as $s) {
            $tmp['attribute_id'] = $s['id'];
            $tmp['attribute_value'] = $s['value'];
            $specifications[] = $tmp;
        }
    }
    $product['specifications'] = $specifications;

    $product['product_options'] = isset($_POST['product_options']) ? $_POST['product_options'] : array();
    $product['qty_discounts'] = isset($_POST['qty_discounts']) ? $_POST['qty_discounts'] : array();
    $product['special_discounts'] = isset($_POST['special_discounts']) ? $_POST['special_discounts'] : array();
    $product['downloads'] = isset($_POST['downloads']) ? $_POST['downloads'] : array();

    if (updateProduct($product)) {
        $savemsg = '<div class="alert alert-success">Product updated successfully!</div>';
    } else {
        $savemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
    }
}

$shop_id = isset($sys['default_shop']) ? $sys['default_shop'] : 1;
if (isset($_REQUEST['shop']) && trim($_REQUEST['shop']) <> "") {
    $shop_id = filter_var(trim($_REQUEST['shop']), FILTER_SANITIZE_NUMBER_INT);
}
$product = getProduct(filter_var(trim($_REQUEST['id']), FILTER_SANITIZE_NUMBER_INT), $shop_id);
$shop = getShop($shop_id);
if ($product == null || empty($shop)) {
    header("location: products.php");
}
$relatedProducts = getProducts(array("id", "name"), array("ids" => explode(",", $product['related_products'])), 0, -1);
$categories = getCategories(array(), array("main_category" => "0"), 0, -1); //getting root categories
$countries = getCountries(array('id', 'name'));
$shippingCompanies = getShippingCompanies();
$shippingDurations = getShippingDurations();
$attributeGroups = getAttributes();
$options = getOptions();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Product - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="<?= $sys['site_url'] ?>/admin/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <?php include 'css.php'; ?>        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <style>
            .custom-nav {    
                position: absolute;
                width: 96%;
                z-index: 1;
                background: white;
            }
            #imguploadinput {
               width: 40px;
               height: 40px;
               position: absolute;
               top: -8px;
               right: 21px;
               z-index: -1; 
            }
            #thumbnails {
                float: none;
                margin-top: 10px;
                min-height: 150px;
                border: solid thick aliceblue;
            }
            #thumbnails .product-img-thumb {
                max-height: 150px;
                margin: 10px;
                border: dashed thin #ccd0d4;
            }
            #thumbnails .delete {
                position: absolute;
                top: -5px;
                right: 1px;
                cursor: pointer;
            }
            #selected-tags {
                padding: 5px 0px;
            }
            #selected-tags li {
                margin-right: 5px;
            }
            #selected-tags .delete {
                cursor: pointer;
            }
            #selected-filters, #selected-related-products {
                min-height: 150px;
            }
            #selected-filters .delete, #selected-related-products .delete {
                cursor: pointer;
            }
            .selected-option-title {
                margin-top: -1.1em;
                margin-left: 10px;
                background-color: white;
                display: block;
                width: fit-content;
                width:max-content;
                padding: 0px 10px;
            }
            .plus-minus {
                padding: 5px 0px;
                border-radius: 0px;
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
                        Edit Product
                        <small>Edit existing product from this section</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class=""><a href="<?= $sys['site_url'] ?>/admin/products.php">Products</a></li>
                        <li class="active">Edit Product</li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">                      
                    <form action="" method="post" enctype="multipart/form-data">
                        <?php echo $savemsg; ?>
                        <input type="hidden" name="id" value="<?= $product['id'] ?>"/>
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#general" data-toggle="tab">GENERAL</a></li>
                                <li><a href="#data" data-toggle="tab">DATA</a></li>
                                <li><a href="#links" data-toggle="tab">LINKS</a></li>
                                <li><a href="#seo" data-toggle="tab">SEO</a></li>
                                <li><a href="#specifications" data-toggle="tab">SPECIFICATIONS</a></li>
                                <li><a href="#option" data-toggle="tab">OPTION</a></li>
                                <li><a href="#qty_discount" data-toggle="tab">QTY DISCOUNT</a></li>
                                <li><a href="#special_discount" data-toggle="tab">SPECIAL DISCOUNT</a></li>
                                <li><a href="#downloads" data-toggle="tab">DOWNLOADS</a></li>                            
                            </ul>
                            <div class="tab-content">                            
                                <div class="tab-pane active" id="general">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Type</label>
                                                <select name="type" class="form-control" required>
                                                    <?php foreach ($PRODUCT_TYPES as $key => $value) { ?>
                                                        <option value="<?= $key ?>" <?= $product['type'] == $key ? "selected" : "" ?>><?= $value ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>Shop*</label>
                                                <input id="shop_name" type="text" class="form-control" name="shop_name" value="<?= $shop['name'] ?>" required readonly/>
                                                <input id="shop_id" type="hidden" class="form-control" name="shop_id" value="<?= $shop['id'] ?>" required readonly/>
                                                <ul id="shopslist" class="nav custom-nav">

                                                </ul>
                                            </div>
                                        </div>                                    
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Sku*</label>
                                                <input type="text" class="form-control" name="sku" value="<?= $product['sku'] ?>" required/>
                                            </div>
                                            <!-- /.form-group -->                                        
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>Name*</label>
                                                <input type="text" class="form-control" name="name" value="<?= $product['name'] ?>" required/>
                                            </div>
                                            <!-- /.form-group -->                                        
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Slug*</label>
                                                <input type="text" class="form-control" name="slug" value="<?= $product['slug'] ?>" required/>
                                                Do not use spaces, instead replace spaces with - and make sure the keyword is globally unique.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Price*</label>
                                                <input type="text" class="form-control" name="price" value="<?= isset($product['prices'][$shop_id]) ? $product['prices'][$shop_id]['price'] : ""; ?>" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Quantity/Stock*</label>
                                                <input type="number" class="form-control" name="stock" value="<?= isset($product['prices'][$shop_id]) ? $product['prices'][$shop_id]['stock'] : ""; ?>" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Minimum Quantity*</label>
                                                <input type="number" class="form-control" name="min_order_qty" value="<?= isset($product['prices'][$shop_id]) ? $product['prices'][$shop_id]['min_order_qty'] : ""; ?>" required/>
                                                <span style="font-size: 12px">Force minimum ordered qty.</span>
                                            </div>                                   
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>HSN Code*</label>
                                                <input id="hsn_code" type="text" class="form-control" name="hsn_code" value="<?= isset($product['prices'][$shop_id]) ? $product['prices'][$shop_id]['hsn_code'] : ""; ?>" required/>
                                                <input id="hsn_code_hidden" type="hidden" class="form-control"/>
                                                <ul id="hsncodelist" class="nav custom-nav">

                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Tax Code*</label>
                                                <select class="form-control" name="tax_code" required>
                                                    <option value="">-Select Code-</option>
                                                    <?php foreach ($TAX_PERCENT as $key => $value) { ?>
                                                        <option value="<?= $key ?>" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['tax_code'] == $key  ? "selected" : "" ?>><?= $key ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Brand*</label>
                                                <input id="brand" type="text" class="form-control" name="brand" value="<?= $product['brand'] ?>" required/>
                                                <ul id="brandslist" class="nav custom-nav">

                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Category*</label>
                                                <select id="rootcategories" name="categories[]" class="form-control" required>
                                                    <option value="">-Select Category-</option>
                                                    <?php foreach ($categories as $category) { ?>
                                                    <option value="<?= $category['id'] ?>" <?= array_key_exists($category['id'], $product['categories']) ? "selected" : "" ?>><?= $category['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div id="subcategories">
                                                    <?php foreach($product['categories'] as $sub_category) { 
                                                        if($sub_category['main_category'] == 0) continue;
                                                        ?>
                                                    <select name="categories[]" class="form-control">
                                                        <option value="<?= $sub_category['id'] ?>"><?= $sub_category['name'] ?></option>
                                                    </select>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <!-- /.form-group -->                                        
                                        </div>
                                        <!-- /.col -->                                                          
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Model*</label>
                                                <input type="text" class="form-control" name="model" value="<?php echo $product['model']; ?>" required/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Condition</label>
                                                <select name="product_condition" class="form-control">
                                                    <?php foreach ($CONDITIONS as $key => $value) { ?>
                                                        <option value="<?= $key ?>" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['product_condition'] == $key ? "selected" : "" ?>><?= $value ?></option>
                                                    <?php } ?>
                                                </select>                                            
                                            </div>
                                            <!-- /.form-group -->                                        
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <?php foreach ($PRODUCT_STATUSES as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>" <?= $product['status'] == $key ? "selected" : "" ?>><?= $value ?></option>
                                                    <?php } ?>
                                                </select> 
                                            </div>
                                            <!-- /.form-group -->                                        
                                        </div>                                    
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">                                                
                                            <!-- /.form-group -->                                        
                                            <div class="form-group">
                                                <label>Images (Click on upload icon to upload or link icon to fetch image from link)</label>
                                                <div class="pull-right">
                                                    <span id="uploadingspanmsg"></span>
                                                    <label class="btn-flat btn btn-sm btn-default" title="Image Link" id="imgaddlink"><i class="fa fa-link"></i></label>
                                                    <label class="btn-flat btn btn-sm btn-primary" title="Upload"><i class="fa fa-upload"></i><input id="imguploadinput" type="file"></label>
                                                </div>
                                                <ul class="nav navbar-nav sortable" id="thumbnails">
                                                    <?php
                                                    if (trim($product['images']) <> "") {
                                                        foreach (explode(",", $product['images']) as $img) {
                                                            ?>
                                                            <li>
                                                                <span class="delete"><i class="fa fa-times fa-2x"></i></span>
                                                                <input type="hidden" name="images[]" value="<?= $img ?>"/>
                                                                <img src="<?= $img ?>" class="img-responsive product-img-thumb"/>
                                                            </li>
                                                        <?php
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <!-- /.form-group -->                                        
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">                                        
                                            <div class="form-group">
                                                <label>Short Description</label>                                                
                                                <textarea name="short_description" class="form-control" maxlength="250" rows="2"><?= $product['short_description'] ?></textarea>
                                            </div>
                                            <!-- /.form-group -->                                        
                                            <div class="form-group">
                                                <label>Long Description</label>
                                                <textarea id="long_description" name="long_description" class="form-control" rows="4"><?= $product['long_description'] ?></textarea>
                                            </div>
                                            <!-- /.form-group -->                                        
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">                                        
                                            <div class="form-group">
                                                <label>Tags</label>
                                                <input type="text" id="tag" class="form-control"/>
                                                <ul class="nav custom-nav" id="tags">

                                                </ul>
                                                <ul class="nav navbar-nav" id="selected-tags">
                                                    <?php foreach ($product['tags'] as $tag) { ?>
                                                        <li>
                                                            <span class="badge">
                                                                <span class="delete"><i class="fa fa-times"></i></span> 
                                                                <input type="hidden" name="tags[]" value="<?= $tag['id'] ?>"/>
                                                                <?= $tag['name'] ?>
                                                            </span>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>                                                                                                                       
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Requires Shipping</label>                                                
                                                <select class="form-control" name="requires_shipping">
                                                    <option value="Y" <?= $product['requires_shipping'] == "Y" ? "selected" : "" ?>>Yes</option>
                                                    <option value="N" <?= $product['requires_shipping'] == "N" ? "selected" : "" ?>>No</option>
                                                </select>
                                            </div>                                                                                                                       
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Shipping Country</label>                                                
                                                <select class="form-control select2" name="shipping_country">
                                                    <option value=""></option>
                                                    <?php foreach ($countries as $c) { ?>
                                                        <option value="<?= $c['id'] ?>" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['shipping_country'] == $c['id'] ? "selected" : "" ?>><?= $c['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>                                                                                                                       
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Free Shipping</label><br/>                                   
                                                <input type="checkbox" name="ship_free" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['ship_free'] == "Y" ? "checked" : "" ?>/>
                                                Shipping Prices will not be considered for any location for ship free products.
                                            </div>                                                                                                                       
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table id="shippings" class="table">
                                                <thead>
                                                    <tr>
                                                        <th>SHIPS TO</th>
                                                        <th>SHIPPING COMPANY</th>
                                                        <th>PROCESSING TIME</th>
                                                        <th>COST[&#8377;]</th>
                                                        <th>EACH ADDITIONAL ITEM [&#8377;]</th>
                                                    </tr>
                                                </thead>    
                                                <tbody id="shipping_tbody">
                                                    <?php
                                                    $i = 0;
                                                    foreach ($product['shippings'] as $shipping) {
                                                        ?>
                                                        <tr id="tr<?= $i ?>">
                                                            <td>
                                                                <select class="form-control select2" name="shippings[<?= $i ?>][country]">
                                                                    <option value="0" <?= "0" == $shipping['country'] ? "selected" : "" ?>>-- Everywhere Else --</option>
                                                                    <?php foreach ($countries as $c) { ?>
                                                                        <option value="<?= $key ?>" <?= $c['id'] == $shipping['country'] ? "selected" : "" ?>><?= $value ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select2" name="shippings[<?= $i ?>][company]">
                                                                    <?php foreach ($shippingCompanies as $shippingCompany) { ?>
                                                                        <option value="<?= $shippingCompany['id'] ?>" <?= $shippingCompany['id'] == $shipping['company'] ? "selected" : "" ?>><?= $shippingCompany['name'] ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select2" name="shippings[<?= $i ?>][duration_id]">
                                                                    <?php foreach ($shippingDurations as $shippingDuration) { ?>
                                                                        <option value="<?= $shippingDuration['id'] ?>" <?= $shippingDuration['id'] == $shipping['duration_id'] ? "selected" : "" ?>><?= $shippingDuration['label'] ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" name="shippings[<?= $i ?>][charges]" value="<?= $shipping['charges'] ?>" class="form-control"/></td>
                                                            <td><input type="text" name="shippings[<?= $i ?>][additional_charges]" value="<?= $shipping['additional_charges'] ?>" class="form-control"/></td>
                                                            <td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow('tr<?= $i ?>')"><i class="fa fa-minus"></i></a></td>
                                                        </tr>
                                                        <?php
                                                        $i++;
                                                    }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td> 
                                                        <td></td> 
                                                        <td><a class="btn btn-primary btn-sm pull-right" href="javascript:addShippingRow()"><i class="fa fa-plus"></i></a></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>                                    
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="data">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Subtract Stock</label>                                                
                                                <select class="form-control" name="substract_stock">
                                                    <option value="Y" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['substract_stock'] == "Y" ? "checked" : "" ?>>Yes</option>
                                                    <option value="N" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['substract_stock'] == "N" ? "checked" : "" ?>>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Track Inventory</label>
                                                <select class="form-control" name="track_inventory">
                                                    <option value="Y" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['track_inventory'] == "Y" ? "checked" : "" ?>>Yes</option>
                                                    <option value="N" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['track_inventory'] == "N" ? "checked" : "" ?>>No</option>
                                                </select>
                                            </div>
                                        </div>                    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Alert Stock Level</label>                                                
                                                <input type="number" class="form-control" name="alert_stock_level" value="<?= isset($product['prices'][$shop_id]) ? $product['prices'][$shop_id]['alert_stock_level'] : "" ?>"/>
                                                Note: You will receive email notification when product stock qty is below or equal to threshold level and Inventory tracking is enabled.
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>In Stock</label>
                                                <select class="form-control" name="in_stock">
                                                    <option value="Y" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['in_stock'] == "Y" ? "checked" : "" ?>>Yes</option>
                                                    <option value="N" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['in_stock'] == "Y" ? "checked" : "" ?>>No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>Youtube Video Link</label>                                                
                                                <input type="text" class="form-control" name="youtube_video" value="<?= $product['youtube_video'] ?>"/>
                                                Please enter the youtube video URL here.
                                            </div>
                                        </div>                    
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Date Available</label>                                                
                                                <input type="text" class="form-control date" name="date_available" value="<?= isset($product['prices'][$shop_id]) ? $product['prices'][$shop_id]['date_available'] : "" ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Length Class</label>                                                
                                                <select class="form-control" name="length_class">
                                                    <option value="CM" <?= $product['length_class'] == "CM" ? "selected" : "" ?>>Centimeter</option>
                                                    <option value="MM" <?= $product['length_class'] == "MM" ? "selected" : "" ?>>Millimeter</option>
                                                    <option value="IN" <?= $product['length_class'] == "IN" ? "selected" : "" ?>>Inch</option>
                                                </select>    
                                            </div>
                                        </div> 
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>Dimensions (L x W x H)</label>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" name="length" value="<?= $product['length'] ?>" placeholder="Length"/>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" name="width" value="<?= $product['width'] ?>" placeholder="Width"/>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" name="height" value="<?= $product['height'] ?>" placeholder="Height"/>
                                                    </div>
                                                </div>                            
                                            </div>                        
                                        </div>
                                    </div>
                                    <div class="row">                                       
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Weight Class</label>                                                
                                                <select class="form-control" name="weight_class">
                                                    <option value="KG" <?= $product['weight_class'] == "KG" ? "selected" : "" ?>>Kilogram</option>
                                                    <option value="GM" <?= $product['weight_class'] == "GM" ? "selected" : "" ?>>Grams</option>
                                                    <option value="PD" <?= $product['weight_class'] == "PD" ? "selected" : "" ?>>Pound</option>
                                                    <option value="OU" <?= $product['weight_class'] == "OU" ? "selected" : "" ?>>Ounce</option>
                                                    <option value="LT" <?= $product['weight_class'] == "LT" ? "selected" : "" ?>>Litres</option>
                                                    <option value="ML" <?= $product['weight_class'] == "ML" ? "selected" : "" ?>>Milli Litre</option>
                                                </select>                            
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>Weight</label>                                                
                                                <input type="text" class="form-control" name="weight" value="<?= $product['weight'] ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">                                       
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Display Order</label>                                                
                                                <input type="number" class="form-control" name="display_order" value="<?= $product['display_order'] ?>"/>                          
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Featured Product</label><br/>
                                                <input type="checkbox" class="" name="featured_product" value="Y" <?= $product['featured_product'] == "Y" ? "checked" : "" ?>/>
                                                Featured Products will be listed on Featured Products Page.
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Enable COD</label><br/>                                            
                                                <input type="checkbox" class="" name="enable_cod" value="Y" <?= isset($product['prices'][$shop_id]) && $product['prices'][$shop_id]['enable_cod'] == "Y" ? "checked" : "" ?>/>                            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="links">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="filters">Filters</label>
                                                <input type="text" id="filter" class="form-control"/>
                                                <ul class="nav custom-nav" id="filters">

                                                </ul>
                                                <ul class="nav" id="selected-filters">
                                                    <?php foreach ($product['filters'] as $filter) { ?>
                                                        <li>
                                                            <span class="delete"><i class="fa fa-times"></i></span> 
                                                            <input type="hidden" name="filters[]" value="<?= $filter['id'] ?>"/>
                                                            <?= $filter['group_name'] . " > " . $filter['name'] ?>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Related Products</label>
                                                <input type="text" id="related-product" class="form-control"/>
                                                <ul class="nav custom-nav" id="related-products">

                                                </ul>
                                                <ul class="nav" id="selected-related-products">
                                                    <?php foreach ($relatedProducts as $relatedProduct) { ?>
                                                        <li>
                                                            <span class="delete"><i class="fa fa-times"></i></span>
                                                            <input type="hidden" name="related_products[]" value="<?= $relatedProduct['id'] ?>"/>
                                                            <?= $relatedProduct['name'] ?>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="seo">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Meta Title</label>
                                                <input type="text" class="form-control" name="meta_title" value="<?= $product['meta_title'] ?>"/>
                                            </div>
                                            <div class="form-group">
                                                <label>Meta Keywords</label>                                                
                                                <textarea name="meta_keywords" class="form-control" maxlength="250" rows="2"><?= $product['meta_keywords'] ?></textarea>
                                            </div>
                                            <!-- /.form-group -->                                        
                                            <div class="form-group">
                                                <label>Meta Description</label>
                                                <textarea name="meta_description" class="form-control" rows="3"><?= $product['meta_description'] ?></textarea>
                                            </div>
                                            <!-- /.form-group -->                                        
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="specifications">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 40%;">Specification</th>
                                                            <th style="width: 40%;">Value</th>
                                                            <th style="width: 20%;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="specifications_tbody">
                                                        <?php
                                                        $i = 0;
                                                        foreach ($product['specifications'] as $specification) {
                                                            ?>
                                                            <tr id="str<?= $i ?>">
                                                                <td>
                                                                    <select name="specifications[<?= $i ?>][id]" class="form-control">
                                                                        <?php foreach ($attributeGroups as $attributeGroup) { ?>
                                                                            <optgroup label="<?= $attributeGroup['name'] ?>">
                                                                                <?php
                                                                                $attributes = getAttribute($attributeGroup['id']);
                                                                                foreach ($attributes['attributes'] as $a) {
                                                                                    ?>
                                                                                    <option value="<?= $a['id'] ?>" <?= $specification['attribute_id'] == $a['id'] ? "selected" : "" ?>><?= $a['name'] ?></option>
                                                                                <?php } ?>
                                                                            </optgroup>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                                <td><textarea type="text" name="specifications[<?= $i ?>][value]" class="form-control"><?= $specification['attribute_value'] ?></textarea></td>
                                                                <td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow('str<?= $i ?>')"><i class="fa fa-minus"></i></a></td>
                                                            </tr>
                                                            <?php
                                                            $i++;
                                                        }
                                                        ?>
                                                    </tbody>
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th style="text-align: right;"><a href="javascript:addSpecificationsRow()" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a></th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="option">
                                    <div class="row">
                                        <div class="col-md-3">                        
                                            <table class="table">
                                                <tbody id="option_container_tbody">
                                                    <?php
                                                    $i = 0;
                                                    foreach ($product['product_options'] as $product_option) {
                                                        ?>
                                                        <tr id="<?= $i ?>">
                                                            <td><a class="" href="javascript:removeOption('<?= $i ?>')"><i class="fa fa-times"></i></a></td>
                                                            <td><?= $options[$product_option['option_id']]['name'] ?></td>                                       
                                                        </tr>
                                                        <?php
                                                        $i++;
                                                    }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="2">
                                                            <select id="product_option" onchange="addOption()" class="form-control select2" style="width: 100%; border-radius: 0px;" placeholder="Option">
                                                                <option value=""></option>
                                                                <?php
                                                                $choose_options = $input_options = $date_options = $file_options = "";
                                                                $option_values = array();
                                                                foreach ($options as $productOption) {
                                                                    if ($productOption['type'] == "Select/Listbox/Dropdown" || $productOption['type'] == "Radio" || $productOption['type'] == "Checkbox") {
                                                                        $option_value = '<select id="option_values' . $productOption['id'] . '" style="display:none;">';
                                                                        foreach ($productOption['values'] as $value) {
                                                                            $option_value .= '<option value="' . $value['option_value'] . '">' . $value['option_value'] . '</option>';
                                                                        }
                                                                        $option_value .= '</select>';
                                                                        $option_values[] = $option_value;
                                                                        $choose_options .= '<option value="' . $productOption['id'] . '" option_type="' . $productOption['type'] . '">' . $productOption['name'] . '</option>';
                                                                    }
                                                                    if ($productOption['type'] == "Textarea" || $productOption['type'] == "Text") {
                                                                        $input_options .= '<option value="' . $productOption['id'] . '" option_type="' . $productOption['type'] . '">' . $productOption['name'] . '</option>';
                                                                    }
                                                                    if ($productOption['type'] == "Date &amp; Time" || $productOption['type'] == "Date" || $productOption['type'] == "Time") {
                                                                        $date_options .= '<option value="' . $productOption['id'] . '" option_type="' . $productOption['type'] . '">' . $productOption['name'] . '</option>';
                                                                    }
                                                                    if($productOption['type'] == "File") {
                                                                        $file_options .= '<option value="' . $productOption['id'] . '" option_type="' . $productOption['type'] . '">' . $productOption['name'] . '</option>';
                                                                    }
                                                                }
                                                                ?>                                            
                                                                <optgroup label="Choose">
                                                                    <?= $choose_options ?>                                                
                                                                </optgroup>
                                                                <optgroup label="Input">                                                                                               
                                                                    <?= $input_options ?>
                                                                </optgroup>
                                                                <optgroup label="Date">
                                                                    <?= $date_options ?>                                                
                                                                </optgroup>
                                                                <optgroup label="File">
                                                                    <?= $file_options ?>                                                
                                                                </optgroup>                                            
                                                            </select>                                        
                                                            <?php
                                                            if (isset($option_values)) {
                                                                foreach ($option_values as $option_value) {
                                                                    echo $option_value . "<br/>";
                                                                }
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>                                
                                                </tfoot>
                                            </table>                        
                                        </div>
                                        <div class="col-md-9">
                                            <div id="option_value_container" class="">
                                                <?php
                                                $i = 0;
                                                foreach ($product['product_options'] as $product_option) {
                                                    $option_type = $options[$product_option['option_id']]['type'];
                                                    ?>
                                                    <div class="" id="opt_val_container_<?= $i ?>" style="border: dashed 1px;padding: 5px;margin-bottom: 10px;">
                                                        <p class="selected-option-title"><?= $options[$product_option['option_id']]['name'] ?></p>
                                                        <input type="hidden" name="product_options[<?= $i ?>][id]" value="<?= $product_option['id'] ?>"/>
                                                        <input type="hidden" name="product_options[<?= $i ?>][option_name]" value="<?= $options[$product_option['option_id']]['name'] ?>"/>
                                                        <input type="hidden" name="product_options[<?= $i ?>][option_id]" value="<?= $product_option['option_id'] ?>"/>
                                                        <input type="hidden" name="product_options[<?= $i ?>][type]" value="<?= $options[$product_option['option_id']]['type'] ?>"/>
                                                        <?php if ($option_type === 'Select/Listbox/Dropdown' || $option_type === 'Radio' || $option_type === 'Checkbox') { ?>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <select name="product_options[<?= $i ?>][required]" class="form-control">
                                                                            <option value="Y" <?= $product_option['required'] == "Y" ? "selected" : "" ?>>Required</option>
                                                                            <option value="N" <?= $product_option['required'] == "N" ? "selected" : "" ?>>Not Required</option>
                                                                        </select>
                                                                    </div>
                                                                    <table id="opt_val_table_<?= $i ?>" class="table table-condensed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Option Value</th>
                                                                                <th>Quantity</th>
                                                                                <th>Subtract</th>
                                                                                <th>Price</th>
                                                                                <th>Weight</th>
                                                                                <th></th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $j = 0;
                                                                            foreach ($product_option['values'] as $ov) {
                                                                                ?>
                                                                                <tr id="option_value_row_<?= $j ?>">
                                                                                    <td>
                                                                                        <input type="hidden" name="product_options[<?= $i ?>][option_value][<?= $j ?>][id]" value="<?= $ov['id'] ?>"/>
                                                                                        <select name="product_options[<?= $i ?>][option_value][<?= $j ?>][option_value]" class="form-control">
                                                                                            <?php foreach($options[$ov['option_id']]['values'] as $v) { ?>
                                                                                            <option value="<?= $v['option_value'] ?>" <?= $v['option_value'] == $ov['option_value'] ? "selected" : "" ?>><?= $v['option_value'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td><input type="text" name="product_options[<?= $i ?>][option_value][<?= $j ?>][quantity]" value="<?= $ov['quantity'] ?>" placeholder="Quantity" class="form-control"></td>
                                                                                    <td>
                                                                                        <select name="product_options[<?= $i ?>][option_value][<?= $j ?>][subtract]" class="form-control">
                                                                                            <option value="Y" <?= $ov['subtract'] == 'Y' ? "selected" : "" ?>>Yes</option>
                                                                                            <option value="N" <?= $ov['subtract'] == 'N' ? "selected" : "" ?>>No</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="input-group">
                                                                                            <span class="input-group-addon" id="basic-addon1" style="padding:0px;">
                                                                                                <select name="product_options[<?= $i ?>][option_value][<?= $j ?>][price_prefix]" class="btn plus-minus">
                                                                                                    <option value="+" <?= $ov['price_prefix'] == '+' ? "selected" : "" ?>>+</option>
                                                                                                    <option value="-" <?= $ov['price_prefix'] == '-' ? "selected" : "" ?>>-</option>
                                                                                                </select>
                                                                                            </span>
                                                                                            <input type="text" name="product_options[<?= $i ?>][option_value][<?= $j ?>][price]" value="<?= $ov['price'] ?>" placeholder="Price" class="form-control" aria-describedby="basic-addon1"/>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="input-group">
                                                                                            <span class="input-group-addon" id="basic-addon1" style="padding:0px;">
                                                                                                <select name="product_options[<?= $i ?>][option_value][<?= $j ?>][weight_prefix]" class="btn plus-minus">
                                                                                                    <option value="+" <?= $ov['weight_prefix'] == '+' ? "selected" : "" ?>>+</option>
                                                                                                    <option value="-" <?= $ov['weight_prefix'] == '-' ? "selected" : "" ?>>-</option>
                                                                                                </select>
                                                                                            </span>
                                                                                            <input type="text" name="product_options[<?= $i ?>][option_value][<?= $j ?>][weight]" value="<?= $ov['weight'] ?>" placeholder="Weight" class="form-control" aria-describedby="basic-addon1"/>
                                                                                        </div>
                                                                                    </td>
                                                                                    <td style="text-align: center;"><a href="javascript:removeOptionValueRow('<?= $j ?>')"><i class="fa fa-minus"></i></a></td>
                                                                                </tr>
                                                                                <?php
                                                                                $j++;
                                                                            }
                                                                            ?>
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td style="text-align: center;"><a href="javascript:addOptionValueRow('<?= $i ?>','<?= $product_option['option_id'] ?>');"><i class="fa fa-plus"></i></a></td>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        <?php } else if ($option_type === 'Textarea' || $option_type === 'Text') { ?>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <select name="product_options[<?= $i ?>][required]" class="form-control">
                                                                            <option value="Y" <?= $product_option['required'] == "Y" ? "selected" : "" ?>>Required</option>
                                                                            <option value="N" <?= $product_option['required'] == "N" ? "selected" : "" ?>>Not Required</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <textarea name="product_options[<?= $i ?>][option_value]" class="form-control" placeholder="Option Value"><?= $product_option['option_value'] ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } else if ($option_type === 'Date &amp; Time') { ?>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <select name="product_options[<?= $i ?>][required]" class="form-control">
                                                                            <option value="Y" <?= $product_option['required'] == "Y" ? "selected" : "" ?>>Required</option>
                                                                            <option value="N" <?= $product_option['required'] == "N" ? "selected" : "" ?>>Not Required</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="text" name="product_options[<?= $i ?>][option_value]" value="<?= $product_option['option_value'] ?>" class="form-control datetime" placeholder="Option Value"/>
                                                                    </div>
                                                                </div>
                                                            </div>                     
                                                            <?php
                                                        } else if ($option_type === 'Date') { ?>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <select name="product_options[<?= $i ?>][required]" class="form-control">
                                                                            <option value="Y" <?= $product_option['required'] == "Y" ? "selected" : "" ?>>Required</option>
                                                                            <option value="N" <?= $product_option['required'] == "N" ? "selected" : "" ?>>Not Required</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="text" name="product_options[<?= $i ?>][option_value]" value="<?= $product_option['option_value'] ?>" class="form-control date" placeholder="Option Value"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } else if ($option_type === 'Time') { ?>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <select name="product_options[<?= $i ?>][required]" class="form-control">
                                                                            <option value="Y" <?= $product_option['required'] == "Y" ? "selected" : "" ?>>Required</option>
                                                                            <option value="N" <?= $product_option['required'] == "N" ? "selected" : "" ?>>Not Required</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="text" name="product_options[<?= $i ?>][option_value]" value="<?= $product_option['option_value'] ?>" class="form-control time" placeholder="Option Value"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                         <?php   
                                                        } else if ($option_type === 'File') { ?>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <select name="product_options[<?= $i ?>][required]" class="form-control">
                                                                            <option value="Y" <?= $product_option['required'] == "Y" ? "selected" : "" ?>>Required</option>
                                                                            <option value="N" <?= $product_option['required'] == "N" ? "selected" : "" ?>>Not Required</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            </div>                        
                                        </div>
                                    </div>                
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="qty_discount">
                                    <div class="col-md-12">                    
                                        <table id="qty_discount_table" class="table">
                                            <thead>
                                                <tr>
                                                    <th>QUANTITY</th>
                                                    <th>PRIORITY</th>
                                                    <th>DISCOUNTED PRICE [&#8377;]</th>
                                                    <th>DATE START</th>
                                                    <th>DATE END</th>
                                                </tr>
                                            </thead>    
                                            <tbody id="qty_discount_tbody">
                                                <?php
                                                $i = 0;
                                                foreach ($product['qty_discounts'] as $qdiscount) {
                                                    ?>
                                                    <tr id="trqd<?= $i ?>">
                                                        <td><input type="text" name="qty_discounts[<?= $i ?>][quantity]" value="<?= $qdiscount['quantity'] ?>" class="form-control"/></td>
                                                        <td><input type="number" name="qty_discounts[<?= $i ?>][priority]" value="<?= $qdiscount['priority'] ?>" class="form-control"/></td>
                                                        <td><input type="text" name="qty_discounts[<?= $i ?>][price]" value="<?= $qdiscount['price'] ?>" class="form-control"/></td>
                                                        <td><input type="text" name="qty_discounts[<?= $i ?>][start_date]" value="<?= $qdiscount['start_date'] ?>" class="form-control date"/></td>
                                                        <td><input type="text" name="qty_discounts[<?= $i ?>][end_date]" value="<?= $qdiscount['end_date'] ?>" class="form-control date"/></td>
                                                        <td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow('trqd<?= $i ?>')"><i class="fa fa-minus"></i></a></td>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td> 
                                                    <td></td> 
                                                    <td><a class="btn btn-primary btn-sm pull-right" href="javascript:addQtyDiscount()"><i class="fa fa-plus"></i></a></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>                                    
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="special_discount">
                                    <div class="col-md-12">                    
                                        <table id="special_discount_table" class="table">
                                            <thead>
                                                <tr>                                
                                                    <th>PRIORITY</th>
                                                    <th>SPECIAL PRICE [&#8377;]</th>
                                                    <th>DATE START</th>
                                                    <th>DATE END</th>
                                                </tr>
                                            </thead>    
                                            <tbody id="special_discount_tbody">
                                                <?php
                                                $i = 0;
                                                foreach ($product['special_discounts'] as $sdiscount) {
                                                    ?>
                                                    <tr id="trsd<?= $i ?>">
                                                        <td><input type="number" name="special_discounts[<?= $i ?>][priority]" value="<?= $sdiscount['priority'] ?>" class="form-control"/></td>
                                                        <td><input type="text" name="special_discounts[<?= $i ?>][price]" value="<?= $sdiscount['price'] ?>" class="form-control"/></td>
                                                        <td><input type="text" name="special_discounts[<?= $i ?>][start_date]" value="<?= $sdiscount['start_date'] ?>" class="form-control date"/></td>
                                                        <td><input type="text" name="special_discounts[<?= $i ?>][end_date]" value="<?= $sdiscount['end_date'] ?>" class="form-control date"/></td>
                                                        <td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow('trsd<?= $i ?>')"><i class="fa fa-minus"></i></a></td>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td> 
                                                    <td></td> 
                                                    <td><a class="btn btn-primary btn-sm pull-right" href="javascript:addSpecialDiscount()"><i class="fa fa-plus"></i></a></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>                                    
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="downloads">
                                    <div class="col-md-12">                    
                                        <table id="downloads_table" class="table">
                                            <thead>
                                                <tr>                                
                                                    <th>Download Name</th>
                                                    <th>File Name</th>
                                                    <th>Max Download Times</th>
                                                    <th>Validity (days)</th>
                                                </tr>
                                            </thead>    
                                            <tbody id="downloads_tbody">
                                                <?php
                                                $i = 0;
                                                foreach ($product['downloads'] as $download) {
                                                    ?>
                                                    <tr id="trd<?= $i ?>">
                                                        <td><input type="text" name="downloads[<?= $i ?>][name]" value="<?= $download['download_name'] ?>" class="form-control"/></td>
                                                        <td>
                                                            <input type="hidden" id="download_<?= $i ?>" name="downloads[<?= $i ?>][file_path]" value="<?= $download['file_path'] ?>" class="form-control"/>
                                                            <span id="download_span_<?= $i ?>"><?= basename($download['file_path']) ?></span>
                                                        </td>
                                                        <td><input type="number" name="downloads[<?= $i ?>][max_downloads_time]" value="<?= $download['max_downloads_time'] ?>" class="form-control"/>-1 for Unlimited</td>
                                                        <td><input type="number" name="downloads[<?= $i ?>][validity_days]" value="<?= $download['validity_days'] ?>" class="form-control"/>-1 for Unlimited</td>
                                                        <td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow('trd<?= $i ?>')"><i class="fa fa-minus"></i></a></td>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td> 
                                                    <td></td> 
                                                    <td><a class="btn btn-primary btn-sm pull-right" href="javascript:addDownload()"><i class="fa fa-plus"></i></a></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>                                    
                                </div>
                                <!-- /.tab-pane -->
                                <hr/>
                                <input type="submit" class="btn btn-success" name="save" value="Save"/>                            
                            </div>
                            <!-- /.tab-content -->
                        </div>                    
                    </form>
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->

            <!-- Main Footer -->      
            <?php include 'footer.php'; ?>     
            <div class='control-sidebar-bg'></div>
        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>        
        <script src="<?= $sys['site_url'] ?>/admin/plugins/select2/select2.full.min.js"></script>
        <script src="<?= $sys['site_url'] ?>/admin/custom/custom.js"></script>
        <script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script>
            CKEDITOR.replace('long_description');
            $(".select2").select2();
            $('.datetime').datetimepicker({ format: 'YYYY-MM-DD HH:mm:ss' });
            $('.date').datetimepicker({ format: 'YYYY-MM-DD' });
            $('.time').datetimepicker({ format: 'HH:mm:ss' });
        </script>        
        <script>
            $("#shop_name").on("keyup focus", function(e){
                var name = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-shops&name=" + name,
                    success: function (response) {
                        $("#shopslist").html(response.html);
                        $("#shopslist").css("border", "solid thin #d2d6de");
                        if(response.html === "") {
                            $("#shopslist").css("border", "none");
                        }
                    }
                });
            });
            $("#shopslist").on("click", "a", function(e){
                $("#shop_name").val($(this).html());
                $("#shop_id").val($(this).attr("data-id"));
                $("#shopslist li").remove();
                $("#shopslist").css("border", "none");
                return false;
            });
            
            $("#hsn_code").on("keyup focus", function(e){
                var query = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-hsn-codes&query=" + query,
                    success: function (response) {
                        $("#hsncodelist").html(response.html);
                        $("#hsncodelist").css("border", "solid thin #d2d6de");
                        if(response.html === "") {
                            $("#hsncodelist").css("border", "none");
                        }
                    }
                });
            });
            $("#hsn_code").on("blur", function(){
                $("#hsn_code").val($("#hsn_code_hidden").val());
            });
            $("#hsncodelist").on("click", "a", function(e){
                $("#hsn_code").val($(this).attr("data-code"));
                $("#hsn_code_hidden").val($(this).attr("data-code"));
                $("#hsncodelist li").remove();
                $("#hsncodelist").css("border", "none");
                return false;
            });
            
            $("#brand").on("keyup focus", function(e){
                var name = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-brands&name=" + name,
                    success: function (response) {
                        $("#brandslist").html(response.html);
                        $("#brandslist").css("border", "solid thin #d2d6de");
                        if(response.html === "") {
                            $("#brandslist").css("border", "none");
                        }
                    }
                });
            });
            $("#brandslist").on("click", "a", function(e){
                $("#brand").val($(this).html());
                $("#brandslist li").remove();
                $("#brandslist").css("border", "none");
                return false;
            });
            
            $("#rootcategories").on("change", function(e){
                var category = $(this).val();
                if(category === "") {
                    $("#subcategories").html("");
                    return;
                }
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-subcategories&category=" + category,
                    success: function (response) {
                        $("#subcategories").html(response.html);
                    }
                });
            });
            $("#subcategories").on("change", ".subcategories", function(e){
                var category = $(this).val();
                $(this).find("option").each(function(){
                    $("#subcategories").find(".sub-of-" + $(this).val()).val("");
                    $("#subcategories").find(".sub-of-" + $(this).val()).trigger("change");
                    $("#subcategories").find(".sub-of-" + $(this).val()).remove();
                });
                if(category === "") {
                    return;
                }
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-subcategories&category=" + category,
                    success: function (response) {
                        $("#subcategories").append(response.html);
                    }
                });
            });
            
            $("#imguploadinput").change(function(e){                                      
                e.preventDefault();                                            
                var action = "<?= $sys['site_url']; ?>/requests.php?action=upload-product-image";
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
                        var html = '<li>'
                                +'<span class="delete"><i class="fa fa-times fa-2x"></i></span>'
                                +'<input type="hidden" name="images[]" value="' + data.file_url + '"/>'
                                +'<img src="' + data.file_url + '" class="img-responsive product-img-thumb"/>'
                                +'</li>';
                        $("#thumbnails").prepend(html);
                        $("#uploadingspanmsg").html("");
                    }  
                }).fail(function(data){
                    //any message
                });  

            });
            $("#imgaddlink").click(function(e){
                var imglink = prompt("Please paste image link here:", "");
                if(imglink !== null && imglink !== "" && (imglink.startsWith("http://") || imglink.startsWith("https://"))) {
                    var html = '<li>'
                            +'<span class="delete"><i class="fa fa-times fa-2x"></i></span>'
                            +'<input type="hidden" name="images[]" value="' + imglink + '"/>'
                            +'<img src="' + imglink + '" class="img-responsive product-img-thumb"/>'
                            +'</li>';
                    $("#thumbnails").prepend(html);
                }
            });
            $("#thumbnails").on("click", ".delete", function(){
                $(this).parent().remove();
            });
            $(".sortable").sortable();
            
            $("#tag").on("keyup focus", function(e){    
                var name = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-tags&name=" + name,
                    success: function (response) {
                        $("#tags").html(response.html);
                        $("#tags").css("border", "solid thin #d2d6de");
                        if(response.html === "") {
                            $("#tags").css("border", "none");
                        }
                    }
                });
            });
            $("#tags").on("click", "a", function(e){
                var selectedtagid = $(this).attr("data-id");
                var alreadyselected = false;
                var html = '<li>'
                            +'<span class="badge">'
                            +'<span class="delete"><i class="fa fa-times"></i></span> '
                            +'<input type="hidden" name="tags[]" value="' + $(this).attr("data-id") + '"/>'
                            +$(this).text()
                            +'</span>'
                            +'</li>';
                $("#selected-tags input").each(function(){
                    if(selectedtagid === $(this).val()) {
                        alreadyselected = true;
                    }
                });
                if(!alreadyselected) {
                    $("#selected-tags").prepend(html);
                }
                //clear tags list
                $("#tags").html("");
                $("#tags").css("border", "none");
                return false;
            });
            $("#selected-tags").on("click", ".delete", function(){
                $(this).parent().remove();
            });
            
            $("#filter").on("keyup focus", function(e){    
                var name = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-filters&q=" + name,
                    success: function (response) {
                        $("#filters").html(response.html);
                        $("#filters").css("border", "solid thin #d2d6de");
                        if(response.html === "") {
                            $("#filters").css("border", "none");
                        }
                    }
                });
            });
            $("#filters").on("click", "a", function(e){
                var selectedfilterid = $(this).attr("data-id");
                var alreadyselected = false;
                var html = '<li>'
                            +'<span class="delete"><i class="fa fa-times"></i></span> '
                            +'<input type="hidden" name="filters[]" value="' + $(this).attr("data-id") + '"/>'
                            +$(this).text()
                            +'</li>';
                $("#selected-filters input").each(function(){
                    if(selectedfilterid === $(this).val()) {
                        alreadyselected = true;
                    }
                });
                if(!alreadyselected) {
                    $("#selected-filters").prepend(html);
                }
                //clear tags list
                $("#filters").html("");
                $("#filters").css("border", "none");
                return false;
            });
            $("#selected-filters").on("click", ".delete", function(){
                $(this).parent().remove();
            });
            
            $("#related-product").on("keyup focus", function(e){    
                var name = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?= $sys['site_url'] ?>/requests.php?action=get-products&name=" + name,
                    success: function (response) {
                        $("#related-products").html(response.html);
                        $("#related-products").css("border", "solid thin #d2d6de");
                        if(response.html === "") {
                            $("#related-products").css("border", "none");
                        }
                    }
                });
            });
            $("#related-products").on("click", "a", function(e){
                var selectedproductid = $(this).attr("data-id");
                var alreadyselected = false;
                var html = '<li>'
                            +'<span class="delete"><i class="fa fa-times"></i></span> '
                            +'<input type="hidden" name="related_products[]" value="' + $(this).attr("data-id") + '"/>'
                            +$(this).text()
                            +'</li>';
                $("#selected-related-products input").each(function(){
                    if(selectedproductid === $(this).val()) {
                        alreadyselected = true;
                    }
                });
                if(!alreadyselected) {
                    $("#selected-related-products").prepend(html);
                }
                //clear tags list
                $("#related-products").html("");
                $("#related-products").css("border", "none");
                return false;
            });
            $("#selected-related-products").on("click", ".delete", function(){
                $(this).parent().remove();
            });
            
            $(".tab-content").on("click", function(e){
                //clear shops list
                $("#shopslist").html("");
                $("#shopslist").css("border", "none");
                //clear brands list
                $("#brandslist").html("");
                $("#brandslist").css("border", "none");
                //clear tags list
                $("#tags").html("");
                $("#tags").css("border", "none");
                //clear filters list
                $("#filters").html("");
                $("#filters").css("border", "none");
                //clear related products list
                $("#related-products").html("");
                $("#related-products").css("border", "none");
            });

            function addShippingRow() {                    
                var trcount = $("#shipping_tbody tr").length;  
                var item = '<tr id="tr'+trcount+'">';
                    item += '<td>'+getCountries(trcount)+'</td>';
                    item += '<td>'+getShippingCompanies(trcount)+'</td>';
                    item += '<td>'+getProcessingTimes(trcount)+'</td>';
                    item += '<td><input type="text" name="shippings['+trcount+'][charges]" class="form-control"/></td>';
                    item += '<td><input type="text" name="shippings['+trcount+'][additional_charges]" class="form-control"/></td>';
                    item += '<td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow(\'tr'+trcount+'\')"><i class="fa fa-minus"></i></a></td>';
                    item += '</tr>';                    
                $('#shipping_tbody').append(item);                    
            }                        
            
            function getCountries(trcount) {
                var countries = '<select class="form-control select2" name="shippings['+trcount+'][country]">';
                countries += '<option value="0">-- Everywhere Else --</option>';
                <?php foreach($countries as $c) { ?>
                    countries += '<option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>';
                <?php } ?>
                countries += '</select>';
                return countries;                
            }
            
            function getShippingCompanies(trcount) {
                var shippingCompanies = '<select class="form-control select2" name="shippings['+trcount+'][company]">';
                <?php foreach($shippingCompanies as $shippingCompany) { ?>
                    shippingCompanies += '<option value="<?= $shippingCompany['id'] ?>"><?= $shippingCompany['name'] ?></option>';
                <?php } ?>
                shippingCompanies += '</select>';
                return shippingCompanies;                
            }
            
            function getProcessingTimes(trcount) {
                var shippingDurations = '<select class="form-control select2" name="shippings['+trcount+'][duration_id]">';
                <?php foreach($shippingDurations as $shippingDuration) { ?>
                    shippingDurations += '<option value="<?= $shippingDuration['id'] ?>"><?= $shippingDuration['label'] ?></option>';
                <?php } ?>
                shippingDurations += '</select>';
                return shippingDurations;                
            }
            
            function addSpecificationsRow() {                    
                var trid = $("#specifications_tbody tr").length;  
                var item = '<tr id="str'+trid+'">';
                    item += '<td>'+getSpecifications(trid)+'</td>';
                    item += '<td><textarea type="text" name="specifications['+trid+'][value]" class="form-control"></textarea></td>';
                    item += '<td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow(\'str'+trid+'\')"><i class="fa fa-minus"></i></a></td>';
                    item += '</tr>';                    
                $('#specifications_tbody').append(item);   
                $(".specifications").select2();
            }
                        
            function getSpecifications(trid) {
                var attributes = '<select class="form-control specifications" name="specifications['+trid+'][id]">';
                <?php foreach($attributeGroups as $attributeGroup) { ?>
                    attributes += '<optgroup label="<?php echo $attributeGroup['name']; ?>">';
                    <?php $attributes = getAttribute($attributeGroup['id']);
                        foreach($attributes['attributes'] as $a) {                                            
                    ?>
                    attributes += '<option value="<?= $a['id'] ?>"><?= $a['name'] ?></option>';
                    <?php } ?>
                    attributes += '</optgroup>';
                <?php } ?>
                attributes += '</select>';                
                return attributes;                
            }
            
            function deleteRow(trid) {
                $("#"+trid).remove();
            }
            
            
            function showsubcategory(maincategory, level) {
                $.ajax({
                    type: "GET",
                    url: "<?php echo $sys['site_url']; ?>/requests.php?f=getsubcategory&maincategory=" + maincategory + "&level=" + level,
                    success: function (response) {
                        $("#level" + level).html(response);
                    }
                });
            }    
            
            function showsubcategoryforimport(maincategory, level) {
                $.ajax({
                    type: "GET",
                    url: "<?php echo $sys['site_url']; ?>/requests.php?f=getsubcategoryforimport&maincategory=" + maincategory + "&importlevel=" + level,
                    success: function (response) {
                        $("#importlevel" + level).html(response);
                    }
                });
            }    
            
            function addOption() {
                var selectedOptionId = $("#product_option").val();
                if(selectedOptionId === "") {
                    return;
                }
                var selectedOptionText = $('option:selected', $("#product_option")).text();
                var selectedOptionType = $('option:selected', $("#product_option")).attr('option_type');
                var trid = $("#option_container_tbody tr").length;  
                var item = '<tr id="'+trid+'">';
                    item += '<td><a class="" href="javascript:removeOption(\''+trid+'\')"><i class="fa fa-times"></i></a></td>';
                    item += '<td>'+selectedOptionText+'</td>';                                       
                    item += '</tr>';                    
                $('#option_container_tbody').append(item);   
                                
                var optionValueContent = '<div class="" id="opt_val_container_' + trid + '" style="border: dashed 1px;padding: 5px;margin-bottom: 10px;">';
                optionValueContent += '<p class="selected-option-title">' + selectedOptionText + '</p>';
                optionValueContent += '<input type="hidden" name="product_options['+trid+'][product_option_id]" value=""/>';
                optionValueContent += '<input type="hidden" name="product_options['+trid+'][option_name]" value="' + selectedOptionText + '"/>' ;
                optionValueContent += '<input type="hidden" name="product_options['+trid+'][option_id]" value="' + selectedOptionId + '"/>';
                optionValueContent += '<input type="hidden" name="product_options['+trid+'][type]" value="' + selectedOptionType + '"/>';
                if(selectedOptionType === 'Select/Listbox/Dropdown' || selectedOptionType === 'Radio' || selectedOptionType === 'Checkbox') {
                    optionValueContent += '<div class="row">';
                    optionValueContent += '<div class="col-md-12">';
                    optionValueContent += '<div class="form-group">';
                    optionValueContent += '<select name="product_options['+trid+'][required]" class="form-control">';
                    optionValueContent +='<option value="Y">Required</option>';
                    optionValueContent += '<option value="N">Not Required</option>';
                    optionValueContent += '</select>';
                    optionValueContent += '</div>';
                    optionValueContent += '<table id="opt_val_table_' +trid+'" class="table table-condensed">';
                    optionValueContent += '<thead>';
                    optionValueContent += '<tr>';
                    optionValueContent += '<th>Option Value</th>';
                    optionValueContent += '<th>Quantity</th>';
                    optionValueContent += '<th>Subtract</th>';
                    optionValueContent += '<th>Price</th>';
                    optionValueContent += '<th>Weight</th>';
                    optionValueContent += '<th></th>';
                    optionValueContent += '</tr>';
                    optionValueContent += '</thead>';
                    optionValueContent += '<tbody>';                  
                    optionValueContent += '</tbody>';
                    optionValueContent += '<tfoot>';
                    optionValueContent += '<tr>';
                    optionValueContent += '<td></td>';
                    optionValueContent += '<td></td>';
                    optionValueContent += '<td></td>';
                    optionValueContent += '<td></td>';
                    optionValueContent += '<td></td>';
                    optionValueContent += '<td style="text-align: center;"><a href="javascript:addOptionValueRow(\''+trid+'\',\''+selectedOptionId+'\');"><i class="fa fa-plus"></i></a></td>';
                    optionValueContent += '</tr>';
                    optionValueContent += '</tfoot>';
                    optionValueContent += '</table>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';
                } else if(selectedOptionType === 'Textarea' || selectedOptionType === 'Text') {
                    optionValueContent += '<div class="row">';
                    optionValueContent += '<div class="col-md-12">';
                    optionValueContent += '<div class="form-group">';
                    optionValueContent += '<select name="product_options['+trid+'][required]" class="form-control">';
                    optionValueContent += '<option value="Y">Required</option>';
                    optionValueContent += '<option value="N">Not Required</option>';
                    optionValueContent += '</select>';
                    optionValueContent += '</div>';
                    optionValueContent += '<div class="form-group">';
                    optionValueContent += '<textarea name="product_options['+trid+'][option_value]" class="form-control" placeholder="Option Value"></textarea>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';
                } else if(selectedOptionType === 'Date & Time') {
                    optionValueContent += '<div class="row">';
                    optionValueContent += '<div class="col-md-12">';
                    optionValueContent += '<div class="form-group">';
                    optionValueContent += '<select name="product_options['+trid+'][required]" class="form-control">';
                    optionValueContent += '<option value="Y">Required</option>';
                    optionValueContent += '<option value="N">Not Required</option>';
                    optionValueContent += '</select>';
                    optionValueContent += '</div>';
                    optionValueContent += '<div class="form-group">';
                    optionValueContent += '<input type="text" name="product_options['+trid+'][option_value]" class="form-control datetime" placeholder="Option Value"/>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';                        
                } else if( selectedOptionType === 'Date') {
                    optionValueContent += '<div class="row">';
                    optionValueContent += '<div class="col-md-12">';
                    optionValueContent += '<div class="form-group">';
                    optionValueContent += '<select name="product_options['+trid+'][required]" class="form-control">';
                    optionValueContent += '<option value="Y">Required</option>';
                    optionValueContent += '<option value="N">Not Required</option>';
                    optionValueContent += '</select>';
                    optionValueContent += '</div>';
                    optionValueContent += '<div class="form-group">';
                    optionValueContent += '<input type="text" name="product_options['+trid+'][option_value]" class="form-control date" placeholder="Option Value"/>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';                        
                } else if( selectedOptionType === 'Time') {
                    optionValueContent += '<div class="row">';
                    optionValueContent += '<div class="col-md-12">';
                    optionValueContent += '<div class="form-group">';
                    optionValueContent += '<select name="product_options['+trid+'][required]" class="form-control">';
                    optionValueContent += '<option value="Y">Required</option>';
                    optionValueContent += '<option value="N">Not Required</option>';
                    optionValueContent += '</select>';
                    optionValueContent += '</div>';
                    optionValueContent += '<div class="form-group">';
                    optionValueContent += '<input type="text" name="product_options['+trid+'][option_value]" class="form-control time" placeholder="Option Value"/>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';                        
                } else if( selectedOptionType === 'File') {
                    optionValueContent += '<div class="row">';
                    optionValueContent += '<div class="col-md-12">';
                    optionValueContent += '<div class="form-group">';
                    optionValueContent += '<select name="product_options['+trid+'][required]" class="form-control">';
                    optionValueContent += '<option value="Y">Required</option>';
                    optionValueContent += '<option value="N">Not Required</option>';
                    optionValueContent += '</select>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';
                    optionValueContent += '</div>';                        
                }
                $("#option_value_container").append(optionValueContent);
                $('.datetime').datetimepicker({                    
                    format: 'YYYY-MM-DD HH:mm:ss'
                });
                $('.date').datetimepicker({                    
                    format: 'YYYY-MM-DD'
                });
                $('.time').datetimepicker({                    
                    format: 'HH:mm:ss'
                });
            }
            
            function removeOption(trid) {
                $("#"+trid).remove();
                $("#opt_val_container_"+trid).remove();
            }
            
            function addOptionValueRow(trid, optid) {
                var trcount = $("#opt_val_table_"+trid + " tbody tr").length;
                $("#opt_val_table_"+trid + " tbody").find("tr").last().find("a").hide();
                var optionValueItem = '<tr id="option_value_row_'+trcount+'">';
                    optionValueItem += '<td>';
                    optionValueItem += '<select name="product_options['+trid+'][option_value]['+trcount+'][option_value]" class="form-control">'
                    optionValueItem += $("#option_values"+optid).html();                    
                    optionValueItem += '</select>';                    
                    optionValueItem += '</td>';
                    optionValueItem += '<td>';
                    optionValueItem += '<input type="text" name="product_options['+trid+'][option_value]['+trcount+'][quantity]" value="" placeholder="Quantity" class="form-control">';
                    optionValueItem += '</td>';
                    optionValueItem += '<td>';
                    optionValueItem += '<select name="product_options['+trid+'][option_value]['+trcount+'][subtract]" class="form-control">';
                    optionValueItem += '<option value="1">Yes</option>';
                    optionValueItem += '<option value="0">No</option>';
                    optionValueItem += '</select>';
                    optionValueItem += '</td>';
                    optionValueItem += '<td class="">';
                    optionValueItem += '<div class="input-group">';
                    optionValueItem += '<span class="input-group-addon" id="basic-addon1" style="padding:0px;">';
                    optionValueItem += '<select name="product_options['+trid+'][option_value]['+trcount+'][price_prefix]" class="btn plus-minus">'
                    optionValueItem += '<option value="+">+</option>';
                    optionValueItem += '<option value="-">-</option>';
                    optionValueItem += '</select>';
                    optionValueItem += '</span>';
                    optionValueItem += '<input type="text" name="product_options['+trid+'][option_value]['+trcount+'][price]" value="" placeholder="Price" class="form-control" aria-describedby="basic-addon1"/>';
                    optionValueItem += '</div>';
                    optionValueItem += '</td>';
                    optionValueItem += '<td class="">';
                    optionValueItem += '<div class="input-group">';
                    optionValueItem += '<span class="input-group-addon" id="basic-addon1" style="padding:0px">';
                    optionValueItem += '<select name="product_options['+trid+'][option_value]['+trcount+'][weight_prefix]" class="btn plus-minus">';
                    optionValueItem += '<option value="+">+</option>';
                    optionValueItem += '<option value="-">-</option>';
                    optionValueItem += '</select>';
                    optionValueItem += '</span>';
                    optionValueItem += '<input type="text" name="product_options['+trid+'][option_value]['+trcount+'][weight]" value="" placeholder="Weight" class="form-control" aria-describedby="basic-addon1"/>';
                    optionValueItem += '</div>';
                    optionValueItem += '</td>';
                    optionValueItem += '<td style="text-align: center;"><a href="javascript:removeOptionValueRow('+trcount+')"><i class="fa fa-minus"></i></a></td>';
                    optionValueItem += '</tr>';
                    
                    $("#opt_val_table_" + trid).append(optionValueItem);
            }
            
            function removeOptionValueRow(trid) {                
                $("#option_value_row_"+trid).remove();
                $("#opt_val_table_"+trid + " tbody").find("tr").last().find("a").show();
            }
            
            function addQtyDiscount(){
                var trcount = $("#qty_discount_tbody tr").length;                
                var item = '<tr id="trqd'+trcount+'">';
                    item += '<td><input type="text" name="qty_discounts['+trcount+'][quantity]" class="form-control"/></td>';
                    item += '<td><input type="number" name="qty_discounts['+trcount+'][priority]" class="form-control"/></td>';
                    item += '<td><input type="text" name="qty_discounts['+trcount+'][price]" class="form-control"/></td>';
                    item += '<td><input type="text" name="qty_discounts['+trcount+'][start_date]" class="form-control date"/></td>';
                    item += '<td><input type="text" name="qty_discounts['+trcount+'][end_date]" class="form-control date"/></td>';
                    item += '<td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow(\'trqd'+trcount+'\')"><i class="fa fa-minus"></i></a></td>';
                    item += '</tr>';                    
                $('#qty_discount_tbody').append(item); 
                $('.date').datetimepicker({ format: "Y-MM-DD" });
            }
            
            function addSpecialDiscount(){
                var trcount = $("#special_discount_tbody tr").length;                
                var item = '<tr id="trsd'+trcount+'">';                    
                    item += '<td><input type="number" name="special_discounts['+trcount+'][priority]" class="form-control"/></td>';
                    item += '<td><input type="text" name="special_discounts['+trcount+'][price]" class="form-control"/></td>';
                    item += '<td><input type="text" name="special_discounts['+trcount+'][start_date]" class="form-control date"/></td>';
                    item += '<td><input type="text" name="special_discounts['+trcount+'][end_date]" class="form-control date"/></td>';
                    item += '<td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow(\'trsd'+trcount+'\')"><i class="fa fa-minus"></i></a></td>';
                    item += '</tr>';                    
                $('#special_discount_tbody').append(item); 
                $('.date').datetimepicker({ format: "Y-MM-DD" });
            }
            
            function addDownload(){
                var trcount = $("#downloads_tbody tr").length;                
                var item = '<tr id="trd'+trcount+'">';                    
                    item += '<td><input type="text" name="downloads['+trcount+'][name]" class="form-control"/></td>';
                    item += '<td>';
                    item += '<input type="file" data-id="'+trcount+'" class="form-control download"/>';
                    item += '<input type="hidden" id="download_'+trcount+'" name="downloads['+trcount+'][file_path]" class="form-control"/>';
                    item += '<span id="download_span_'+trcount+'"></span>';
                    item += '</td>';
                    item += '<td><input type="number" name="downloads['+trcount+'][max_downloads_time]" class="form-control"/>-1 for Unlimited</td>';
                    item += '<td><input type="number" name="downloads['+trcount+'][validity_days]" class="form-control"/>-1 for Unlimited</td>';
                    item += '<td><a class="btn btn-danger btn-sm pull-right" href="javascript:deleteRow(\'trd'+trcount+'\')"><i class="fa fa-minus"></i></a></td>';
                    item += '</tr>';                    
                $('#downloads_tbody').append(item); 
                $('.date').datetimepicker({ format: "Y-MM-DD" });
            }
            
            $("#downloads_tbody").on("change", ".download", function(e){                                      
                e.preventDefault();                                            
                var action = "<?= $sys['site_url']; ?>/requests.php?action=upload-product-download";
                if($(this).val() === "") {
                    return;
                }
                var id = $(this).attr("data-id");
                $("#download_span_" + id).html("Uploading...");
                var data = new FormData();
                data.append("download", e.target.files[0]);
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: data,
                    /*THIS MUST BE DONE FOR FILE UPLOADING*/
                    contentType: false,
                    processData: false,
                }).done(function(data){  
                    $("#download_span_" + id).html(data.msg);
                    if(data.code === '0') {   
                        $("#download_" + id).val(data.file_url);
                        $("#download_span_" + id).html("uploaded");
                    }  
                }).fail(function(data){
                    //any message
                });  

            });
        </script>     
    </body>
</html>