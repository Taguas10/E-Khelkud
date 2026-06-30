<?php
session_start();
include '../../config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location:../../auth/login.php');
    exit;
}
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['product_id'])){
    $user_id = $_SESSION['user_id'];
    
    $product_id= intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if($quantity<1){
        $quantity=1;
    }
    $check_sql ="SELECT cart_id ,quantity from cart where user_id =? AND product_id=?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param('ii',$user_id,$product_id);
    $stmt->execute();
    $result = $stmt->get_result();


    if($result->num_rows>0){
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity']+ $quantity;


        $update_sql = "UPDATE cart SET quantity=? WHERE user_id=?  AND product_id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('iii',$new_quantity,$user_id,$product_id);
        $stmt->execute();
        $stmt->close();
        }
        else{
        $insert_sql ="INSERT INTO cart(user_id,product_id,quantity)VALUES(?,?,?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param('iii',$user_id,$product_id,$quantity);
        $stmt->execute();
        $stmt->close();
        }
        $conn->close();

        header("Location:../../shop.php");
        exit();
    }
    else{
        header("Location:../../shop.php");
        exit();
    }
    ?>