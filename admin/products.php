<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(MANAGE_PRODUCTS_SECTION, getUserLoggedId())) {
    header("location: dashboard.php");
}

//Delete Product
if (isset($_GET['del']) && isUserHavePermission(MANAGE_PRODUCTS_SECTION, getUserLoggedId())) {
    if (deleteProduct($_GET['del'])) {
        echo "<script>alert('Deleted successfully'); location.href='products.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='products.php';</script>";
    }
}

$filters = array();
$shop_id = null;
$querystring = "";
if (isset($_REQUEST['shop'])) {
    $filters['shop'] = $shop_id = filter_var(trim($_REQUEST['shop']), FILTER_SANITIZE_NUMBER_INT);
    $querystring .= "&shop=" . $_REQUEST['shop'];
    $shop = getShop($filters['shop']);
    if(empty($shop)) {
        header('location: products.php');
        exit();
    }
}
if (isset($_REQUEST['category'])) {
    $filters['category'] = filter_var(trim($_REQUEST['category']), FILTER_SANITIZE_NUMBER_INT);
    $querystring .= "&category=" . $_REQUEST['category'];
    $category = getCategory($filters['category']);
    if(empty($category)) {
        header('location: products.php');
        exit();
    }
}
if (isset($_REQUEST['featured_product'])) {
    $filters['featured_product'] = filter_var(trim($_REQUEST['featured_product']), FILTER_SANITIZE_STRING);
    $querystring .= "&featured_product=" . $_REQUEST['featured_product'];
}
if (isset($_REQUEST['q'])) {
    $filters['query'] = filter_var(trim($_REQUEST['q']), FILTER_SANITIZE_STRING);
    $querystring .= "&q=" . $_REQUEST['q'];
}
/* pagination logic start */
$items_count = count(getProducts(array('id'), $filters, 0, -1));
$items_per_page = isset($config['items_per_page_product_admin']) ? $config['items_per_page_product_admin'] : 20;
$max_pages = intval($items_count / $items_per_page + 1);
$current_page = !isset($_REQUEST['paged']) || intval($_REQUEST['paged']) < 1 ? 1 : filter_var(trim($_REQUEST['paged']), FILTER_SANITIZE_NUMBER_INT);
if ($current_page > $max_pages) {
    header("location: products.php?" . $querystring . "&paged=" . $max_pages);
    exit();
}
$offset = $items_per_page * $current_page - $items_per_page;
/* pagination logic end */
$order_by = isset($_REQUEST['order_by']) && in_array($_REQUEST['order_by'], array("name", "type", "orders", "price")) ? filter_var(trim($_REQUEST['order_by']), FILTER_SANITIZE_STRING) : "id";
$order = isset($_REQUEST['order']) && in_array($_REQUEST['order'], array("asc", "desc")) ? filter_var(trim($_REQUEST['order']), FILTER_SANITIZE_STRING) : "DESC";
$products = getProducts(array('id', 'name', 'type', 'orders', 'price'), $filters, $offset, $items_per_page, $order_by, $order);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= isset($shop) ? $shop['name'] . ' ' : '' ?>Products<?= isset($category) ? ' - ' . $category['name'] : '' ?> - Admin</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php include 'css.php'; ?>
        <link rel="stylesheet" href="<?= $sys['site_url']; ?>/admin/plugins/iCheck/flat/blue.css">
        <style>
             #products {
                margin-bottom: 0;
            }
            #products th:first-child {
                width: 50px;
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
                        <?= isset($shop) ? $shop['name'] . ' ' : '' ?>Products<?= isset($category) ? ' - ' . $category['name'] : '' ?>
                        <small><a href="<?= $sys['site_url'] ?>/admin/product-add.php?action=new" class="btn btn-default btn-sm">Add New</a></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li class="active">Products</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form id="comments-form" method="get">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="" style="float:left">
                                    <a href="<?= $sys['site_url'] ?>/admin/products.php">All <span class="count">(<?= count(getProducts(array('id'), array(), 0, -1)) ?>)</span></a> |
                                    <a href="<?= $sys['site_url'] ?>/admin/products.php?featured_product=Y">Featured <span class="count">(<?= count(getProducts(array('id'), array('featured_product' => 'Y'), 0, -1)) ?>)</span></a>                          
                                </div>
                                <div class="" style="float:right">
                                    <input type="text" class="form-control" name="q" value="<?= isset($_REQUEST['q']) ? $_REQUEST['q'] : "" ?>" style="width:auto; float:left;padding: 0px 2px;max-height: 30px;">
                                    <input type="submit" style="float:left;" name="search" value="Search Product" class="btn btn-default btn-sm">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8" style="margin: 3px 0px;">
                                <select name="action" id="bulk-action-selector-top" style="max-width: 150px;float: left;padding: 0px 5px;max-height: 30px;margin-right: 2px;" class="form-control">
                                    <option value="-1">Bulk Actions</option>
                                    <option value="edit" class="hide-if-no-js">Edit</option>
                                    <option value="trash">Move to Trash</option>
                                </select>                            
                                <input type="submit" id="doaction" class="btn btn-sm btn-default action" value="Apply">
                            </div>
                            <div class="col-md-4" style="margin: 3px 0px;">
                                <div class="" style="float:right">
                                    <span class="displaying-num"><?= $items_count ?> items</span>
                                    <a class="first-page btn btn-default btn-sm btn-flat" href="products.php?<?= $querystring . '&paged=1' ?>"><i class="fa fa-angle-double-left"></i></a>
                                    <a class="previous-page btn btn-default btn-sm btn-flat" href="products.php?<?= $querystring . '&paged=' . ($current_page > 1 ? $current_page - 1 : 1) ?>"><i class="fa fa-angle-left"></i></a>
                                    <span class="paging-input"><input class="btn btn-sm btn-flat" style="cursor:auto;max-width: 50px;padding: 4px 10px;" id="current-page-selector" type="text" name="paged" value="<?= $current_page ?>"> of <?= $max_pages ?></span>
                                    <a class="next-page btn btn-default btn-sm btn-flat" href="products.php?<?= $querystring . '&paged=' . ($current_page < $max_pages ? $current_page + 1 : $max_pages) ?>"><i class="fa fa-angle-right"></i></a>
                                    <a class="last-page btn btn-default btn-sm btn-flat" href="products.php?<?= $querystring . '&paged=' . $max_pages ?>"><i class="fa fa-angle-double-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="box">
                            <div class="table-responsive" style="width: 100%;">
                                <table id="products" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="checkall"/></th>
                                            <th><a href="products.php?order_by=name&order=<?= $order_by == "name" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Name <?= $order_by == "name" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th><a href="products.php?order_by=type&order=<?= $order_by == "type" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Type <?= $order_by == "type" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th><a href="products.php?order_by=orders&order=<?= $order_by == "orders" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Orders <?= $order_by == "orders" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Shop</th>
                                            <th>Available</th>
                                            <th><a href="products.php?order_by=price&order=<?= $order_by == "price" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Price <?= $order_by == "price" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product) { 
                                            $p = getProductPrice($product['id'], $shop_id);
                                            $s = getShop($p['shop_id'], array('id', 'name'));
                                            ?>
                                            <tr>
                                                <th><input type="checkbox" name="products[]" value="<?= $product['id'] ?>"/></th>
                                                <td><?= $product['name'] ?></td>
                                                <td><?= $product['type'] ?></td>
                                                <td><?= $product['orders'] ?></td>
                                                <td><a href="products.php?shop=<?= $s['id'] ?>"><?= $s['name'] ?></a></td>
                                                <td><?= $p['stock'] ?></td>
                                                <td><?= $product['price'] ?></td>
                                                <td>
                                                    <div class='btn-group'>
                                                        <?php if (isUserHavePermission(MANAGE_PRODUCTS_SECTION, getUserLoggedId())) { ?>
                                                        <a class='btn btn-sm btn-primary' href="<?= 'product-edit.php?id=' . $product['id'] . '&shop=' . $p['shop_id'] ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                        <a class='btn btn-sm btn-danger' href="<?= 'products.php?del=' . $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')" title="Delete"><i class="fa fa-trash"></i></a>
                                                        <?php } ?>                                                    
                                                    </div>
                                                </td>                                                    
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th><input type="checkbox" class="checkall"/></th>
                                            <th><a href="products.php?order_by=name&order=<?= $order_by == "name" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Name <?= $order_by == "name" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th><a href="products.php?order_by=type&order=<?= $order_by == "type" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Type <?= $order_by == "type" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th><a href="products.php?order_by=orders&order=<?= $order_by == "orders" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Orders <?= $order_by == "orders" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Shop</th>
                                            <th>Available</th>
                                            <th><a href="products.php?order_by=price&order=<?= $order_by == "price" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Price <?= $order_by == "price" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div><!-- /.box -->
                    </form>
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
        </script>  
    </body>
</html>