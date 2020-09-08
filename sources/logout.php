<?php
if (isUserLogged()) {
    unset($_SESSION['username']);
    unset($_SESSION['user_id']);
    unset($_SESSION['role']);
    unset($_SESSION['display_name']);
    unset($_SESSION['registered']);
    setcookie("user_id", "", time() - 3600); //setting cookie expiration to one hour ago
    session_destroy();        
}
header("location: " . $sys['site_url'] . '/login');