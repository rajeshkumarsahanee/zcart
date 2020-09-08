<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php

//Not authorized to access
if (!isset($_REQUEST['code']) || trim($_REQUEST['code']) == "" || !isUserHavePermission(PAYMENT_METHODS_SECTION, getUserLoggedId())) {
    header("location: settings-payment-method.php");
    exit();
}

$code = filter_var(trim($_REQUEST['code']), FILTER_SANITIZE_STRING);
//Update Payment Method Fields
switch ($code) {
    case 'amazon':
        $file = dirname(dirname(__FILE__)) . '/payment-methods/amazon/index.php';
        break;
    case 'banktransfer':
        $file = dirname(dirname(__FILE__)) . '/payment-methods/banktransfer/index.php';
        break;
    case 'cashondelivery':
        $file = dirname(dirname(__FILE__)) . '/payment-methods/cashondelivery/index.php';
        break;
    case 'ccavenue':
        $file = dirname(dirname(__FILE__)) . '/payment-methods/ccavenue/index.php';
        break;
    case 'paypal':
        $file = dirname(dirname(__FILE__)) . '/payment-methods/paypal/index.php';
        break;
    case 'paytm':
        $file = dirname(dirname(__FILE__)) . '/payment-methods/paytm/index.php';
        break;
    case 'payumoneyindia':
        $file = dirname(dirname(__FILE__)) . '/payment-methods/payumoneyindia/index.php';
        break;
    case 'payubizindia':
        $file = dirname(dirname(__FILE__)) . '/payment-methods/payubizindia/index.php';
        break;
    case 'razorpay':
        $file = dirname(dirname(__FILE__)) . '/payment-methods/razorpay/index.php';
        break;
    default:
        $file = dirname(dirname(__FILE__)) . '/payment-methods/index.html';
}

include_once $file;
?>