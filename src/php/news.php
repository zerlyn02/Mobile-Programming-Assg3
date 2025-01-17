<?php
if (isset($_GET['country'])) {
    $country = strtolower(trim($_GET['country']));

    // Map country names to codes
    $countryCodes = [
        "china" => "cn",
        "indonesia" => "id",
        "japan" => "jp",
        "malaysia" => "my",
        "united kingdom" => "gb",
        "united states of america" => "us",
        "world" => "world"
    ];

    if (!array_key_exists($country, $countryCodes)) {
        echo json_encode(['error' => 'Invalid country. Please select a valid country.']);
        exit;
    }

    $apiKey = 'pub_65706fc5bcd42aa89bd5c273e280597c8d11d';
    $baseUrl = 'https://newsdata.io/api/1/news';
    $countryCode = $countryCodes[$country];

    $url = "{$baseUrl}?apikey={$apiKey}&q={$country}&country={$countryCode}";

    $response = file_get_contents($url);

    if ($response === false) {
        echo json_encode(['error' => 'Failed to fetch news from the API.']);
        exit;
    }

    $data = json_decode($response, true);

    if (!isset($data['results']) || empty($data['results'])) {
        echo json_encode(['error' => 'No news found for this country.']);
        exit;
    }

    // Return only the first 10 results
    echo json_encode(array_slice($data['results'], 0, 10));
} else {
    echo json_encode(['error' => 'Country parameter is missing.']);
}
?>
