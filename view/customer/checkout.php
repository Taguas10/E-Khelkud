<?php
session_start();
include_once '../../config/connection.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
 $grand_total = isset($_SESSION['grand_total']) ? $_SESSION['grand_total']:0;
 ?>
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../assets/css/checkout.css">
</head>
<body>
<div class ="cart-container">
    <div class ="cart-items">
        
    <div class ="checkout-form">
        <h2>Delivery and Payment Details</h2>
        <form action ="../../controller/customer/payment-request.php" method="POST">
          <div class ="form-group">
            <label>Full Name</label>
            <input type ="text" name="name" placeholder ="e.g Ram" required>
            </div> 
            <div class ="form-group">
            <label>Phone Number</label>
            <input type ="text" name="phone" placeholder ="e.g 9810000000" required>
            </div> 

        <div class ="form-group">
            <label>Delivery Address</label>
            <input type="text" name="address" placeholder="e.g. Birtamode Bhadrapur road" required>  
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="e.g.saugat@gmail.com" required>
        </div>
        <div class="form-group">
            <label>Payment</label>
            <select name="payment-method" required>
                <option value="Khalti"> Khalti</option>
            </select>
        </div>
        <button type="submit" class="checkout-btn">Proceed to Pay</button>
        </form>
        <div class ="cart-summary">
            <h3>Order Summary</h3>
            <p>Total Payable Amount :</p>
            <h2>Rs<?php echo number_format($grand_total,2);?></h2>
        </div>
             
            
        </form>
    </div>
    </div>
</div>

</body>
</html>
