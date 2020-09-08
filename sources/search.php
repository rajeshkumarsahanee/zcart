<?php
$sys['s'] = isset($request['s']) ? filter_var(trim(urldecode($request['s'])), FILTER_SANITIZE_STRING) : "";
$sys['description'] = $sys['site_meta_desc'];
$sys['keywords'] = $sys['site_meta_keywords'];
$sys['page']        = 'search';
$sys['title']       = 'Search';
$sys['content']     = loadPage('search');
