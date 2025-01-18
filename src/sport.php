<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins
header("Content-Type: application/json"); // Set content type to JSON

session_start();
require 'db_connection.php';

function logActivity($activity) {
    global $conn;
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("INSERT INTO user_log (user_id, function_name) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $activity);
        $stmt->execute();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['activity'])) {
        logActivity($data['activity']);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

$apiUrl = "https://api.sportradar.com/badminton/trial/v2/en/rankings.json?api_key=ETb9qACWFPcE7emfp2ev3AGbyoQ98pUc9ms81NTi";

// Fetch data from the API
$response = file_get_contents($apiUrl);

if ($response === FALSE) {
    http_response_code(500);
    echo json_encode(["error" => "Unable to fetch data from Sportradar API."]);
} else {
    echo $response; // Return the API response
}
?>
