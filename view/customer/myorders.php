<?php
session_start();
include '../../config/connection.php';

if(!isset($_SESSION['user_id'])){
    header('Location: ../../auth/login.php');
    exit();
}
$user_id = $_SESSION['user_id'];


if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['update-status'])){
    $order_id = $_POST['order_id'];
    $order_status = "Delivered";
    $update_sql = "UPDATE orders SET order_status=? WHERE order_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('si',$order_status,$order_id);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


$order_sql ="SELECT o.order_id, op.product_id, p.product_name, op.quantity, op.price, o.order_status
             FROM orders o
             INNER JOIN order_products op ON o.order_id = op.order_id
             INNER JOIN products p ON op.product_id = p.product_id
             WHERE o.user_id=?
             ORDER BY order_id DESC";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param('i',$user_id);
$stmt->execute();
$result = $stmt->get_result();

$current_order_id = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="../../assets/css/myorders.css">
</head>
<body>
    <div class ="navbar">
    <a href ="index.php" class ="Logo" >E-KHELKUD</a>
    <a href="../../index.php">Home</a>
    <div class="nav-right">
            <a href="cart.php">Cart</a>
            <a href="../../auth/logout.php">Logout</a>    
    </div>
</div>

<div class="orders-container">
    <h2>Your Order History</h2>
    
    <?php if($result && $result->num_rows > 0):?>
        
        <?php while($row = $result->fetch_assoc()):?>
            
            <?php
            if($current_order_id !== $row['order_id']):
                if($current_order_id !== null):?>
                    </div> <?php endif;?>
                
                <?php $current_order_id = $row['order_id'];?>
                
                <div class="order-card">
                    <div class="order-header">
                        <span>ORDER-ID: #<?php echo $row['order_id'];?></span>
                        <span>Status: <?php echo htmlspecialchars($row['order_status']);?></span>
                    </div>
                    
                    <?php if(strtolower($row['order_status']) !== 'delivered'):?>
                        <div class="action">
                            <form action="" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $row['order_id'];?>">
                                <button type="submit" name="update-status">DELIVERED</button>
                            </form>
                        </div>
                    <?php endif;?>
            <?php endif;?>

            <div class="order-details">
                <span>
                    Product : <strong><?php echo htmlspecialchars($row['product_name']);?></strong>
                    (x<?php echo htmlspecialchars($row['quantity']);?>)
                </span>
                <?php $total = $row['price'] * $row['quantity'];?>
                <span>Amount : Rs. <?php echo number_format($total);?></span>
            </div>
            
        <?php endwhile; ?>
        
        </div> <?php else:?>
        <p>No Orders Found</p>
    <?php endif;?>
</div>

<?php 

$conn->close();
?>
</body>
</html>