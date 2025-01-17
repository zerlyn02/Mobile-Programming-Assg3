<?php
require 'db_connection.php';
require __DIR__ . '/../../vendor/autoload.php'; // Include the Composer autoloader
// require 'PHPMailer/PHPMailerAutoload.php'; // Include PHPMailer library

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $sql = "SELECT * FROM user WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Generate a unique token for password reset
        date_default_timezone_set('Asia/Kuala_Lumpur'); // Set your server's time zone
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token expires in 1 hour
        echo "Token Expiration Time: " . $expires_at;
        echo "Current Time: " . date("Y-m-d H:i:s", time());

        // Save the token and expiration time in the database
        $update_sql = "UPDATE user SET reset_token='$token', token_expires_at='$expires_at' WHERE email='$email'";
        if ($conn->query($update_sql) === TRUE) {
            // Send email to the user with the password reset link
            $reset_link = "http://localhost/mobileprog/src/php/reset_password.php?token=$token&email=$email";

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Use your email provider's SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = 'zerlyn0816@gmail.com'; // Your Gmail address
            $mail->Password = 'vybh dxtf dike kzsn'; // Your Gmail app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('zerlyn0816@gmail.com', 'Badminton Lookup');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Hi,\n\nClick the link below to reset your password:\n$reset_link\n\nThis link will expire in 1 hour.";

            if ($mail->send()) {
                echo "A password reset link has been sent to your email. Please check your inbox.";
                header('Location: /mobileprog/src/login.html');
                exit();
            } else {
                echo "Error sending email. Please try again.";
            }
        } else {
            echo "Error saving reset token.";
        }
    } else {
        echo "Email not found!";
    }
    $conn->close();
}
?>
