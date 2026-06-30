<?php
session_start();
include '../../config/connection.php';

$fetch_sql = "SELECT user_id,username,email,role FROM users ";
$result= $conn->query($fetch_sql);
?>
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Manage Users</title>

    <link rel="stylesheet" href="../../assets/css/user.css">
</head>
<body>
<h2>Users</h2>
<?php if($result->num_rows >0):?>
    <table>
        <thead>
            <tr>
              <th>User_ID</th>
              <th>USERNAME</th>
              <th>EMAIL</th>
              <th>ROLE</th>
              <th>ACTIONS</th>
</tr>
</thead>
<tbody>
    <?php while($user = $result->fetch_assoc()):?>
        <tr>
            <td><?php echo htmlspecialchars($user['user_id']);?></td>
              <td><?php echo htmlspecialchars($user['username']);?></td>
                <td><?php echo  htmlspecialchars($user['email']);?></td>
                  <td><?php echo htmlspecialchars($user['role']);?></td>
                    <td>
                    <form action ="../../controller/admin/delete_user.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This will also wipe their orders!');">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id'];?>"> 
                    <button type ="submit" name="delete_user_btn" class="delete-btn"> DELETE   </button>                 
                    </form> 
                    </td>
                    </tr>
                    <?php endwhile;?>
          </tbody>
          </table>
<?php else:?>
    <h2>No Users Found</h2>
<?php endif ;?>

  
</body>
</html>

