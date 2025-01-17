<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prevent SQL Injection by using prepared statements
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            // Start a session if not started already
            session_start();
            $_SESSION['user_id'] = $user['id']; // Add this line to store user ID
            $_SESSION['username'] = $user['username']; // Store username in session
            header("Location: ../index.html"); // Redirect to index.html
            exit(); // Ensure no further execution after redirection
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }

    $stmt->close();
    $conn->close();
}
?>
