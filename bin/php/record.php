<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

// Modified query to include username from user table
$query = "SELECT user_log.*, user.username 
          FROM user_log 
          JOIN user ON user_log.user_id = user.id 
          WHERE user_log.user_id = ? 
          ORDER BY access_time DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$records = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($records);

$stmt->close();
$conn->close();
?>