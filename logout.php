<?php
session_name('Zwe_Het_Zaw');
session_start();
session_destroy();  // Destroy the session
header("Location: login.php");  // Redirect to login page
exit;
?>
