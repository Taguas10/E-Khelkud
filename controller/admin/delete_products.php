<?php
include '../../config/connection.php';
 if(isset($_GET['product_id']) &&  !empty($_GET['product_id'])){
    $product_id = $_GET['product_id'];
     $delete_sql = "DELETE FROM products where product_id=?";

    if($stmt = $conn->prepare($delete_sql)) {
        $stmt->bind_param('s',$product_id);
        $stmt->execute();
        $stmt->close();
    }
    }
header("Location: ../../view/admin/products.php");
exit();
