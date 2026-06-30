<?php
session_start();

include '../../config/connection.php';

$pidx = isset($_GET['pidx']) ? $_GET['pidx'] : '';
$amount = isset($_GET['amount']) ? $_GET['amount'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$purchase_order_id = isset($_GET['purchase_order_id']) ? $_GET['purchase_order_id'] : '';

// 1. Initial Validation - Verify URL parameters exist
if (empty($pidx) || empty($status)) {
    echo "Critical error: No response found from Khalti";
    exit();
}


if ($status === 'Completed') {
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://dev.khalti.com/api/v2/epayment/lookup/', 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(array("pidx" => $pidx)),
        CURLOPT_HTTPHEADER => array(
            'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455', 
            'Content-Type: application/json',
        ),
    ));

    $response = curl_exec($curl);
    $result = json_decode($response, true);
    
    // Validate response from Khalti's servers
    if (isset($result['status']) && $result['status'] === 'Completed') {
        
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['checkout_data'])) {
            echo "Error: Missing session data";
            exit();
        }
        
        $user_id = $_SESSION['user_id'];
        $checkout_data = $_SESSION['checkout_data'];
        $customer_name = $checkout_data['name'];
        $customer_phone = $checkout_data['phone'];
        $customer_address = $checkout_data['address'];
        $customer_email = $checkout_data['email'];

        $grand_total = $amount / 100;
        $payment_method = "Khalti";
        $payment_status = "Paid";


        $conn->begin_transaction();

        try {
         
            $order_sql = "INSERT INTO orders (
                purchase_order_id, user_id, customer_name, customer_phone, customer_email, 
                delivery_address, total_amount, payment_method, payment_status, khalti_pidx
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($order_sql);
            $stmt->bind_param(
                "sissssdsss",
                $purchase_order_id, $user_id, $customer_name, $customer_phone, $customer_email,
                $customer_address, $grand_total, $payment_method, $payment_status, $pidx
            );
            $stmt->execute();
            
           
            $order_id = $conn->insert_id; 
            $stmt->close();

            $cart_sql = "SELECT c.product_id, c.quantity, p.price FROM cart c
            INNER JOIN  products p ON   c.product_id = p.product_id
            WHERE c.user_id = ?";
            $stmt = $conn->prepare($cart_sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $cart_items = $stmt->get_result();
            $stmt->close();

            if ($cart_items->num_rows > 0) {
                //statement to insert into order_products
                $item_sql = "INSERT INTO order_products (order_id, product_id, quantity, price, total_amount) VALUES (?, ?, ?, ?, ?)";
                $stmt_item = $conn->prepare($item_sql);
                

                //statement to decrease stock dynamically  
                $stock_sql ="UPDATE products SET stock = stock - ? WHERE product_id=?";
                $stmt_stock  = $conn->prepare($stock_sql);


                while ($item = $cart_items->fetch_assoc()) {
                    $total = $item['price'] * $item['quantity']; 
                    $stmt_item->bind_param("iiidd", $order_id, $item['product_id'], $item['quantity'], $item['price'], $total);
                    $stmt_item->execute();

                    //to decrease stock inside the loop
                    $stmt_stock ->bind_param("ii",$item['quantity'],$item['product_id']);
                    $stmt_stock->execute();                
                    }
                $stmt_item->close();
                $stmt_stock->close();
            }

            $delete_sql = "DELETE FROM cart WHERE user_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $user_id); 
            $stmt->execute();
            $stmt->close();

            $conn->commit();

            unset($_SESSION['checkout_data'], $_SESSION['grand_total']);
            
            $_SESSION['success_message'] = "Order processed successfully!";
           header("Location: ../../view/customer/bill.php?order_id=" . intval($order_id));
            exit();
        } catch (Exception $e) {
            
            $conn->rollback();
            die("Order processing failed: " . $e->getMessage());
        }
    } else {
        echo "Khalti remote transaction confirmation failed.";
    }
} else {
    echo "Payment gateway response status returned: " . htmlspecialchars($status);
}
?>