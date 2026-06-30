<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="../../assets/css/add_product.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href ="../../assets/css/admin_dashboard.css">
</head>

<body>
    <?php include '../../includes/sidebar.php'; ?>
    <div class="main-content">
        <div class="admin-header">
            <span class="page-title">ADMIN DASHBOARD - ADD PRODUCTS</span>
        </div>

        <div class="card">
            <h2>Add New Product</h2>
            <p>Enter the details below.</p>
            <form action="../../controller/admin/add_products.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" required></input>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="Football">Football</option>
                        <option value="Cricket">Cricket</option>
                        <option value="Footwear">Footwear</option>
                        <option value="Clothing">Clothing</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price(Rs)</label>
                        <input type="number" id="price" name="price" placeholder="Enter price">
                    </div>
                    <div class="form-group">
                        <label for=stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" placeholder="Enter Number">
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Write Description"> </textarea>
                </div>
                <div class ="form-group">
                    <label for="product_image">Product Photo</label>
                    <input type="file" id ="product_image" name ="product_image">
                </div>
                <button type="submit" class ="submit-btn">Submit</button>
            </form>
        </div>
    </div>
 
</body>

</html>