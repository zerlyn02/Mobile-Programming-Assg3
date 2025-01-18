<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins
header("Content-Type: application/json"); // Set content type to JSON

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
