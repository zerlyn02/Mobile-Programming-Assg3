<?php
$to = "recipient@example.com";
$subject = "Test Email";
$message = "This is a test email sent from PHP.";
$headers = "From: your-email@mydomain.com";

if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully.";
} else {
    echo "Failed to send email.";
}
?>
