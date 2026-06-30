<?php
session_start();

include 'config/connection.php';
if(isset($_SESSION['user_id'])){

$user_id = $_SESSION['user_id'];

$count_sql= "SELECT SUM(quantity) FROM cart where user_id=?";
$stmt = $conn->prepare($count_sql);
$stmt->bind_param('i',$user_id);
$stmt->execute();

$stmt->bind_result($total_items);
$stmt->fetch();
$cart_count = $total_items ?$total_items:0;
$stmt->close();
}

$query ="SELECT product_id,product_name,category,price,stock,description,product_image FROM products";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="assets/css/shop.css">
</head>
<body>
<div class ="navbar">
    <a href ="index.php" class ="Logo" >E-KHELKUD</a>
    <a href="index.php">Home</a>
    <div class="nav-right">
        <?php if(isset($_SESSION['user_id'])):?>
            <a href="view/customer/cart.php">Cart(<?php echo $cart_count;?>)</a>
            <a href="view/customer/myorders.php">Orders</a>
            <a href="auth/logout.php">Logout</a>
            <a href="view/customer/update_user.php">Update</a>
            <?php else:?>
                <a href = "auth/login.php">Login</a>
                <?php endif;?>
    </div>
</div>

<main class="store-container">
    <h2 class="section-title">Explore Sports Gears</h2>
    <div class="product-grid">
        <?php
        if($result && $result->num_rows > 0):
            while($product = $result->fetch_assoc()):
            ?>
            <div class="product-card">
                <div class="product-visual"></div>
                <div class="product-info">
                    <img src="assets/products/<?php echo htmlspecialchars($product['product_image']); ?>" alt="Product Image">
                    
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p class="price">Rs. <?php echo number_format($product['price']); ?></p>
                    
                    <form action="controller/customer/add_to_cart.php" method="POST" class="qty-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        
                        <div class="qty-container">
                            <label>Qty:</label>
                            <div class="quantity-area">
                                <button type="button" class="qty-btn" onclick="let input = this.nextElementSibling; if(input.value > 1) input.value--;">-</button>
                                
                                <input type="number" name="quantity" value="1" min="1" readonly>
                                
                                <button type="button" class="qty-btn" onclick="let input = this.previousElementSibling; input.value++;">+</button>
                            </div>
                        </div>  
                        
                        <button type="submit" class="add-btn">Add to cart</button>
                    </form>  
                </div>
            </div>
            <?php
            endwhile;
        else:
            ?>
            <div class="empty-catalog">
                <p>NO Items Found</p>
            </div>
        <?php
        endif;
        $conn->close();
        ?>
    </div>
</main>
</body>
</html>