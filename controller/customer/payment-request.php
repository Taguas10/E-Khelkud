<?php
session_start();
include_once '../../config/connection.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../../index.php");
}
if($_SERVER['REQUEST_METHOD']!=='POST') {
header("Location: ../.../customer/cart.php");
exit();    
}
$user_id =$_SESSION['user_id'];

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']):'';
$address = isset($_POST['address']) ? trim($_POST['address']):'';
$email = isset($_POST['email']) ? trim($_POST['email']) :'';

$errors =[];

if(empty($name)){
    $errors[]="Full name is required";
}elseif(strlen($name)>100){
    $errors[]="Name cannot exceed 100 characters";
}elseif(!preg_match("/^[a-zA-Z\s]+$/",$name)){
    $errors[]="Name must contain letters and spaces.";
}

if(empty($address)){
    $errors[]="Delivery address is required";
}elseif(strlen($address)<5 || strlen($address)>255){
    $errors[] ="Address between 5 and 255 characters";
    }
if(empty($phone)){
    $errors[]="Phone number is required";
}elseif(!preg_match("/^9[78]\d{8}$/",$phone)){
    $errors[] ="Please Provide 10 digit valid Nepali Mobile Number";
}
if(empty($email)){
    $errors[] ="Email Address is required";
}elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    $errors[]= "Please provide valid email address";
}
$grand_total = isset($_SESSION['grand_total']) ? floatval($_SESSION['grand_total']) : 0;
if($grand_total <=0){
    $errors[] ="Invalid payment amount.Your cart might be empty";
}
if(!empty($errors)){
    $_SESSION['validation_errors'] = $errors;
    header("Location:../../customer/views/checkout.php");
    exit();
}
$name = mysqli_real_escape_string($conn,$name);
$phone = mysqli_real_escape_string($conn,$phone);
$address = mysqli_real_escape_string($conn,$address);
$email = mysqli_real_escape_string($conn,$email);

$_SESSION['checkout_data']=[
    'name' =>$name,
    'phone' =>$phone,
    'address' =>$address,
    'email' => $email
];

$amount_paisa =$grand_total * 100;
$purchase_order_id ="Order-".$user_id."-".time();
$purchase_order_name ="E-Khelkud Items Purchase";
$post_fields = array(
    "return_url"          => "http://localhost/E-Khelkud/controller/customer/payment-response.php",
    "website_url"         => "http://localhost/E-Khelkud/",
    "amount"              => (int)$amount_paisa, 
    "purchase_order_id"   => $purchase_order_id,
    "purchase_order_name" => $purchase_order_name,
    "customer_info"       => array(
        "name"  => $name,
        "email" => $email,
        "phone" => $phone
    )
);


$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://dev.khalti.com/api/v2/epayment/initiate/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30, // 0 बाट 30 बनाइएको ता कि अनन्तकाल सम्म नकुरोस्
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($post_fields), 
    CURLOPT_HTTPHEADER => array(
        'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455', 
        'Content-Type: application/json',
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);


if ($err) {
    echo "cURL Error #:" . $err;
} else {
    
    $result = json_decode($response, true);
    if(isset($result['payment_url']) && isset($result['pidx'])) {
        $_SESSION['khalti_pidx']=$result['pidx'];
        header("Location: " . $result['payment_url']);
        
           exit();
    } else {
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    }
}
?>