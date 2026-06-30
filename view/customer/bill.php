<?php
session_start();
include '../../config/connection.php';

if(!isset($_SESSION['user_id'])){
    header('../../auth/login.php');
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'];

$order_sql = "SELECT * FROM orders where user_id =? AND order_id=?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param('ii',$user_id,$order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if($order_result->num_rows == 0){
    echo "NO RECORDS FOUND ";
}
$order = $order_result->fetch_assoc();
$stmt->close();



$products_sql = "SELECT op.*,p.product_name
FROM order_products op
INNER JOIN products p ON op.product_id = p.product_id
WHERE op.order_id=?";

$stmt = $conn->prepare($products_sql);
$stmt->bind_param("i",$order_id);
$stmt->execute();
$products_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
  
    <link rel="stylesheet" href=" ../../assets/css/billl.css">
</head>
<body>
<div class="print">
   
    <button class ="print-btn" onclick ="window.print()">Print Invoice</button>
       
</div>

<div class ="bill-box">
    <table class="order-detail">
        <tr>
            <td>
                <h2>E-Khelkud</h2>
            </td>
            <td class ="order_id">Invoice ID :#<?php echo $order['order_id'];?>
</td>

        </tr>
    </table>
    <table class="customer-detail">
        <tr>
            <td> 
                Name:<?php echo htmlspecialchars($order['customer_name']);?><br>
                Phone:<?php echo htmlspecialchars($order['customer_phone']);?><br>
                Email :<?php echo htmlspecialchars($order['customer_email']);?><br>
                Address:<?php echo htmlspecialchars($order['delivery_address']);?><br>

            </td>
            <td class="payment-detail">
                Gateway:<?php echo htmlspecialchars($order['payment_method']);?><br>
                Ref:<?php echo htmlspecialchars($order['purchase_order_id']);?><br>
                Status:<?php echo htmlspecialchars($order['payment_status']);?><br>
            </td>
            </tr>
            </table>

        <table class="item-detail">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="price">Price</th>
                    <th class="qty">Quantity</th>
                    <th class ="total">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($products_result->num_rows >0):?>
                <?php while($item = $products_result->fetch_assoc()):?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']);?></td>
                        <td><?php echo number_format($item['price']);?></td>
                        <td><?php echo $item['quantity'];?></td>
                        <td><?php echo number_format($item['total_amount']);?></td>

                    </tr>
                <?php endwhile; ?>
                <?php else:?>
                    <tr>
                        <td>No Products Fouund</td>
                    </tr>
                    <?php endif;?>
            </tbody>
        </table>
                <div class="back">  <a href="../../index.php" class="shop-btn"> Back to Shopping</a>
        </div>
</div>
</body>
</html>

