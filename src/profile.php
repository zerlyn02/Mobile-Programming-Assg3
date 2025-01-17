<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$apiKey = "ETb9qACWFPcE7emfp2ev3AGbyoQ98pUc9ms81NTi";
$baseUrl = "https://api.sportradar.com/badminton/trial/v2/en/competitors/";

$players = [];

// For mixed players, we are expecting two IDs
if (isset($_GET['id1']) && isset($_GET['id2'])) {
    $competitorId1 = $_GET['id1'];
    $competitorId2 = $_GET['id2'];

    // Fetch the first competitor's profile
    $apiUrl1 = $baseUrl . $competitorId1 . "/profile.json?api_key=" . $apiKey;
    $response1 = @file_get_contents($apiUrl1);
    if ($response1 === FALSE) {
        echo json_encode(["error" => "Error fetching data for player 1"]);
        exit();
    }
    $players[] = json_decode($response1, true)['competitor'];

    // Fetch the second competitor's profile
    $apiUrl2 = $baseUrl . $competitorId2 . "/profile.json?api_key=" . $apiKey;
    $response2 = @file_get_contents($apiUrl2);
    if ($response2 === FALSE) {
        echo json_encode(["error" => "Error fetching data for player 2"]);
        exit();
    }
    $players[] = json_decode($response2, true)['competitor'];

    echo json_encode(["players" => $players]);

} elseif (isset($_GET['competitorId'])) { // For single player
    $competitorId = $_GET['competitorId'];
    
    $apiUrl = $baseUrl . $competitorId . "/profile.json?api_key=" . $apiKey;
    $response = @file_get_contents($apiUrl);
    if ($response === FALSE) {
        echo json_encode(["error" => "Error fetching data for player"]);
        exit();
    }

    $responseData = json_decode($response, true);
    if (isset($responseData['competitor'])) {
        echo json_encode($responseData);
    } else {
        echo json_encode(["error" => "No profile data found for competitorId: $competitorId"]);
    }
} else {
    echo json_encode(["error" => "No competitorId provided."]);
}
?>
