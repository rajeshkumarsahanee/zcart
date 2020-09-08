<?php
if(!isUserLogged()) {
    header("location:" . $sys['site_url'] . "/login");
}
if (isset($_REQUEST['id'])) {
    $order_id = trim($_REQUEST['id']);
    $order = getOrder($order_id, array(), true);
    
    $sys['description'] = $sys['site_meta_desc'];
    $sys['keywords'] = $sys['site_meta_keywords'];
    $sys['page'] = 'order-details';
    $sys['title'] = "Order Details";
    $sys['content'] = loadPage('order-details');
} else {
    echo "Provide Order ID";
    die();
}