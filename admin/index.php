<?php
require '../system/init.php';
if(isUserLogged()) {    
    header("location: dashboard.php");
} else { 
    header("location: login.php");
}