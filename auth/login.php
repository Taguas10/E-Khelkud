<?php
$error = '';
$success = '';
include '../config/connection.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];


    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password!";
    } else {
        $login_sql = "SELECT user_id,username,password,role FROM users  where username= ? OR email =? LIMIT 1";
      
        if($stmt = $conn->prepare($login_sql)){
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location:../view/admin/admindashboard.php");
            } elseif($user['role']=='customer') {
                header("Location:../shop.php");
            }
            exit;
        } else {
            $error = "Invalid username or password!";
        }
        $stmt->close();
    
}
      
else {
    $error = "Something went wrong!";
}
    }
}
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Title</title>

    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>
    <div class="authentcation">
        <h2>E-KHELKUD LOGIN PAGE</h2>
        <?php if (!empty($error))
            echo "<p class ='alert-error'>$error</p>"; ?>
        <?php if (!empty($success))
            echo "<p class='alert-success'>$success</p>"; ?>


        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username or Email">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter username or email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter password">
            </div>

            <button type="submit" class="btn-register">Login</button>

        </form>
        <p class="auth-footer">Don't have an account yet?<a href="register.php">Register here</a></p>
    </div>
</body>

</html>