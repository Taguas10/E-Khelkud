<?php
session_start();
include '../../config/connection.php';
if(!isset($_SESSION['user_id'])){
    header('../../auth/login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD']=='POST'  && isset($_POST['update-user'])) {
$user_id = $_SESSION['user_id']; 
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$hashed_password = password_hash($password,PASSWORD_BCRYPT);

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
    else{
 $update_sql ="UPDATE users SET username=?,email=? ,password=? WHERE user_id=?";
 $stmt = $conn->prepare($update_sql);
 $stmt->bind_param('sssi',$username,$email,$hashed_password,$user_id);
 if($stmt->execute()){
    $success ="Updated Successfully";
 }
 else{
    $error="Update Failed";
 }
 $stmt->close();
}
}
$fetch_sql = "SELECT username , email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($fetch_sql);
$stmt->bind_param('i',$user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../assets/css/update_user.css">
</head>
<body>
    <h2>Edit User Profile</h2>
     <?php if (!empty($error)) echo "<p class='alert-error'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p class='alert-success'>$success</p>"; ?>

    <form action="" method="post" class="update_user">
        <div class="form-group">
            <label for ="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo ($user['username'])?>">
        </div>
                <div class="form-group">
            <label for ="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo ($user['email'])?>">
        </div>
                <div class="form-group">
            <label for ="password">Password</label>
            <input type="password" name="password" id="password">
        </div>
        <button type="submit" name="update-user">Update</button>
        <a href ="../../shop.php">Back to shop</a>
    </form> 
</body>
</html>

