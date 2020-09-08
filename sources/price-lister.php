<?php
if (Sys_isListerLogged()) {
  header("Location: " . $sys['config']['site_url']);
  exit();
}

$sys['description'] = $sys['config']['siteDescription'];
$sys['keywords']    = $sys['config']['siteKeywords'];
$sys['page']        = 'price-lister';
$sys['title']       = 'Price Lister';
$sys['content']     = Sys_LoadPage('price-lister');
