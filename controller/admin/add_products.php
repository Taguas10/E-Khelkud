<?php
include_once '../../config/connection.php';

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $product_name = $_POST['product_name'];
    $category =$_POST['category'];
    $price =(float)$_POST['price'];
    $stock =(int)$_POST['stock'];
    $description = $_POST['description'];

    $target_dir ="../../assets/products/";
    $image_name =basename($_FILES["product_image"]["name"]);
    $target_file_path = $target_dir.$image_name;

if(move_uploaded_file($_FILES["product_image"]["tmp_name"],$target_file_path)){
    $insert_sql ="INSERT INTO products(product_name,category,price,stock,description,product_image)
    VALUES(?,?,?,?,?,?)";
    $stmt = $conn->prepare($insert_sql);
   
    $stmt->bind_param("ssdiss",$product_name,$category,$price,$stock,$description,$image_name);
    $stmt->execute();
    $stmt->close();
    echo"<script>
    alert('product added successfully!');
    window.location.href='../../view/admin/admindashboard.php';
    </script>";
    exit();
}
else{
echo"<script>
alert('Error:Could not add product.');
window.location.href='../../view/admin/add_products.php';
</script>";
exit();
}
}
?>
