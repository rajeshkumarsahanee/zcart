<?php

/*print_r($_POST);*/
//payUbiz India
if(isset($_POST['mihpayid'])) {
    require_once dirname(dirname(__FILE__)) . '/payment-methods/payubizindia/handle-response.php';
}
//payUmoney India
if(isset($_POST['payuMoneyId'])) {
    require_once dirname(dirname(__FILE__)) . '/payment-methods/payumoneyindia/handle-response.php';
}
//ccavenue
if(isset($_POST["encResp"])) {
    require_once dirname(dirname(__FILE__)) . '/payment-methods/ccavenue/handle-response.php';
}
//header($sys['site_url'] . "/order-placed?orderid=" . $order_id);

