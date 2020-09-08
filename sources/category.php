<?php
//if (Wo_IsLogged() === true) {
//  header("Location: " . $sys['config']['site_url']);
//  exit();
//}
if(isset($_GET['slug']) && !empty($_GET['slug'])) {    
    $category = Sys_getCategoryBySlug(trim($_GET['slug']));      
} else {
    header("Location: " . $sys['config']['site_url']);
    exit();
}
$sys['description'] = $sys['config']['siteDesc'];
$sys['keywords']    = $sys['config']['siteKeywords'];
$sys['page']        = 'category';
$sys['title']       = $category['name'];
$sys['content']     = Sys_LoadPage('category');
