<?php
if (isset($_REQUEST['pid'])) {
    $pid = trim($_REQUEST['pid']);
    $likes = isset($_SESSION['likes']) ? $_SESSION['likes'] : "";      
    if ($likes == null || trim($likes) == '') {
        $likesarr = array();
    } else {
        $likesarr = explode(",", $likes);
    }
    if (!in_array($pid, $likesarr)) {
        Sys_incrementProductLikes($pid);
        $likesarr[] = $pid;
        $_SESSION['likes'] = implode(",", $likesarr);
    }
}
$loc = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : $sys['config']['site_url'];
header("location: " . $loc);