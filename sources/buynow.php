<?php

if (isset($_REQUEST['pid']) && isset($_REQUEST['sid'])) {
    $pid = trim($_REQUEST['pid']);
    $sid = trim($_REQUEST['sid']);    
    $affilate = Sys_getAffiliates($pid)[$sid];
    $ru = "";
    if ($affilate['id'] == '1') { //for amazon
        $ru = $affilate['url'] . "/?tag=" . $affilate['affiliate_id'];
    } else if ($affilate['id'] == '2') { //for flipkart        
        $ru = strpos($affilate['url'], "?") == false ? $affilate['url'] . "?affid=" . $affilate['tracking_id'] : $affilate['url'] . "&affid=" . $affilate['tracking_id'];
    } else if ($affilate['id'] == '3') { //for snapdeal
        $ru = $affilate['url'] . "?utm_source=aff_prog&utm_campaign=afts&offer_id=17&aff_id=" . $affilate['affiliate_id'];
    }
    if ($ru <> '' && Sys_incrementProductClicks($pid, $sid)) {                
        header("location: " . $ru);
        exit();
    }    
}
if (isset($_REQUEST['pid']) && isset($_REQUEST['lid'])) {
    $pid = trim($_REQUEST['pid']);
    $lid = trim($_REQUEST['lid']);    
    $listerprice = Sys_getListerPrice($lid, $pid);
    $ru = $listerprice['url'];
    
//    if ($ru <> '' && Sys_incrementProductClicks($pid, $lid)) {        
//        header("location: " . $ru);
//        exit();
//    }
    header("location: " . $ru);
}
header("location: " . $sys['config']['site_url']);

