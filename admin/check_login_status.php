<?php
require_once '../system/init.php';
if(!isUserLogged()){
    header("location: login.php");
}
