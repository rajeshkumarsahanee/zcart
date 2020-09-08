<?php
$pmethod = getPaymentMethod("payubizindia");
if ($pmethod == null) {
    die("Payment Gateway Settings Not Available!");
}
$fields = json_decode($pmethod['fields'], true);

/* PG Provided Code Start */
$MERCHANT_KEY = $fields['merchantkey'];
$SALT = $fields['salt'];
$PAYU_BASE_URL = $fields['mode'] == 'live' ? "https://secure.payu.in" : "https://test.payu.in";

$action = '';

$posted = array();
if (!empty($_POST)) {
    //print_r($_POST);
    foreach ($_POST as $key => $value) {
        $posted[$key] = $value;
    }
}

$formError = 0;

if (empty($posted['txnid'])) {
    // Generate random transaction id
    //$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    // Generate transaction id by adding payment with pending status
    $txnid = addOrderPayment(array('order_id' => $order['id'],
        'payment_method' => $pmethod['name'],
        'payment_method_key' => $pmethod['code'],
        'amount' => $order['payable_amount'],
        'pg_txn_id' => "",
        'pg_response' => "",
        'pg_status' => "pending",
        'comments' => "",
        'payment_datetime' => date("Y-m-d H:i:s")
    ));
    if (!$txnid) {
        die('Error! Please Try Again Later - <a href="' . $sys['site_url'] . '">Go To Home</a>');
    }
} else {
    $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if (empty($posted['hash']) && sizeof($posted) > 0) {
    if (empty($posted['key']) || empty($posted['txnid']) || empty($posted['amount']) || empty($posted['firstname']) || empty($posted['email']) || empty($posted['phone']) || empty($posted['productinfo'])) {
        $formError = 1;
    } else {
        //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';
        foreach ($hashVarsSeq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }
        $hash_string .= $SALT;
        $hash = strtolower(hash('sha512', $hash_string));
        $action = $PAYU_BASE_URL . '/_payment';
    }
} elseif (!empty($posted['hash'])) {
    $hash = $posted['hash'];
    $action = $PAYU_BASE_URL . '/_payment';
}
/* PG Provided Code End */

$surl = $furl = $curl = $sys['site_url'] . "/pay-response";
?>
<html>
    <head>
        <script>
            var hash = '<?php echo $hash ?>';
            function submitPayuForm() {
                if (hash == '') {
                    return;
                }
                var payuForm = document.forms.payuForm;
                payuForm.submit();
            }
        </script>
    </head>
    <body onload="submitPayuForm()">        
        <?php if ($formError) { ?>
            <span style="color:red">Please fill all mandatory fields.</span>            
        <?php } ?>
        <form action="<?php echo $action; ?>" method="post" name="payuForm">
            <!-- Required -->
            <input type="hidden" name="key" value="<?= $MERCHANT_KEY ?>"/>
            <input type="hidden" name="hash" value="<?= $hash ?>"/>
            <input type="hidden" name="txnid" value="<?= $txnid ?>"/>
            <input type="hidden" name="amount" value="<?= $order['payable_amount'] ?>"/>
            <input type="hidden" name="firstname" id="firstname" value="<?= $order['billing_name'] ?>"/>
            <input type="hidden" name="email" id="email" value="<?= $order['billing_email'] ?>"/>
            <input type="hidden" name="phone" value="<?= $order['billing_mobile'] ?>"/>
            <input type="hidden" name="productinfo" value="<?= $order['invoice_number'] ?>"/>
            <input type="hidden" name="surl" value="<?= $surl ?>" size="64"/>
            <input type="hidden" name="furl" value="<?= $furl ?>" size="64"/>
            <input type="hidden" name="curl" value="<?= $curl ?>"/>
            <!-- Optional -->
            <input type="hidden" name="lastname" id="lastname" value=""/>
            <input type="hidden" name="address1" value="<?= $order['billing_address'] ?>"/>
            <input type="hidden" name="address2" value="<?= $order['billing_locality'] . " " . $order['billing_landmark'] ?>"/>
            <input type="hidden" name="city" value="<?= $order['billing_city'] ?>"/>
            <input type="hidden" name="state" value="<?= $order['billing_state'] ?>"/>        
            <input type="hidden" name="country" value="<?= $order['billing_country'] ?>"/>      
            <input type="hidden" name="zipcode" value="<?= $order['billing_pincode'] ?>"/>        
            <input type="hidden" name="udf1" value="<?= $order['reference_number'] ?>"/>          
            <input type="hidden" name="udf2" value="<?= $order['id'] ?>"/>        
            <input type="hidden" name="udf3" value=""/>          
            <input type="hidden" name="udf4" value=""/>        
            <input type="hidden" name="udf5" value=""/>          
            <input type="hidden" name="pg" value=""/>
            <?php if (!$hash) { ?>
                <!--<input type="submit"/>-->
                <div class="container" style="position: absolute;top: 50%;left: 50%;-moz-transform: translateX(-50%) translateY(-50%);-webkit-transform: translateX(-50%) translateY(-50%);transform: translateX(-50%) translateY(-50%);">
                    <h1>Please Don't Press Back or Refresh Button</h1>
                </div>
                <script> document.forms.payuForm.submit(); </script>
            <?php } ?>
        </form>
    </body>
</html>
<?php exit(); ?>