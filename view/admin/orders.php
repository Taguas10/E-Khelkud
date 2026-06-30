<?php
session_start();
include '../../config/connection.php';

if (!isset($S_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update-status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status'];

    //update status segment
    $update_sql = "UPDATE orders SET order_status=? WHERE order_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('si', $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: orders.php");
    exit();
}
$orders_sql = "SELECT o.order_id,o.customer_name,o.customer_name,o.customer_phone
    ,o.delivery_address,o.total_amount,o.payment_status,o.order_status,
GROUP_CONCAT(CONCAT(p.product_name,'(x',op.quantity,')')SEPARATOR',') as all_products
FROM orders o 
INNER JOIN order_products op ON o.order_id=op.order_id
INNER JOIN products p on op.product_id  = p.product_id
GROUP BY order_id
ORDER BY o.order_id DESC";
$order_result = $conn->query($orders_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Title</title>


    <link rel="stylesheet" href="../../assets/css/orders.css">
</head>

<body>
    <div class="orders-section" > 
        <div class ="section-header">
        <h2> Manage Orders</h2>
    </div>
    <table class="orders-display-table">
        <thead>
            <tr class="headings">
                <th>Order_ID</th>
                <th>Customer Details</th>
                <th>Producs Ordered</th>
                <th>Total Price</th>
                <th>Order Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($order_result->num_rows > 0): ?>
                <?php while ($row = $order_result->fetch_assoc()): ?>
                    <tr class="details">
                    <td class="order_id"> <?php echo htmlspecialchars($row['order_id']); ?>
                    </td>
                
    
        
                        <td class=" cs-details">
                            <div class="cs-name"><?php echo htmlspecialchars($row['customer_name']); ?></div>
                            <div class="cs-phone"><?php echo htmlspecialchars($row['customer_phone']); ?></div>
                            <div class="cs-address"><?php echo htmlspecialchars($row['delivery_address']); ?></div>
                        </td>
                        <td class="products">
                            <?php echo htmlspecialchars($row['all_products']); ?>
                        </td>

                        <td class="total_amount">
                            <?php echo number_format($row['total_amount']) ?>
                        </td>
                        <td class="ORDER STATUS"><?php echo htmlspecialchars($row['order_status']) ?></td>
                        <td class="actions">
                            <?php if ($row['order_status'] === 'Delivered'): ?>
                                <span class="txt-completed">Delivered to Customer</span>
                            <?php else: ?>
                                
                                <form action="" method="POST" class="status-form">
                                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                    <select name="order_status" class="status-dropdown">
                                        <option value="Pending">Pending</option>
                                        <option value="Preparing">Preparing</option>
                                        <option value="Dispatched">Dispatched</option>
                                    </select>
                                    <button type="submit" name="update-status" class="btn-save">Save</button>

                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="no-data">No orders Found</td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>


</body>
</div>

</html>