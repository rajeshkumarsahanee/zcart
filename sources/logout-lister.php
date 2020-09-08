<?php
if (Sys_IsListerLogged()) {
    unset($_SESSION['is_lister_login']);
    unset($_SESSION['lister_id']);
    unset($_SESSION['lister_username']);
    //session_destroy();        
}
header("Location: " . $sys['config']['site_url'] . '/price-lister');