<?php

if (!isUserLogged()) {
    header("location: " . $sys['site_url'] . "/login");
    exit();
}

$sys['description'] = $sys['site_meta_desc'];
$sys['keywords'] = $sys['site_meta_keywords'];


if (isset($request['section'])) {
    $section = trim($request['section']);
    if ($section == "orders") {
        $page = "myorders";
        $title = "My Orders";
        $content = "myorders";
    } else if ($section == "addresses") {
        $page = "myaddresses";
        $title = "My Addresses";
        $content = "myaddresses";
    } else if ($section == "wallet") {
        $page = "mywallet";
        $title = "My Wallet";
        $content = "mywallet";
    } else if ($section == "wishlist") {
        $page = "mywishlist";
        $title = "My Wishlist";
        $content = "mywishlist";
    } else if ($section == "reviews") {
        $page = "myreviews";
        $title = "My Reviews";
        $content = "myreviews";
    }
    
} else {
    $page = "myaccount";
    $title = "My Profile";
    $content = "myaccount";
}
$sys['page'] = $page;
$sys['title'] = $title;
$sys['content'] = loadPage($content);
