<?php

if (isset($request['productcategory']) && !empty($request['productcategory'])) {
    $slug = filter_var(trim($request['productcategory']), FILTER_SANITIZE_STRING);
    
    $category = getCategory($slug);
    if (empty($category)) {
        $sys['content'] = '';
    } else {
        $sys['title'] = $category['name'];
        $sys['keywords'] = $category['meta_keywords'];
        $sys['description'] = $category['meta_description'];
        $sys['page'] = 'product-category';
        $sys['category'] = $category;
        $sys['content'] = loadPage('product-category');
    }
} else {
    header("location: " . $sys['site_url']);
    exit();
}

