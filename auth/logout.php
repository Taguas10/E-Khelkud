<?php
include '../config/connection.php';
session_start();

$_SESSION =  array();

session_destroy();
 
header('location: login.php');
exit();
?>