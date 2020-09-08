<?php include_once '../system/init.php' ?>
<?php
session_destroy();
setcookie("user_id", "", time() - 3600); //setting cookie expiration to one hour ago
header("location: login.php");
?>