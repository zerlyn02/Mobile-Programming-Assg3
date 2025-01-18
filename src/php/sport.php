<?php
session_start();
require 'db_connection.php';

// Add function to get player name from API
function getPlayerName($playerId) {
    $apiKey = "ETb9qACWFPcE7emfp2ev3AGbyoQ98pUc9ms81NTi";
    $baseUrl = "https://api.sportradar.com/badminton/trial/v2/en/competitors/";
    $apiUrl = $baseUrl . $playerId . "/profile.json?api_key=" . $apiKey;
    
    $response = @file_get_contents($apiUrl);
    if ($response !== FALSE) {
        $data = json_decode($response, true);
        if (isset($data['competitor']['name'])) {
            return $data['competitor']['name'];
        }
    }
    return $playerId; // Return ID if name cannot be fetched
}

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

// Handle the sport profile view logging
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id1']) && isset($_GET['id2'])) {
        // For mixed doubles
        $player1Name = getPlayerName($_GET['id1']);
        $player2Name = getPlayerName($_GET['id2']);
        logActivity("View Sport Profile - Mixed Players: $player1Name and $player2Name");
    } elseif (isset($_GET['id'])) {
        // For single player
        $playerName = getPlayerName($_GET['id']);
        $category = isset($_GET['category']) ? $_GET['category'] : 'Player';
        
        if ($category === 'men') {
            logActivity("View Sport Profile - Male Player: $playerName");
        } elseif ($category === 'women') {
            logActivity("View Sport Profile - Female Player: $playerName");
        } else {
            logActivity("View Sport Profile - Player: $playerName");
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
}

$conn->close();
?>