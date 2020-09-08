<?php

if (!isUserLogged()) {
    header("location: " . $sys['site_url'] . "/login");
    exit();
}
$user_id = isUserLogged() ? getUserLoggedId() : session_id();
$cart = getCart($user_id);
if (count($cart['cart_details']['items']) <= 0) {
    header("location: " . $sys['site_url'] . "/cart");
}
$sys['description'] = $sys['site_meta_desc'];
$sys['keywords'] = $sys['site_meta_keywords'];
$sys['page'] = 'checkout';
$sys['title'] = "Checkout";
$sys['cart'] = $cart;
$sys['content'] = loadPage('checkout');
