<?php
require 'db_connection.php';
require __DIR__ . '/../../vendor/autoload.php'; // Include the Composer autoloader
// require 'PHPMailer/PHPMailerAutoload.php'; // Include PHPMailer library

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // Check if username and email match
    $sql = "SELECT * FROM user WHERE username=? AND email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update the password
        $update_sql = "UPDATE user SET password=? WHERE username=? AND email=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sss", $hashed_password, $username, $email);
        
        if ($update_stmt->execute()) {
            echo "<script>
                    alert('Password successfully reset!');
                    window.location.href = '../login.html';
                  </script>";
        } else {
            echo "<script>
                    alert('Error updating password. Please try again.');
                    window.location.href = '../forgot_password.html';
                  </script>";
        }
        $update_stmt->close();
    } else {
        echo "<script>
                alert('Invalid username or email!');
                window.location.href = '../forgot_password.html';
              </script>";
    }
    $stmt->close();
    $conn->close();
}
?>
