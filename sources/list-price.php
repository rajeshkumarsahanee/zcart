<?php
if (!Sys_isListerLogged()) {
  header("Location: " . $sys['config']['site_url'] . "/price-lister.php");
  exit();
}

if (isset($_REQUEST['pid'])) {
    $pid = filter_var(trim($_REQUEST['pid']), FILTER_SANITIZE_NUMBER_INT);
    $product = Sys_getProduct($pid);
    if($product == null) {
        echo "Invalid Product ID";
        exit();
    }
    $sys['description'] = $sys['config']['siteDescription'];
    $sys['keywords'] = $sys['config']['siteKeywords'];
    $sys['page'] = 'list-price';
    $sys['title'] = 'List Your Price for '.$product['name'];
    $sys['content'] = Sys_LoadPage('list-price');
}
