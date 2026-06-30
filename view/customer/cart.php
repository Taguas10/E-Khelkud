<?php
session_start();
include '../../config/connection.php';

if(!isset($_SESSION['user_id'])){
    header('location:../../login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$cart_sql = "SELECT c.cart_id ,c.quantity ,p.product_name ,p.price,p.product_image
FROM cart c 
JOIN products p ON c.product_id = p.product_id
WHERE c.user_id =? ";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param('i',$user_id);
$stmt->execute();
$result = $stmt->get_result();

$grand_total="0";


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Title</title>
  
    <link rel="stylesheet" href="../../assets/css/cart.css">
</head>
<body>
<div class="navbar">
    <a href="index.php" class="Logo">E-KHELKUD</a>
    <div class="nav-right">
        <a href="../../index.php">Home</a>
        <a href="myorders.php">Orders</a>
        <a href="../../auth/logout.php">Logout</a>    
    </div>
</div>
<div class="cart-container" >
 <?php
if($result->num_rows >0):
     while($item = $result->fetch_assoc()):
    $item_total = $item['price']*$item['quantity'];
    $grand_total += $item_total;
    $_SESSION['grand_total'] = $grand_total;
    ?>
    <div class ="cart-card">
        <div class="cart-img">
            <img src="../../assets/products/<?php echo htmlspecialchars($item['product_image']);?>">

        </div>
        <div class="cart-details">
        <h4><?php echo htmlspecialchars($item['product_name']);?></h4>
        <p><?php echo htmlspecialchars($item['price'],2);?></p>
        </div>

        <div class = "cart-qty">
        <span>Qty:<?php echo $item['quantity'];?></b></span>
        </div>
        <div class="form-actions">
        <div>Rs<?php echo number_format($item_total,2);?></div>
        <form action = "../../controller/customer/remove_from_cart.php" method="POST">
        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id'];?>">
        <button type ="submit" >Remove </button>
        </form>
</div>
</div>
<?php endwhile;?>
</div>
<div class="cart-summary" >
Grand Total: Rs<?php echo number_format($grand_total,2);?>
<a href="checkout.php">Proceed to checkout</a>
</div>
<?php else:?>
    <div>
    <p>Your Cart is empty</p>
    </div>
    <?php endif; ?>
    </div>
    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
