<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $token = $_GET['token'];
    $email = $_GET['email'];

    // Verify token and expiration
    $sql = "SELECT * FROM user WHERE email='$email' AND reset_token='$token' AND token_expires_at > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "
        <form action='' method='POST'>
            <input type='hidden' name='email' value='$email'>
            <input type='password' name='new_password' placeholder='Enter new password' required>
            <button type='submit'>Reset Password</button>
        </form>";
    } else {
        echo "Invalid or expired reset token.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $update_sql = "UPDATE user SET password='$hashed_password', reset_token=NULL, token_expires_at=NULL WHERE email='$email'";
    if ($conn->query($update_sql) === TRUE) {
        echo "Your password has been successfully reset.";
    } else {
        echo "Error resetting password.";
    }
}
?>
