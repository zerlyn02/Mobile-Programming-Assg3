<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Verify token and expiration
    $sql = "SELECT * FROM user WHERE email='$email' AND reset_token='$token' AND token_expires_at > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Reset Password</title>
            <link rel='stylesheet' href='css/main.css'>
            <style>
                body {
                    background-image: url('/mobileprog/src/images/background.jpg');
                    background-size: cover; 
                    background-position: center; 
                    background-attachment: fixed;
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .reset-container {
                    max-width: 400px;
                    margin: 100px auto;
                    padding: 20px;
                    background: rgba(255, 255, 255, 0.8);
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .reset-container h2 {
                    margin-bottom: 20px;
                }
                .reset-container input {
                    width: 90%;
                    padding: 10px;
                    margin: 10px 0;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                }
                .reset-container button {
                    width: 100%;
                    padding: 10px;
                    background: #007bff;
                    border: none;
                    color: white;
                    border-radius: 5px;
                    cursor: pointer;
                }
                .reset-container button:hover {
                    background: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class='reset-container'>
                <h2>Reset Password</h2>
                <form action='' method='POST'>
                    <input type='hidden' name='email' value='$email'>
                    <input type='password' name='new_password' placeholder='Enter new password' required>
                    <button type='submit'>Reset Password</button>
                </form>
            </div>
        </body>
        </html>";
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
