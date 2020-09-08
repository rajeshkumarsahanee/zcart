<?php

$sys['description'] = $sys['site_meta_desc'];
$sys['keywords'] = $sys['site_meta_keywords'];
$sys['page'] = 'welcome';
$sys['title'] = $sys['site_meta_title'];

$configs = getConfig();
$home_banners = isset($configs['home_banners']) ? json_decode($configs['home_banners'], true) : array();
$home_sections = isset($configs['home_sections']) ? json_decode($configs['home_sections'], true) : array();
$sys['banners'] = $home_banners;
$sys['sections'] = $home_sections;
$sys['content'] = loadPage('welcome');
