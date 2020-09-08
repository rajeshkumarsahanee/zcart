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
        echo "<script>alert('Deleted successfully'); location.href='product-reviews.php';</script>";
    } else {
        echo "<script>alert('Cannot be deleted'); location.href='product-reviews.php';</script>";
    }
}

$filters = array();
$querystring = "";
if (isset($_REQUEST['product_id'])) {
    $filters['product_id'] = filter_var(trim($_REQUEST['product_id']), FILTER_SANITIZE_NUMBER_INT);
    $querystring .= "&product_id=" . $_REQUEST['product_id'];
    $product = getProduct($filters['product_id']);
    if(empty($product)) {
        header('location: product-reviews.php');
        exit();
    }
}
if (isset($_REQUEST['shop_id'])) {
    $filters['shop_id'] = filter_var(trim($_REQUEST['shop_id']), FILTER_SANITIZE_NUMBER_INT);
    $querystring .= "&shop_id=" . $_REQUEST['shop_id'];
    $shop = getShop($filters['shop_id']);
    if(empty($shop)) {
        header('location: product-reviews.php');
        exit();
    }
}
if (isset($_REQUEST['user_id'])) {
    $filters['user_id'] = filter_var(trim($_REQUEST['user_id']), FILTER_SANITIZE_STRING);
    $querystring .= "&user_id=" . $_REQUEST['user_id'];
    $user = getUser($filters['user_id']);
    if(empty($user)) {
        header('location: product-reviews.php');
        exit();
    }
}
if (isset($_REQUEST['q'])) {
    $filters['query'] = filter_var(trim($_REQUEST['q']), FILTER_SANITIZE_STRING);
    $querystring .= "&q=" . $_REQUEST['q'];
}
/* pagination logic start */
$items_count = count(getProductReviews(array('id'), $filters, 0, -1));
$items_per_page = isset($config['items_per_page_review_admin']) ? $config['items_per_page_review_admin'] : 20;
$max_pages = intval($items_count / $items_per_page + 1);
$current_page = !isset($_REQUEST['paged']) || intval($_REQUEST['paged']) < 1 ? 1 : filter_var(trim($_REQUEST['paged']), FILTER_SANITIZE_NUMBER_INT);
if ($current_page > $max_pages) {
    header("location: product-reviews.php?" . $querystring . "&paged=" . $max_pages);
    exit();
}
$offset = $items_per_page * $current_page - $items_per_page;
/* pagination logic end */
$order_by = isset($_REQUEST['order_by']) && in_array($_REQUEST['order_by'], array("id","rating", "review_timestamp")) ? filter_var(trim($_REQUEST['order_by']), FILTER_SANITIZE_STRING) : "id";
$order = isset($_REQUEST['order']) && in_array($_REQUEST['order'], array("asc", "desc")) ? filter_var(trim($_REQUEST['order']), FILTER_SANITIZE_STRING) : "DESC";
$reviews = getProductReviews(array('id', 'product_id', 'shop_id', 'user_id', 'name', 'review', 'rating', 'review_timestamp', 'status'), $filters, $offset, $items_per_page, $order_by, $order);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= isset($shop) ? $shop['name'] . ' ' : '' ?>Product Reviews<?= isset($product) ? ' - ' . $product['name'] : '' ?><?= isset($user) ? ' - ' . $user['name'] : '' ?> - Admin</title>
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
                        <?= isset($shop) ? $shop['name'] . ' ' : '' ?>Product Reviews<?= isset($category) ? ' - ' . $category['name'] : '' ?>
                        <small></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
                        <li><a href="#">Catalog</a></li>
                        <li class="active">Product Reviews</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <form id="product-reviews-form" method="get">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="" style="float:left">
                                    <a href="<?= $sys['site_url'] ?>/admin/product-reviews.php">All <span class="count">(<?= count(getProductReviews(array('id'), array(), 0, -1)) ?>)</span></a>
                                </div>
                                <div class="" style="float:right">
                                    <input type="text" class="form-control" name="q" value="<?= isset($_REQUEST['q']) ? $_REQUEST['q'] : "" ?>" style="width:auto; float:left;padding: 0px 2px;max-height: 30px;">
                                    <input type="submit" style="float:left;" name="search" value="Search Review" class="btn btn-default btn-sm">
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
                                    <a class="first-page btn btn-default btn-sm btn-flat" href="product-reviews.php?<?= $querystring . '&paged=1' ?>"><i class="fa fa-angle-double-left"></i></a>
                                    <a class="previous-page btn btn-default btn-sm btn-flat" href="product-reviews.php?<?= $querystring . '&paged=' . ($current_page > 1 ? $current_page - 1 : 1) ?>"><i class="fa fa-angle-left"></i></a>
                                    <span class="paging-input"><input class="btn btn-sm btn-flat" style="cursor:auto;max-width: 50px;padding: 4px 10px;" id="current-page-selector" type="text" name="paged" value="<?= $current_page ?>"> of <?= $max_pages ?></span>
                                    <a class="next-page btn btn-default btn-sm btn-flat" href="product-reviews.php?<?= $querystring . '&paged=' . ($current_page < $max_pages ? $current_page + 1 : $max_pages) ?>"><i class="fa fa-angle-right"></i></a>
                                    <a class="last-page btn btn-default btn-sm btn-flat" href="product-reviews.php?<?= $querystring . '&paged=' . $max_pages ?>"><i class="fa fa-angle-double-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="box">
                            <div class="table-responsive" style="width: 100%;">
                                <table id="products" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="checkall"/></th>
                                            <th>Product</th>
                                            <th>Shop</th>
                                            <th>Reviewed By</th>
                                            <th><a href="product-reviews.php?order_by=rating&order=<?= $order_by == "rating" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Rating <?= $order_by == "rating" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th><a href="product-reviews.php?order_by=review_timestamp&order=<?= $order_by == "review_timestamp" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Date <?= $order_by == "review_timestamp" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reviews as $review) { 
                                            $p = getProduct($review['product_id'], $review['shop_id'], array('id', 'name'));
                                            $s = getShop($review['shop_id'], array('id', 'name'));
                                            $u = getUser($review['user_id']);
                                            ?>
                                            <tr>
                                                <th><input type="checkbox" name="review[]" value="<?= $review['id'] ?>"/></th>
                                                <td><?= $p['name'] ?></td>
                                                <td><?= $s['name'] ?></td>
                                                <td><?= $u['name'] ?></td>
                                                <td><?= $review['rating'] ?></td>
                                                <td><?= $review['review_timestamp'] ?></td>
                                                <td><?= $review['status'] ?></td>
                                                <td>
                                                    <div class='btn-group'>
                                                        <?php if (isUserHavePermission(PRODUCT_REVIEWS_SECTION, getUserLoggedId())) { ?>
                                                        <a class='btn btn-sm btn-primary' href="<?= 'product-review-edit.php?id=' . $review['id'] . '&shop=' . $review['shop_id'] ?>" title="Edit"><i class="fa fa-pencil"></i></a>
                                                        <a class='btn btn-sm btn-danger' href="<?= 'product-reviews.php?del=' . $review['id']; ?>" onclick="return confirm('Are you sure you want to delete this review?')" title="Delete"><i class="fa fa-trash"></i></a>
                                                        <?php } ?>                                                    
                                                    </div>
                                                </td>                                                    
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th><input type="checkbox" class="checkall"/></th>
                                            <th>Product</th>
                                            <th>Shop</th>
                                            <th>Reviewed By</th>
                                            <th><a href="product-reviews.php?order_by=rating&order=<?= $order_by == "rating" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Rating <?= $order_by == "rating" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th><a href="product-reviews.php?order_by=review_timestamp&order=<?= $order_by == "review_timestamp" ? ($order == "asc" ? "desc" : "asc") : "asc" ?><?= $querystring ?>">Date <?= $order_by == "review_timestamp" ? ($order == "asc" ? '<i class="fa fa-arrow-up"></a>' : '<i class="fa fa-arrow-down"></i>') : "" ?></a></th>
                                            <th>Status</th>
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