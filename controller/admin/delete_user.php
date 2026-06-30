<?php
include '../../config/connection.php';
session_start();

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['delete_user_btn'])){
    $user_id = $_POST['user_id'];

    $delete_user_sql = "DELETE FROM users WHERE user_id = ?";
    $stmt_user = $conn->prepare($delete_user_sql);
    if ($stmt_user) {
        $stmt_user->bind_param('i', $user_id);
        $stmt_user->execute();
        $stmt_user->close();
    } else {
        die("MySQL Error: " . $conn->error);
    }
    
    header("Location:../../view/admin/users.php");
    exit();
}

