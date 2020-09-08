<?php
if (isUserLogged()) {
  header("location: " . $sys['site_url']);
  exit();
}

$sys['description'] = $sys['site_meta_desc'];
$sys['keywords']    = $sys['site_meta_keywords'];
$sys['page']        = 'register';
$sys['title']       = 'Register';
$sys['content']     = loadPage('register');
