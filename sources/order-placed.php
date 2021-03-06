<?php

if (!isUserLogged()) {
    header("location:" . $sys['site_url'] . "/login");
    exit();
}
if (isset($_REQUEST['orderid'])) {
    $order_id = trim($_REQUEST['orderid']);
    $order = getOrder($order_id, array(), true);
    $msg = "";
    if (($order['payment_method'] == PM_CASH_ON_DELIVERY && isset($sys['ORDER_COD_DEFAULT_STATUS']) && $order['payment_status'] == $sys['ORDER_COD_DEFAULT_STATUS']) 
            || ($order['payment_method'] == PM_CASH_ON_DELIVERY && !isset($sys['ORDER_COD_DEFAULT_STATUS']) && $order['payment_status'] == OS_PAYMENT_PENDING)
            || $order['payment_status'] == OS_PAYMENT_CONFIRMED) {
        $msg = "Your Order has been placed!";
    } else {
        header("location:" . $sys['site_url'] . "/account/orders");
        exit();
    }
    $shops = array();
    $productshtml = '<table>';
    foreach ($order['products'] as $p) {
        $tmpproductshtml = '<tr>';
        if ($p['first_image'] <> "") {
            $tmpproductshtml .= '<td><img src="' . $p['first_image'] . '" style="max-width: 200px;"/></td>';
        } else {
            $tmpproductshtml .= '<td><img src="https://via.placeholder.com/400x400"/></td>';
        }

        $tmpproductshtml .= '<td>' . $p['product_name'] . '<br/><span>' . $p['customization_string'] . '</span></td>';
        $tmpproductshtml .= '<td>₹' . $p['total'] . '</td>';
        $tmpproductshtml .= '</tr>';
        $productshtml .= $tmpproductshtml;

        if (isset($shops[$p['shop_id']])) {
            $shops[$p['shop_id']]['productshtml'] .= $tmpproductshtml;
        } else {
            $shops[$p['shop_id']] = array("vendor_name" => $p['shop_owner_name'], "vendor_email" => $p['shop_owner_email'], "productshtml" => $tmpproductshtml);
        }
    }
    $productshtml .= '</table>';
    $configs = getConfig();
    $logotag = isset($configs["EMAIL_TEMPLATE_LOGO"]) && trim($configs["EMAIL_TEMPLATE_LOGO"]) <> "" ? '<img src="' . $configs["EMAIL_TEMPLATE_LOGO"] . '"/>' : "";

    //send mail to admin
    $template = getEmailTemplate('admin_order_email');
    if ($template != null) {
        $subject = str_replace('{website_name}', $sys['site_name'], $template['subject']);
        $searchfor = array('{Company_Logo}', '{current_date}', '{user_full_name}', '{order_invoice_number}', '{order_date}', '{order_products_table_format}', '{website_name}', '{website_url}');
        $replacements = array($logotag, date("Y-m-d"), "Admin", $order['invoice_number'], $order['added_timestamp'], $productshtml, $sys['site_name'], $sys['site_url']);
        $body = str_replace($searchfor, $replacements, $template['body']);

        $data['from_email'] = $sys['admin_email'];
        $data['from_name'] = $sys['site_name'];
        $data['to_email'] = $sys['admin_email'];
        $data['to_name'] = "Admin";
        $data['charSet'] = "";
        $data['is_html'] = true;
        $data['subject'] = $subject;
        $data['message_body'] = $body;
        if (!isMessageAlreadySent($data, "admin_order_email", "orderid:" . $order['id'])) {
            sendMessageTemplate($data, "admin_order_email", "orderid:" . $order['id']);
        }
    }
    //send mail to user
    $template = getEmailTemplate('customer_order_email');
    if ($template != null) {
        $u = getUser($order['user_id']);
        $subject = str_replace('{website_name}', $sys['site_name'], $template['subject']);
        $searchfor = array('{Company_Logo}', '{current_date}', '{user_full_name}', '{order_invoice_number}', '{order_products_table_format}', '{website_name}', '{website_url}');
        $replacements = array($logotag, date("Y-m-d"), $u['display_name'], $order['invoice_number'], $productshtml, $sys['site_name'], $sys['site_url']);
        $body = str_replace($searchfor, $replacements, $template['body']);

        $data['from_email'] = $sys['admin_email'];
        $data['from_name'] = $sys['site_name'];
        $data['to_email'] = $u['email'];
        $data['to_name'] = $u['display_name'];
        $data['charSet'] = "";
        $data['is_html'] = true;
        $data['subject'] = $subject;
        $data['message_body'] = $body;
        if (!isMessageAlreadySent($data, "customer_order_email", "orderid:" . $order['id'])) {
            sendMessageTemplate($data, "customer_order_email", "orderid:" . $order['id']);
        }
    }
    //send mail to store owners
    if (isset($configs['ORDER_EMAIL_ALERT_SELLER']) && trim($configs['ORDER_EMAIL_ALERT_SELLER']) == "Y") {
        foreach ($shops as $s) {
            $template = getEmailTemplate('vendor_order_email');
            if ($template != null) {
                $subject = str_replace('{website_name}', $sys['site_name'], $template['subject']);
                $searchfor = array('{Company_Logo}', '{current_date}', '{vendor_name}', '{order_items_table_format}', '{website_name}', '{website_url}');
                $replacements = array($logotag, date("Y-m-d"), $s['vendor_name'], $s['productshtml'], $sys['site_name'], $sys['site_url']);
                $body = str_replace($searchfor, $replacements, $template['body']);

                $data['from_email'] = $sys['admin_email'];
                $data['from_name'] = $sys['site_name'];
                $data['to_email'] = $s['vendor_email'];
                $data['to_name'] = $s['vendor_name'];
                $data['charSet'] = "";
                $data['is_html'] = true;
                $data['subject'] = $subject;
                $data['message_body'] = $body;
                if (!isMessageAlreadySent($data, "vendor_order_email", "orderid:" . $order['id'])) {
                    sendMessageTemplate($data, "vendor_order_email", "orderid:" . $order['id']);
                }
            }
        }
    }

    $sys['description'] = $sys['site_meta_desc'];
    $sys['keywords'] = $sys['site_meta_keywords'];
    $sys['page'] = 'order-placed';
    $sys['title'] = "Order Placed";
    $sys['content'] = '<div class="container">'
            . '<br><br><br><br>'
            . '<div class="alert alert-success">' . $msg . ' <a href="' . $sys['site_url'] . '/account/orders">Go To Orders</a></div>'
            . '</div>';
} else {
    echo "Provide Order ID";
    die();
}