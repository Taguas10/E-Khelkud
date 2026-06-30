    <?php
    include '../../config/connection.php';
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = $_POST['description'];
        $old_image = $_POST['old_image']; //comes from hidden field
        // old filename passes if no new file is updated
        $image_name = $old_image;

        //if new file is uploaded
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $target_dir = "../../assets/products/";
            $image_name = basename($_FILES["product_image"]["name"]);
            $target_file_path = $target_dir . $image_name;

            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file_path)) {
                if (!empty($old_image) && file_exists($target_dir . $old_image)) {
                    unlink($target_dir . $old_image);
                }
            }
        }
        $update_sql = "UPDATE products SET product_name=?, category=?, price=?, stock=?, description=? , product_image=? WHERE product_id=?";
        if ($stmt = $conn->prepare($update_sql)) {
            $stmt->bind_param('ssdissi', $product_name, $category, $price, $stock, $description, $image_name, $product_id);
            $stmt->execute();
            $stmt->close();
        }
       
    }
    $product=null;

    if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
        $product_id = $_GET['product_id'];
        $fetch_sql = "SELECT * from products where product_id=?";

        if ($stmt = $conn->prepare($fetch_sql)) {
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();
        }
   
    }
         if (!$product) {
        header("Location: products.php");
        exit();
    }    
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet"  href="../../assets/css/update_products.css">
    </head>

    <body>

        <h2>Edit Product Details</h2>
        <form action="update_products.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
            <input type="hidden" name="old_image" value="<?php echo $product['product_image']; ?>">
            

            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>">
            </div>
            <div class="form-group">
                <label>Category:</label>
                <select name="category">
                    <option value="Football" <?php if ($product['category'] == 'Football') echo 'selected'; ?>>Football</option>
                    <option value="Cricket" <?php if ($product['category'] == 'Cricket') echo 'selected'; ?>>Cricket</option>
                    <option value="Footwear" <?php if ($product['category'] == 'Footwear') echo 'selected'; ?>>Footwear</option>
                    <option value="Clothing" <?php if ($product['category'] == 'Clothing') echo 'selected'; ?>>Clothing</option>
                </select>
            </div>

            <div class="form-group">
                <label>Price:</label>
                <input type="number" name="price" value="<?php echo $product['price']; ?>">
            </div>
            <div>
                <div class="form-group">
                    <label>Stock:</label>
                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>">
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <input type="text" name="description" value="<?php echo $product['description']; ?>">
                </div>

                <div class="form-group">
                    <label> Current Image:</label>
                    <img src="../../assets/products/<?php echo $product['product_image']; ?>" width="100"><br>
                    <label>Upload New mage(Optional):</label>
                    <input type="file" name="product_image">
                </div>

                <button type="submit" name="update_product">Update Product</button>
        </form>

    </body>

    </html>
    