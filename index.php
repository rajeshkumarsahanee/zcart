<?php

// +------------------------------------------------------------------------+
// | @author Rajesh Kumar Sahanee
// | @author_url 1: http://www.zatackcoder.com
// | @author_email: rajeshsahanee@gmail.com   
// +------------------------------------------------------------------------+

require 'system/init.php';

/* add visitor log */
$ipinfo = ipInfo($_SERVER['REMOTE_ADDR']);
@addLog(array(
            'user_id' => getUserLoggedId(),
            'role' => getUserLoggedRole(),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'country_code' => $ipinfo['country_code'],
            'country' => $ipinfo['country'],
            'region' => ipInfo($_SERVER['REMOTE_ADDR'], "region", TRUE),
            'state' => $ipinfo['state'],
            'city' => $ipinfo['city'],
            'address' => "",
            'source' => getReferrer(),
            'destination' => $_SERVER['REQUEST_URI'],
            'additional_info' => "",
            'action' => "visited",
            'action_date' => date("Y-m-d"),
            'action_time' => date("H:i:s")
        ));
/* add visitor log end */

if ((isset($sys['config']['underconstruction']) && $sys['config']['underconstruction']) || (isset($sys['config']['undermaintenance']) && $sys['config']['undermaintenance'])) {
    include 'sources/under-maintenance.php';
    exit();
}
die("coming soon");
$page = '';
if (!isset($_GET['tab1'])) {
    $page = 'welcome';
} elseif (isset($_GET['tab1'])) {
    $page = $_GET['tab1'];
}

switch ($page) {
    case 'home':
    case 'welcome':
        include('sources/welcome.php');
        break;
    case 'login':
        include('sources/login.php');
        break;
    case 'register':
        include('sources/register.php');
        break;
    case 'logout':
        include('sources/logout.php');
        break;
    case 'logout-lister':
        include('sources/logout-lister.php');
        break;
    case 'myaccount':
    case 'account':
        include('sources/account.php');
        break;
    case 'cart':
        include('sources/cart.php');
        break;
    case 'checkout':
        include('sources/checkout.php');
        break;
    case 'price-lister':
        include('sources/price-lister.php');
        break;
    case 'list-price':
        include('sources/list-price.php');
        break;
    case 'pay':
        include('sources/pay.php');
        break;
    case 'pg-response':
        include('sources/pg-response.php');
        break;
    case 'order-placed':
        include('sources/order-placed.php');
        break;
    case 'product':
        include('sources/product.php');
        break;
    case 'category':
        include('sources/category.php');
        break;
    case 'compare':
        include('sources/compare.php');
        break;
    case 'like':
        include('sources/like.php');
        break;
    case 'buynow':
        include('sources/buynow.php');
        break;
    case 'search':
        include('sources/search.php');
        break;
    case '404':
        include('sources/404.php');
        break;
    case 'contact-us':
        include('sources/contact.php');
        break;
    /* API / Developers (will be available on future updates)
      case 'oauth':
      include('sources/oauth.php');
      break;
      case 'graph':
      include('sources/graph.php');
      break;
      case 'graph-success':
      include('sources/graph_success.php');
      break;
      case 'app-setting':
      include('sources/app_setting.php');
      break;
      case 'developers':
      include('sources/developers.php');
      break;
      case 'create-app':
      include('sources/create_app.php');
      break;
      case 'app':
      include('sources/app_page.php');
      break;
      case 'apps':
      include('sources/apps.php');
      break;
     */
}
if (empty($sys['content'])) {
    include('sources/404.php');
}

echo loadpage('container');
//mysqli_close($sqlConnect);
?>
