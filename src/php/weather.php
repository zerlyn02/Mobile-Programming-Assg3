<?php
session_start();
require 'db_connection.php';

// Add logging function
function logActivity($activity) {
    global $conn;
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("INSERT INTO user_log (user_id, function_name, access_time) VALUES (?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("is", $_SESSION['user_id'], $activity);
            $success = $stmt->execute();
            if (!$success) {
                error_log("Failed to log activity: " . $stmt->error);
            }
            $stmt->close();
        } else {
            error_log("Failed to prepare statement: " . $conn->error);
        }
    } else {
        error_log("No user_id in session");
    }
}

// Handle the weather search logging
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['city'])) {
    $city = trim($_GET['city']);
    logActivity("Search Weather - City: " . $city);
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
}

$conn->close();
?>