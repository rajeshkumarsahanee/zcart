<?php

if (!isUserLogged()) {
    header("location:" . $sys['site_url'] . "/login?redirect=checkout");
    exit();
}
if (isset($_REQUEST['orderid'])) {
    $order_id = filter_var(trim($_REQUEST['orderid']), FILTER_SANITIZE_NUMBER_INT);
    $order = getOrder($order_id);
    if ($order == null) {
        header("location:" . $sys['site_url'] . "/checkout");
        exit();
    }
    if($order['payment_status'] !== "1") {
        header("location:" . $sys['site_url'] . "/order-details?id=" . $order['id']);
        exit();
    }
    switch ($order['payment_method']) {
        case 'amazon' : 
            require_once dirname(dirname(__FILE__)) . '/payment-methods/amazon/request-form.php';
            break;
        case 'ccavenue' : 
            require_once dirname(dirname(__FILE__)) . '/payment-methods/ccavenue/request-form.php';
            break;
        case 'paypal' : 
            require_once dirname(dirname(__FILE__)) . '/payment-methods/paypal/request-form.php';
            break;
        case 'paytm' : 
            require_once dirname(dirname(__FILE__)) . '/payment-methods/paytm/request-form.php';
            break;
        case 'payubizindia' : 
            require_once dirname(dirname(__FILE__)) . '/payment-methods/payubizindia/request-form.php';
            break;
        case 'payumoneyindia' : 
            require_once dirname(dirname(__FILE__)) . '/payment-methods/payumoneyindia/request-form.php';
            break;
        case 'razorpay' : 
            require_once dirname(dirname(__FILE__)) . '/payment-methods/razorpay/request-form.php';
            break;
    }
} else {
    echo "Provide Order ID";
    exit();
}