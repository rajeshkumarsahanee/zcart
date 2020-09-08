<?php

$user_id = isUserLogged() ? getUserLoggedId() : session_id();
$cart = getCart($user_id);

$sys['description'] = $sys['site_meta_desc'];
$sys['keywords'] = $sys['site_meta_keywords'];
$sys['page'] = 'cart';
$sys['title'] = "Cart";
$sys['cart'] = $cart;
$sys['content'] = loadPage('cart');
