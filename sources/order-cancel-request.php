<?php
if(!isUserLogged()) {
    header("location:" . $sys['site_url'] . "/login");
}
if (isset($_REQUEST['id'])) {
    $order_id = trim($_REQUEST['id']);
    $order = getOrder($order_id, array(), true);
    
    $sys['description'] = $sys['site_meta_desc'];
    $sys['keywords'] = $sys['site_meta_keywords'];
    $sys['page'] = 'order-cancel-request';
    $sys['title'] = "Order Cancel Request";
    $sys['content'] = loadPage('order-cancel-request');
} else {
    echo "Provide Order ID";
    die();
}