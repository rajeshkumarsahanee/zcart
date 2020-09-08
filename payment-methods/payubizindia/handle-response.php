<?php

if (empty($_POST)) {
    header("location: " . $sys['site_url']);
    exit();
}

$pmethod = getPaymentMethod("payubizindia");
if ($pmethod == null) {
    die("Payment Gateway Settings Not Available!");
}
$fields = json_decode($pmethod['fields'], true);

$salt = $fields['salt']; //Please change the value with the live salt for production environment

$mihpayid = $_POST['mihpayid'];
$status = $_POST["status"];
$firstname = $_POST["firstname"];
$amount = $_POST["amount"]; //Please use the amount value from database
$txnid = $_POST["txnid"];
$posted_hash = $_POST["hash"];
$key = $_POST["key"];
$productinfo = $_POST["productinfo"];
$email = $_POST["email"];
$reference_number = $udf1 = $_POST['udf1'];
$order_id = $udf2 = $_POST['udf2'];

//Validating the reverse hash
if (isset($_POST["additionalCharges"])) {
    $additionalCharges = $_POST["additionalCharges"];
    $retHashSeq = $additionalCharges . '|' . $salt . '|' . $status . '|||||||||' . $udf2 . '|' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
} else {
    $retHashSeq = $salt . '|' . $status . '|||||||||' . $udf2 . '|' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
}

$hash = hash("sha512", $retHashSeq);

$payment = getOrderPayment($txnid);
$payment['pg_txn_id'] = $mihpayid;
$payment['pg_response'] = json_encode($_POST);
$payment['pg_status'] = $status;
$payment['payment_datetime'] = date("Y-m-d H:i:s");

if ($hash != $posted_hash) {
    $payment['comments'] = "Transaction has been tampered";
    updateOrderPayment($payment);
    header("location: " . $sys['site_url'] . "/payment-failed?orderid=" . $order_id);
    exit();
} else {
    if ($status == "success") {
        //$order = getOrder($order_id);
        $order_status = isset($sys['ORDER_PAID_DEFAULT_STATUS']) ? $sys['ORDER_PAID_DEFAULT_STATUS'] : OS_PAYMENT_CONFIRMED;
        $payment_status = isset($sys['ORDER_PAID_DEFAULT_STATUS']) ? $sys['ORDER_PAID_DEFAULT_STATUS'] : OS_PAYMENT_PENDING;
        $updated = update(T_ORDERS, array('order_status' => $order_status, 'payment_status' => $payment_status), array('id' => $order_id));
        if (updateOrderPayment($payment) && $updated) {
            $products = getOrderProducts(array(), array("order_id" => $order_id));
            foreach ($products as $p) {
                $statushistory = array(
                    'order_id' => $order_id,
                    'order_product_id' => $p['product_id'],
                    'status' => $sys['ORDER_PAID_DEFAULT_STATUS'],
                    'added_datetime' => date("Y-m-d H:i:s"),
                    'comments' => "",
                    'tracking_number' => "",
                    'append_comment' => "Y",
                    'payment_status' => "Y",
                    'customer_notified' => "N"/* can be updated in order-placed */);
                addOrderStatus($statushistory);
            }
        }

        header("location: " . $sys['site_url'] . "/order-placed?orderid=" . $order_id);
        exit();
    }

    header("location: " . $sys['site_url'] . "/payment-failed?orderid=" . $order_id);
    exit();
}
?>	