<?php
include '../config/connection.php';
$error = "";
$success = "";

define('ADMIN_SECRET_KEY', 'admin123');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = ($_POST['password']);
    $admin_key = trim($_POST['admin_key']);

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required !";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";

    } elseif(preg_match('/\s/',$username)){
        $error = "Username cannot contain spaces";
    }
    elseif (strlen($username) < 6) {
    $error = "Username must contain more than or equal to 6 characters";

    }
    
    elseif(preg_match('/[^a-zA-Z0-9]/',$username)){
        $error ="Username cannot contain special characters ";
    }
    elseif(($numCount = preg_match_all('/\d/',$username))>2){
        $error = "Only upto 2 numbers are allowed";

    }     elseif(preg_match('/\s/',$password)){
        $error = "Password cannot contain spaces";
    }
       elseif (strlen($password) <= 6) {
        $error = "Password must be more than 6 characters long!";
       }
    elseif(!preg_match('/\d/',$password)){
    $error = "Password must contain atleast 1 number!";
    }
    elseif (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        $error = "Password must contain at least 1 special character!";
    }
    else {
        $check_sql = "SELECT user_id FROM users WHERE email =? OR username=?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or Email is already registered";
            $stmt->close();
        } else {
            $stmt->close();

            $assigned_role = 'customer';
            if (!empty($admin_key)) {
                if ($admin_key === ADMIN_SECRET_KEY) {
                    $assigned_role = 'admin';

                } else {
                    $error = "Invalid Admin Secret Key!";
                }
            }
            if (empty($error)) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $insert_sql = "INSERT INTO users(username,email,password,role)VALUES(?,?,?,?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("ssss", $username, $email, $hashed_password, $assigned_role);

                if ($stmt->execute()) {
                    $success = "Account Sucessfully Created";

                } else {
                    $error = "Registration entry failed";
                }
                $stmt->close();
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
    <title>REGISTER | E-KHELKUD</title>
    <link rel="stylesheet" href="../assets/css/register.css">
</head>

<body>
    <div class="authentication">
        <h2>E-KHELKUD REGISTER PAGE</h2>
        <h1>Create an Account</h1>
        
        <?php if (!empty($error)) echo "<p class='alert-error'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p class='alert-success'>$success</p>"; ?>
        
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="e.g saugat1" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="e.g saugat@gmail.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
            </div>
            
            <div class="form-group">
                <label for="admin_key">Admin Secret Key</label>
                <input type="text" name="admin_key" id="admin_key" placeholder="Leave Blank By Customers">
            </div>
            
            <button type="submit" class="btn-register">Register</button>
        </form>
        
        <p class="auth-footer">Already have an account? <a href="login.php">Login here</a></p>
    </div> 
   
</body>

</html>