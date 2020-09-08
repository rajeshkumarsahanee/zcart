<?php

$sys['content'] = '';
if (isset($request['pagename']) && !empty($request['pagename'])) {
    $slug = filter_var(trim($request['pagename']), FILTER_SANITIZE_STRING);
    $sys['post'] = $post = getPost($slug);
    if (!empty($post)) {
        $sys['title'] = $post['post_title'];
        if ($post['post_type'] == "page") {
            $sys['page'] = 'page';
            $sys['content'] = loadPage('page');
        } else if ($post['post_type'] == "post") {
            $sys['page'] = 'post';
            $sys['content'] = loadPage('post');
        }
    }
}

$sys['description'] = $sys['site_meta_desc'];
$sys['keywords'] = $sys['site_meta_keywords'];
