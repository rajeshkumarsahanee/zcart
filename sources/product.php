<?php

if (isset($request['product']) && !empty($request['product'])) {
    $slug = filter_var(trim($request['product']), FILTER_SANITIZE_STRING);
    $shop_id = isset($sys['default_shop']) ? $sys['default_shop'] : 1;
    if (isset($request['shop']) && trim($request['shop']) <> "") {
        $shop_id = filter_var(trim($request['shop']), FILTER_SANITIZE_NUMBER_INT);
    }
    $product = getProduct($slug, $shop_id);
    if (empty($product) || !isset($product['prices'][$shop_id])) {
        $sys['content'] = '';
    } else {
        $sys['title'] = $product['name'];
        $sys['keywords'] = $product['meta_keywords'];
        $sys['description'] = $product['meta_description'];
        $sys['page'] = 'product';
        $sys['product'] = $product;
        $sys['shop_id'] = $shop_id;
        $sys['options'] = getOptions();
        $sys['conditions'] = $CONDITIONS;
        $sys['content'] = loadPage('product');
    }
} else {
    header("location: " . $sys['site_url']);
    exit();
}

