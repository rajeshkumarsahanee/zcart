<?php
if(!isUserLogged()) {
    header("location:" . $sys['site_url'] . "/login");
}
if (isset($_REQUEST['orderid'])) {
    $order_id = filter_var(trim($_REQUEST['orderid']), FILTER_SANITIZE_NUMBER_INT);

    $sys['description'] = $sys['site_meta_desc'];
    $sys['keywords'] = $sys['site_meta_keywords'];
    $sys['page'] = 'payment-failed';
    $sys['title'] = "Payment Failed";
    $sys['content'] = '<div class="container">'
            . '<br><br><br><br>'
            . '<div class="alert alert-danger">Payment Failed! <a href="' . $sys['site_url'] . '/pay?orderid=' . $order_id . '">Try Again</a></div>'
            . '</div>';
} else {
    echo "Provide Order ID";
    die();
}