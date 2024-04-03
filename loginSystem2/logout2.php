<?php
session_start();

include 'partials/_dbconnect2.php';

// Destroyed session
session_destroy();

// Redirected to login page after destroying session
header("location: login2.php");
exit;
?>
