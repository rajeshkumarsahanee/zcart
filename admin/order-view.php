<?php require_once '../system/init.php'; ?>
<?php require_once 'check_login_status.php'; ?>
<?php
//Not authorized to access
if (!isUserHavePermission(ORDERS_SECTION, getUserLoggedId()) || !isset($_REQUEST['id'])) {
    header("location: orders.php");
}

$msg = "";

$ps = 1;
$p = 1;
$order = getOrder(trim($_REQUEST['id']), array(), true);
if ($order == null) {
    header("location: orders.php");
}
if(isset($_POST['update-order-payment']) && isset($_REQUEST['id']) && isset($_POST['order_id']) && trim($_REQUEST['id']) == trim($_REQUEST['order_id'])) {
    //update order and payment status
    $payment['order_id'] = filter_var(trim($_POST['order_id']), FILTER_SANITIZE_STRING);
    $payment['payment_method'] = filter_var(trim($_POST['payment_method']), FILTER_SANITIZE_STRING);
    $payment['payment_method_key'] = "";
    $payment['amount'] = filter_var(trim($_POST['amount']), FILTER_SANITIZE_STRING);
    $payment['pg_txn_id'] = filter_var(trim($_POST['pg_txn_id']), FILTER_SANITIZE_STRING);
    $payment['pg_response'] = "";
    $payment['pg_status'] = "";
    $payment['comments'] = filter_var(trim($_POST['comments']), FILTER_SANITIZE_STRING);
    $payment['payment_datetime'] = date("Y-m-d H:i:s");
    
    $status['order_id'] = filter_var(trim($_POST['order_id']), FILTER_SANITIZE_STRING);
    $status['order_product_id'] = "0";
    $status['status'] = "";
    $status['added_datetime'] = date("Y-m-d H:i:s");
    $status['comments'] = filter_var(trim($_POST['comments']), FILTER_SANITIZE_STRING);
    $status['tracking_number'] = "";
    $status['append_comment'] = "N";
    $status['payment_status'] = "N";
    
    if(addOrderPayment($payment)) {
        $msg = '<div class="">Order Payment Information Updated</div>';
        
        //send mail
        $configs = getConfig();
        $template = getEmailTemplate('primary_order_payment_status_change_buyer');
        $logotag = isset($configs["EMAIL_TEMPLATE_LOGO"]) && trim($configs["EMAIL_TEMPLATE_LOGO"]) <> "" ? '<img src="' . $configs["EMAIL_TEMPLATE_LOGO"] . '"/>' : "";
        $subject = str_replace('{website_name}', $sys['site_name'], $template['subject']);
        $searchfor = array('{Company_Logo}', '{current_date}', '{user_full_name}', '{new_order_status}', '{invoice_number}', '{website_name}');
        $replacements = array($logotag, date("Y-m-d"), $order['billing_name'], $ORDER_STATUSES[2], $order['invoice_number'], $sys['site_name']);
        $body = str_replace($searchfor, $replacements, $template['body']);

        $data = array(); //clearing data array
        $data['from_email'] = secure($sys['admin_email']);
        $data['from_name'] = $sys['site_name'];
        $data['to_email'] = $order['email'];
        $data['to_name'] = $order['billing_name'];
        $data['charSet'] = "";
        $data['is_html'] = true;
        $data['subject'] = $subject;
        $data['message_body'] = $body;
        
        $status['customer_notified'] = sendMessage($data) ? "Y" : "N";
        
        if(addOrderStatus($status) && update(T_ORDERS, array("order_status" => "2", "payment_status" => "2"), array("id" => $payment['order_id']))) {
            $msg .= '<div class="">Order Status Updated</div>';
            $order['order_status'] = "2";
            $order['payment_status'] = "2";
        }
    }
}

$statuseshistory = getOrderStatuses(array(), array("order_id" => $order['id']));
$paymentshistory = getOrderPayments(array(), array("order_id" => $order['id']));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">      
        <title>View Order - Admin</title>        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <?php include 'css.php'; ?>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
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
                        Invoice
                        <small>#<?= $order['id'] ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li><a href="#">Orders</a></li>
                        <li class="active">Invoice</li>
                    </ol>
                </section>               

                <!-- Main content -->
                <section class="invoice">
                    <?= $msg ?>
                    <!-- title row -->
                    <div class="row">
                        <div class="col-xs-12">
                            <h2 class="page-header">
                                <?= $sys['site_name'] ?>
                                <small class="pull-right">Date: <?= $order['added_timestamp'] ?></small>
                            </h2>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info" style="margin-bottom: 15px;">
                        <div class="col-sm-4 invoice-col">
                            Billing Address
                            <address>
                                <strong><?= $order['billing_name'] ?></strong><br>
                                <?= $order['billing_address'] ?><br>
                                <?= $order['billing_city'] ?>, <?= getState($order['billing_state'])['name'] . " - " . $order['billing_pincode'] ?>, <?= $order['billing_country'] ?><br>
                                Phone: <?= $order['billing_phone']; ?><br>
                                Email: <?= $order['billing_email']; ?>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            Shipping Address
                            <address>
                                <strong><?= $order['shipping_name'] ?></strong><br>
                                <?= $order['shipping_address'] ?><br>
                                <?= $order['shipping_city'] ?>, <?= getState($order['shipping_state'])['name'] . " - " . $order['shipping_pincode'] ?>, <?= $order['shipping_country'] ?><br>
                                Phone: <?= $order['shipping_phone']; ?><br>
                                Email: <?= $order['shipping_email']; ?>
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <b>Order ID:</b> #<?= $order['invoice_number'] ?><br>                          
                            <b>Order Status:</b> <?= $ORDER_STATUSES[$order['order_status']] ?><br/>
                            <b>Amount:</b> <?= $order['payable_amount'] ?><br/>
                            <b>Payment Status:</b> <?= $ORDER_STATUSES[$order['payment_status']] ?><br/>
                            <b>Payment Method:</b> <?= $order['payment_method'] ?><br/>
                            <b>Site Commission:</b> <?= $order['site_commission'] ?><br/>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Shipping</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($order['products'] as $item) {
                                        $s = getShop($item['shop_id'], array("id", "name"));
                                        ?>
                                        <tr>
                                            <td>
                                                <?= $item['product_name'] ?><br/>
                                                <?= $item['customization_string'] ?><br/>
                                                <b>SKU:</b> <?= $item['product_sku'] ?><br/>
                                                <b>Vendor:</b> <?= $s['name'] ?>
                                            </td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td><?= $item['amount'] ?></td>
                                            <td><?= $item['shipping_charges'] ?></td>
                                            <td><?= $item['total'] ?></td>
                                        </tr>                            
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-xs-6">
                            <p class="lead">Payment Methods:</p>
                            <img src="<?= $sys['site_url'] ?>/admin/dist/img/credit/visa.png" alt="Visa">
                            <img src="<?= $sys['site_url'] ?>/admin/dist/img/credit/mastercard.png" alt="Mastercard">
                            <img src="<?= $sys['site_url'] ?>/admin/dist/img/credit/american-express.png" alt="American Express">

                            <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                Thank you for shopping!
                            </p>
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-6">
                            <p class="lead">Amount Due 2/22/2014</p>

                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th>Cart Total</th>
                                        <td>Rs. <?= $order['cart_total'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Shipping:</th>
                                        <td>+Rs. <?= $order['shipping_charges'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>VAT</th>
                                        <td>+Rs. <?= $order['vat'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Discount Coupon[<?= $order['coupon_code'] ?>]</th>
                                        <td>-Rs. <?= $order['coupon_amount'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Reward Points</th>
                                        <td>-Rs. <?= $order['credits_used'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Total:</th>
                                        <td>Rs. <?= $order['payable_amount'] ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- this row will not appear when printing -->
                    <div class="row no-print">
                        <div class="col-xs-12">
                            <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
                            <button type="button" class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Submit Payment
                            </button>
                            <button type="button" class="btn btn-primary pull-right" style="margin-right: 5px;">
                                <i class="fa fa-download"></i> Generate PDF
                            </button>
                        </div>
                    </div>
                </section>
                <?php if($order['payment_status'] == '1') { ?>
                    <section style="margin: 15px 25px;">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-default">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Order Payments</h3>
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body">
                                            <div class="row">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>"/>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Payment Method*</label>
                                                        <input type="text" name="payment_method" class="form-control"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Txn ID*</label>
                                                        <input type="text" name="pg_txn_id" class="form-control"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Amount*</label>
                                                        <input type="text" name="amount" class="form-control"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Comments</label>
                                                        <textarea type="text" name="comments" class="form-control"></textarea>
                                                        Please enter some comments/details about this transaction.
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="submit" name="update-order-payment" value="Update" class="btn btn-success"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.box-body -->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                <?php } else { ?>
                <section style="margin: 15px 25px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Order Status History</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Date Added</th>
                                                <th>Customer Notified</th>
                                                <th>Payment Status</th>
                                                <th>Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($statuseshistory as $sh) { ?>
                                            <tr>
                                                <td><?= $sh['added_datetime'] ?></td>
                                                <td><?= $sh['customer_notified'] ?></td>
                                                <td><?= $sh['payment_status'] ?></td>
                                                <td><?= $sh['comments'] ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Order Payment History</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Date Added</th>
                                                <th>Txn ID</th>
                                                <th>Payment Method</th>
                                                <th>Amount</th>
                                                <th>Comments</th>
                                                <th>Gateway Response</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($paymentshistory as $ph) { ?>
                                            <tr>
                                                <td><?= $ph['payment_datetime'] ?></td>
                                                <td><?= $ph['pg_txn_id'] ?></td>
                                                <td><?= $ph['payment_method'] ?></td>
                                                <td><?= $ph['amount'] ?></td>
                                                <td><?= $ph['comments'] ?></td>
                                                <td><?= $ph['pg_response'] ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                    </div>
                </section>
                <?php } ?>
                <!-- /.content -->
                <div class="clearfix"></div>
            </div>
            <!-- Main Footer -->
            <?php include 'footer.php'; ?>          
        </div>
        <!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->
        <?php include 'script.php'; ?>
    </body>
</html>