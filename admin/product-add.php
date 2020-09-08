<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(MANAGE_PRODUCTS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
    exit();
}

$msg = "";
$savemsg = "";
$savevariantmsg = "";
$savevariantcsmsg = "";

if (isset($_REQUEST['action']) && trim($_REQUEST['action']) == "new") {                       

    if (isset($_POST['save'])) {
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
        $product['display_order'] = isset($_POST['display_order']) && !empty($_POST['display_order'])? filter_var(trim($_POST['display_order']), FILTER_SANITIZE_NUMBER_INT) : 1;
        $product['featured_product'] = isset($_POST['featured_product']) ? 'Y' : 'N';
        $product['meta_title'] = filter_var(trim($_POST['meta_title']), FILTER_SANITIZE_STRING);
        $product['meta_keywords'] = filter_var(trim($_POST['meta_keywords']), FILTER_SANITIZE_STRING);
        $product['meta_description'] = htmlspecialchars(addslashes(trim($_POST['meta_description'])));
        $product['status'] = filter_var(trim($_POST['status']), FILTER_SANITIZE_STRING);
        $product['added_by'] = getUserLoggedId();
        $product['updated_by'] = getUserLoggedId();
        $product['added_timestamp'] = date("Y-m-d H:i:s");
        $product['updated_timestamp'] = date("Y-m-d H:i:s");
        $product['shop_id'] = filter_var(trim($_POST['shop_id']), FILTER_SANITIZE_STRING);
                
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
        
        if ($product['shop_id'] == "") {
            $savemsg = '<div class="alert alert-danger">Shop not selected</div>';
        } else if($product['sku'] == "" || $product['name'] == "" || $product['slug'] == "") {
            $savemsg = '<div class="alert alert-danger">Sku, Name and Slug are required</div>';
        } else if($price['price'] == "" || $price['stock'] == "" || $price['min_order_qty'] == "" || $price['hsn_code'] == "" || $price['tax_code'] == "") {
            $savemsg = '<div class="alert alert-danger">Price, Stock, Minimum Quantity, HSN Code and Tax Code are required fields</div>';
        } else if($product['brand'] == "" || $product['model'] == "") {
            $savemsg = '<div class="alert alert-danger">Brand and Model are required</div>';
        } else {
            if (addProduct($product)) {
                $savemsg = '<div class="alert alert-success">Product added successfully!</div>';
            } else {
                $savemsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
            }
        }
    }
}
/*
if (isset($_REQUEST['sku_variant'])) {
    $sku_variant = trim($_REQUEST['sku_variant']);
    $product = Sys_getProductBySku($sku_variant);
    $category = Sys_getCategory($product['category_id']);
    $attributes = Sys_getAttributesByCategoryID($product['category_id']);
    $defaultseller = $product['sellers'][$sys['config']['defaultSeller']];    
    foreach($defaultseller['prices'] as $seller_price) {
        if($seller_price['color'] == "NA" && $seller_price['size'] == "NA") {
            unset($seller_price['id']);
            $defaultseller = array_merge($defaultseller, $seller_price);
            break;            
        }
    }
    $affiliates = Sys_getAffiliates();
    $productcategoryids = Sys_getCategoryIdsByProductId(trim($product['id']));
    if($product == null) {
        unset($sku_variant);
        $msg = '<div style="color:red; font-weight:bold;">Invalid SKU</div>';
    }
    
    if (isset($_POST['save_variant'])) {
        $product['category_id'] = filter_var(trim($_POST['category']), FILTER_SANITIZE_STRING);
        $product['sku'] = filter_var(trim($_POST['sku']), FILTER_SANITIZE_STRING);
        $product['name'] = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
        $product['slug'] = filter_var(trim($_POST['slug']), FILTER_SANITIZE_STRING);
        $product['short_description'] = htmlspecialchars(addslashes(trim($_POST['short_description'])));
        $product['long_description'] = htmlspecialchars(addslashes(trim($_POST['long_description'])));
        $product['images'] = trim($_POST['images']);
        $product['stock'] = 1;
        $product['special'] = $_POST['special'];
        $product['latest'] = $_POST['latest'];
        $product['views'] = filter_var(trim($_POST['views']), FILTER_SANITIZE_NUMBER_INT);
        $product['likes'] = filter_var(trim($_POST['likes']), FILTER_SANITIZE_NUMBER_INT);
        $product['meta_title'] = filter_var(trim($_POST['meta_title']), FILTER_SANITIZE_STRING);
        $product['meta_keywords'] = filter_var(trim($_POST['meta_keywords']), FILTER_SANITIZE_STRING);
        $product['meta_description'] = htmlspecialchars(addslashes(trim($_POST['meta_description'])));
        $product['added_by'] = $_SESSION['admin_username'];
        $product['categoriesR'] = isset($_POST['categories']) ? $_POST['categories'] : array();

        $postedattributes = array();
        foreach ($attributes as $attribute) {
            $attribute['value'] = trim($_POST[$attribute['id']]);
            $postedattributes[] = $attribute;
        }
        $product['attributesR'] = $postedattributes;

        $seller['in_stock'] = isset($_POST['pin_stock']) ? trim($_POST['pin_stock']) : "Y";
        $seller['id'] = $_POST['defaultsellerid'];
        $seller['color'] = "NA";
        $seller['size'] = "NA";
        $seller['images'] = trim($_POST['images']);
        $seller['price'] = trim($_POST['pprice']);
        $seller['shipping'] = trim($_POST['pshipping']);
        $seller['marketplace_fees'] = $seller['price'] * ($sys['config']['marketPlaceFees'] / 100);
        $seller['tax'] = trim($_POST['ptax']);
        $seller['selling_price'] = $seller['price'] + $seller['shipping'] + $seller['marketplace_fees'] + $seller['tax'];
        $seller['percent_discount'] = trim($_POST['percent_discount']);
        $seller['active_discount'] = isset($_POST['active_discount']) ? "Y" : "N";
        $product['seller'] = $seller;

        $postedaffiliates = array();
        foreach ($affiliates as $affiliate) {
            $affiliate['url'] = trim($_POST['pau' . $affiliate['id']]);
            $affiliate['price'] = trim($_POST['pap' . $affiliate['id']]);
            $postedaffiliates[] = $affiliate;
        }
        $product['affiliatesR'] = $postedaffiliates;

        if (Sys_addProduct($product)) {
            $savevariantmsg = '<div class="alert alert-success">Product variant added successfully!</div>';
        } else {
            $savevariantmsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

if (isset($_REQUEST['sku_variant_cs'])) {
    $sku_variant_cs = trim($_REQUEST['sku_variant_cs']);
    $product = Sys_getProductBySku($sku_variant_cs);
    $category = Sys_getCategory($product['category_id']);
    $sellers = Sys_getSellers();
    $defaultseller = $product['sellers'][$sys['config']['defaultSeller']];
    if ($product == null) {
        unset($sku_variant_cs);
        $msg = '<div style="color:red; font-weight:bold;">Invalid SKU</div>';
    }

    if (isset($_POST['save_variant_cs'])) {
        $product['id'] = filter_var(trim($_POST['id']), FILTER_SANITIZE_STRING);        
        $seller['in_stock'] = isset($_POST['pin_stock']) ? trim($_POST['pin_stock']) : "Y";
        $seller['id'] = $_POST['defaultsellerid'];
        $seller['color'] = $_POST['color'];
        $seller['size'] = $_POST['size'];
        $seller['images'] = trim($_POST['images']);
        $seller['price'] = trim($_POST['pprice']);
        $seller['shipping'] = trim($_POST['pshipping']);
        $seller['marketplace_fees'] = $seller['price'] * ($sys['config']['marketPlaceFees'] / 100);
        $seller['tax'] = trim($_POST['ptax']);
        $seller['selling_price'] = $seller['price'] + $seller['shipping'] + $seller['marketplace_fees'] + $seller['tax'];        
        $seller['percent_discount'] = trim($_POST['percent_discount']);
        $seller['active_discount'] = isset($_POST['active_discount']) ? "Y" : "N";
        $product['seller'] = $seller;

        if (Sys_addProductCSVariant($product)) {
            $savevariantcsmsg = '<div class="alert alert-success">Product color/size variant saved successfully!</div>';
        } else {
            $savevariantcsmsg = '<div class="alert alert-danger">' . $queryerrormsg . '</div>';
        }
    }
}

if (isset($_REQUEST['import'])) {
    $import = trim($_REQUEST['import']);    
    $category = Sys_getCategory($import);
    //$sellers = Sys_getSellers();
    //$defaultseller = $product['sellers'][$sys['config']['defaultSeller']];
//    if ($product == null) {
//        unset($sku_variant_cs);
//        $msg = '<div style="color:red; font-weight:bold;">Invalid SKU</div>';
//    }

    if (isset($_POST['save_import'])) {
        $importmsg = "";
        
        if (is_uploaded_file($_FILES['csvfile']['tmp_name'])) {
            $importmsg = "<h3>" . "File " . $_FILES['csvfile']['name'] . " uploaded successfully." . "</h3>";
        }

        //Import uploaded file to Database
        $handle = fopen($_FILES['csvfile']['tmp_name'], "r");
        $skip = true;

        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
            if ($skip) {
                $skip = FALSE;
                continue; //skipping fist line
            }

            $attributes = Sys_getAttributesByCategoryID(trim($_POST['category_id']));            
            $affiliates = Sys_getAffiliates();
            
            $product = array();
            $product['category_id'] = filter_var(trim($_POST['category_id']), FILTER_SANITIZE_STRING);             
            $product['sku'] = filter_var(trim($data[0]), FILTER_SANITIZE_STRING);
            $product['name'] = filter_var(trim($data[1]), FILTER_SANITIZE_STRING);
            $product['slug'] = filter_var(trim($data[2]), FILTER_SANITIZE_STRING);
            $product['short_description'] = htmlspecialchars(addslashes(trim($data[3])));
            $product['long_description'] = htmlspecialchars(addslashes(trim($data[4])));
            $product['images'] = filter_var(trim($data[5]), FILTER_SANITIZE_STRING);
            $product['stock'] = filter_var(trim($data[6]), FILTER_SANITIZE_STRING);
            $product['special'] = filter_var(trim($data[7]), FILTER_SANITIZE_STRING);
            $product['latest'] = filter_var(trim($data[8]), FILTER_SANITIZE_STRING);
            $product['views'] = filter_var(trim($data[9]), FILTER_SANITIZE_NUMBER_INT);
            $product['likes'] = filter_var(trim($data[10]), FILTER_SANITIZE_NUMBER_INT);
            $product['meta_title'] = filter_var(trim($data[11]), FILTER_SANITIZE_STRING);
            $product['meta_keywords'] = filter_var(trim($data[12]), FILTER_SANITIZE_STRING);
            $product['meta_description'] = htmlspecialchars(addslashes(trim($data[13])));
            $product['added_by'] = $_SESSION['admin_username'];
            $product['categoriesR'] = !empty($data[14]) ? explode(";", $data[14]) : array();

            $i = 15;
            
            $postedattributes = array();
            foreach ($attributes as $attribute) {
                $attribute['value'] = trim($data[$i++]);
                $postedattributes[] = $attribute;
            }
            $product['attributesR'] = $postedattributes;
            
            $seller = array();
            $seller['id'] = $sys['config']['defaultSeller'];
            $seller['in_stock'] = isset($data[$i]) ? trim($data[$i]) : "Y"; $i++;
            $seller['color'] = !empty(trim($data[$i])) ? trim($data[$i]) : "NA"; $i++;
            $seller['size'] = !empty(trim($data[$i])) ? trim($data[$i]) : "NA"; $i++;
            $seller['images'] = trim($data[$i++]);
            $seller['price'] = trim($data[$i++]);
            $seller['shipping'] = trim($data[$i++]);
            $seller['marketplace_fees'] = $seller['price'] * ($sys['config']['marketPlaceFees'] / 100); $i++;
            $seller['tax'] = !empty(trim($data[$i])) ? trim($data[$i]) : 0; $i++;
            $seller['selling_price'] = $seller['price'] + $seller['shipping'] + $seller['marketplace_fees'] + $seller['tax']; $i++;
            $seller['percent_discount'] = !empty(trim($data[$i])) ? trim($data[$i]) : 0; $i++;
            $seller['active_discount'] = (isset($data[$i++]) && trim($data[$i]) == "Y") ? "Y" : "N";
            $product['seller'] = $seller;            
            
            $postedaffiliates = array();
            foreach ($affiliates as $affiliate) {
                $affiliate['url'] = trim($data[$i++]);
                $affiliate['price'] = trim($data[$i++]);
                $postedaffiliates[] = $affiliate;
            }
            $product['affiliatesR'] = $postedaffiliates;
            
            if (Sys_addProduct($product)) {               
                $importmsg .= $product['name'] . ' imported successfully!<br/>';
            } else {
                $importmsg .= $queryerrormsg . '<br/>';
            }
           
        }
        fclose($handle);
    }
}

if (isset($_REQUEST['downloadsample'])) {
    $catid = trim($_REQUEST['downloadsample']);
    $category = Sys_getCategory($catid);
    $attributes = Sys_getAttributesByCategoryID($catid);
    $affiliates = Sys_getAffiliates();

    $output = "sku, name, slug, short_description, long_description, images(semicolon separated images url), stock, special, latest, views, likes, meta_title, meta_keywords, meta_description, additional_categories(semicolon separated ids), ";

    foreach ($attributes as $attribute) {
        $output .= '' . $attribute['name'] . '(' . $attribute['id'] . '),';
    }
    $output .= "d_seller_in_stock(Y/N), d_seller_color, d_seller_size, d_seller_images(semicolon separated), d_seller_price, d_seller_shipping, d_seller_marketplace_fees(leave blank), d_seller_tax, d_seller_selling_price(leave blank), d_seller_percent_discount(%), d_seller_active_discount(Y/N), ";

    foreach ($affiliates as $affiliate) {        
        $output .= $affiliate['name'] . ' url(' . $affiliate['id'] . '),';
        $output .= $affiliate['name'] . ' price(' . $affiliate['id'] . '),';
    }
    $output = trim($output, ",");

    $filename = strtolower($category['name']) . ".csv";
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    echo $output;
    exit();
}
*/

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
        <title>Add Product - Admin</title>
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
                        Add Product
                        <small>Add new product from this section</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="">Catalog</li>
                        <li class="active"><a href="<?= $sys['site_url'] ?>/admin/products.php">Products</a></li>
                        <li class="active">Add New Product</li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">  
                    <?php 
                    if (isset($_REQUEST['action']) && trim($_REQUEST['action']) == "new") {
                        include_once 'product_add_new.php';
                    } 
                    ?>
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
        <?php if(isset($_REQUEST['action']) && trim($_REQUEST['action']) == "new") { ?>
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
                    console.log($(this).val() + " == " + selectedproductid);
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
                //clear shops list
                $("#hsncodelist").html("");
                $("#hsncodelist").css("border", "none");
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
                    optionValueContent +='<option value="Y">Required</option>';
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
                    optionValueContent +='<option value="Y">Required</option>';
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
                    optionValueContent +='<option value="Y">Required</option>';
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
                    optionValueContent +='<option value="Y">Required</option>';
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
                    optionValueItem += '<option value="Y">Yes</option>';
                    optionValueItem += '<option value="N">No</option>';
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
        <?php } ?>
        
        <?php if(isset($_REQUEST['action']) && trim($_REQUEST['action']) == "sku-variant") { ?>
        <script>
            function getSubcategories() {
                alert("get subcategories ajax");
            }                       
            
            $("#pprice").change(function(){
                calculate();
            });
            $("#pshipping").change(function(){
                calculate();
            });
            $("#ptax").change(function(){
                calculateWithDiffTax()();
            });
            
            function calculate() {
                var price = parseFloat($("#pprice").val()); 
                var shipping = parseFloat($("#pshipping").val());
                var fixrate = parseFloat($("#fixedrate").val());
                var fixcharge = price * (fixrate / 100)
                var taxrate = parseFloat($("#taxrate").val());
                var tax = price * (taxrate / 100);
                $("#pmarketplacechage").val(fixcharge);
                $("#ptax").val(tax);
                $("#psellingprice").val(price + shipping + fixcharge + tax);
            }
            
            function calculateWithDiffTax() {
                var price = parseFloat($("#pprice").val()); 
                var shipping = parseFloat($("#pshipping").val());
                var fixrate = parseFloat($("#fixedrate").val());
                var fixcharge = price * (fixrate / 100)                
                var tax = parseFloat($("#ptax").val());
                $("#pmarketplacechage").val(fixcharge);                
                $("#psellingprice").val(price + shipping + fixcharge + tax);
            }
            
            function fetchFromUrl() {
                var url = $("#fetchfromurl").val();                
                if (url === '') {
                    alert("Please enter url");
                } else {
                    $("#loading").show();
                    var action = "<?= $sys['site_url']; ?>/requests.php?f=gethtml";
                    var data = new FormData();
                    data.append("from_url", url);                    
                    $.ajax({
                        type: 'POST',
                        url: action,
                        data: data,   
                        /*THIS MUST BE DONE FOR FILE UPLOADING*/
                        contentType: false,
                        processData: false,
                    }).done(function(data){                              
                        if(data.code === '0') {                                                                                    
                            fillForm(url, data.html, '<?php echo $selectedcategory['name']; ?>');
                            $("#loading").hide();
                        }                        
                    }).fail(function(data){
                        $("#loading").hide();
                    });  
                    
                }
            }
            
            $("#searchattribute").keyup(function(){
                var q = $(this).val().toLowerCase();
                $('.attribute').each(function(){
                    var div = $(this);        

                    if(div.text().toLowerCase().indexOf(q) >= 0){
                        div.show();
                    } else {
                        div.hide();
                    }
                });
            });
            
            $("#variant_sku").change(function(){
               var variant_sku = $("#variant_sku").val(); 
               var hidden_sku = $("#skuhidden").val(); 
               $("#sku").val(hidden_sku + "-" + variant_sku.trim());
            });
        </script>        
        <?php } ?>        
    </body>
</html>