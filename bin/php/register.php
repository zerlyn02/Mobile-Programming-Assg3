<?php
session_start();
echo "Session started";  // Check if session starts properly

require 'db_connection.php';
echo "DB connection successful";  // Check if DB connection works

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "POST request received";  // Check if form is submitted

    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    echo "Form data received";  // Check if form data is correct

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    echo "Password hashed";  // Check if password hashing works

    if ($hashed_password === false) {
        die("Password hashing failed.");
    }

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    echo "SQL statement prepared";  // Check if SQL statement is prepared

    // Bind parameters
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "Registration successful";  // Check if execution works
        header("Location: ../login.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;  // Provide error message if any issue
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
