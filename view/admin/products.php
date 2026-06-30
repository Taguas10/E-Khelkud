<?php
include '../../config/connection.php';
session_start();
$fetch_sql = "SELECT * FROM products ORDER BY product_id DESC";
$result=$conn->query($fetch_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Title</title>
   <link rel="stylesheet" href="../../assets/css/product.css">
   <link rel="stylesheet" href="../../assets/css/sidebar.css">
   <link rel ="stylesheet" href="../../assets/css/admin_dashboard.css">
</head>
<body>
<?php include '../../includes/sidebar.php'; ?>
<div class ="main-content">
  <h2>ADMIN DASHBOARD - MANAGE PRODUCTS</h2>
</div>
<div class="content-body">
  <table class="products-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Product Name</th>
        <th>Category</th>
        <th>Price</th> 
        <th>Stock</th>
        <th>Actions</th>
      </tr>
    </thead>
  <tbody>
    <?php if($result && $result->num_rows>0):?>
    <?php while($row=$result->fetch_assoc()): ?>
    <tr>
    

    
    <td><?php echo htmlspecialchars($row['product_id']);?></td>
    <td><img src="../../assets/products/<?php echo htmlspecialchars($row['product_image']);?>" alt="product" ></td>
    <td><?php echo htmlspecialchars($row['product_name']);?></td>
    <td><?php echo htmlspecialchars($row['category']);?></td>
    <td><?php echo number_format($row['price']);?></td>
    <td><?php echo $row['stock'];?></td>
    <td>
   <a href="update_products.php?product_id=<?php echo $row['product_id']; ?>" class="btn-edit">Update</a>
    <a href="../../controller/admin/delete_products.php?product_id=<?php echo $row['product_id'];?>"class="btn-delete" onclick="return confirm('Are you sure?');">Delete</a>    
  </td> 
       
    </tr>
    <?php endwhile;?>
    <?php else : ?>
      <tr>
        <td colspan="7" style="text-align: center;">No products found</td>
      </tr>
      <?php endif ;?>
  </tbody>
  </table>
</div>
  
</body>
</html>

