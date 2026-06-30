<?php
include '../../config/connection.php';
session_start();
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['cart_id'])){
$cart_id = $_POST['cart_id'];
 $remove_sql="DELETE FROM cart WHERE cart_id=?";
 $stmt = $conn->prepare($remove_sql);
$stmt->bind_param('i',$cart_id);
$stmt->execute();
$stmt->close();
header('Location:../../view/customer/cart.php');
exit();
}