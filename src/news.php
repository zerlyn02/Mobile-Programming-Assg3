<?php
session_start(); // Add this at the very top
require 'php/db_connection.php'; // Add database connection

// Add logging function
function logActivity($activity) {
    global $conn;
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("INSERT INTO user_log (user_id, function_name, access_time) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $_SESSION['user_id'], $activity);
        $stmt->execute();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['country'])) {
    header('Content-Type: application/json');
    $country = strtolower(trim($_GET['country']));
    
    // Log the news search activity
    logActivity("Search News - Country: " . $country);

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

    echo json_encode(array_slice($data['results'], 0, 10));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest News by Country</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
        }
        .news-card {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 15px;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
        }
        .news-card h5 {
            color: #007bff;
        }
        .news-image {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 5px;
        }
        #mainPageButton {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 14px;
            padding: 5px 10px;
        }
        #infoButton {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            font-size: 18px;
            line-height: 35px;
            text-align: center;
            cursor: pointer;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <button id="mainPageButton" class="btn btn-outline-primary">Back</button>
        <button id="infoButton">?</button>
        <h1 class="text-center mb-4">Search Latest News by Country</h1>
        <div class="input-group mb-4">
            <input type="text" id="countryInput" class="form-control" placeholder="Enter country name (e.g., China)">
            <button class="btn btn-primary" id="searchButton">Search</button>
        </div>
        <div id="newsContainer">
            <!-- News cards will be dynamically added here -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const countryCodes = {
            "china": "cn",
            "indonesia": "id",
            "japan": "jp",
            "malaysia": "my",
            "united kingdom": "gb",
            "united states of america": "us",
            "world": "world"
        };

        // Back to Main Page Button
        document.getElementById('mainPageButton').addEventListener('click', () => {
            location.href = 'index.html'; // Redirects to index.html
        });

        // Info Button (Show Available Countries and System Info)
        document.getElementById('infoButton').addEventListener('click', () => {
            alert(`Available countries: ${Object.keys(countryCodes).join(', ')}.\n\nNote: This system only shows the 10 latest news available.`);
        });

        // Search Button Click Handler
        document.getElementById('searchButton').addEventListener('click', () => {
            const country = document.getElementById('countryInput').value.trim().toLowerCase();
            if (!country) {
                alert('Please enter a country name!');
                return;
            }

            if (!countryCodes[country]) {
                alert(`"${country}" is not available. Available countries: ${Object.keys(countryCodes).join(', ')}`);
                return;
            }

            fetchNews(country);
        });

        // Fetch News Function
        async function fetchNews(country) {
            try {
                const response = await fetch(`news.php?country=${encodeURIComponent(country)}`);
                const data = await response.json();
                console.log('Response:', data);

                if (data.error) {
                    alert(data.error);
                    document.getElementById('newsContainer').innerHTML = '';
                    return;
                }

                displayNews(data);
            } catch (error) {
                console.error('Error fetching news:', error);
                alert('Failed to fetch news. Please try again later.');
            }
        }

        // Display News Function
        function displayNews(newsArray) {
            const container = document.getElementById('newsContainer');
            container.innerHTML = ''; // Clear previous news

            newsArray.forEach(news => {
                const card = document.createElement('div');
                card.className = 'news-card';

                let imageContent = '';
                if (news.image_url) {
                    imageContent = `<img src="${news.image_url}" alt="News Image" class="news-image mb-3">`;
                }

                card.innerHTML = `
                    ${imageContent}
                    <h5>${news.title || 'No title available'}</h5>
                    <p><strong>Description:</strong> ${news.description || 'No description available'}</p>
                    <p><strong>Published At:</strong> ${news.pubDate || 'No date available'}</p>
                    <p><strong>Source:</strong> ${news.source_name || 'Unknown'}</p>
                    <p><strong>Country:</strong> ${news.country ? news.country.join(', ') : 'Unknown'}</p>
                    <a href="${news.link}" target="_blank" class="btn btn-link">Read More</a>
                `;
                container.appendChild(card);
            });
        }
    </script>
</body>
</html>
