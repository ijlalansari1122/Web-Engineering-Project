<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Your OpenWeatherMap API key
$apiKey = "241993c804a060173c5e0e0cc0e4e6dd";

// Get the city name from the query string
$cityName = isset($_GET['city']) ? $_GET['city'] : '';

if ($cityName) {
    // API endpoint with city name and API key
    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($cityName) . "&appid=" . $apiKey . "&units=metric";

    // Make the API request
    $response = file_get_contents($apiUrl);

    if ($response === FALSE) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to connect to the API.']);
        exit;
    }

    // Decode the JSON response
    $weatherData = json_decode($response, true);

    // Log the raw response for debugging
    error_log("API Response: " . print_r($weatherData, true));

    // Check if the API response contains weather data
    if ($weatherData['cod'] == 200) {
        $temp = $weatherData['main']['temp'];
        $humidity = $weatherData['main']['humidity'];
        $description = ucfirst($weatherData['weather'][0]['description']);
        $windSpeed = $weatherData['wind']['speed'];
        $icon = $weatherData['weather'][0]['icon'];

        // Create an array with the data to return
        $data = array(
            'status' => 'success',
            'city' => $cityName,
            'temperature' => $temp,
            'humidity' => $humidity,
            'description' => $description,
            'windSpeed' => $windSpeed,
            'icon' => $icon
        );
    } else {
        // If the city is not found or another error occurred
        $data = array('status' => 'error', 'message' => $weatherData['message']);
    }
} else {
    // If no city name was provided
    $data = array('status' => 'error', 'message' => 'Please provide a city name.');
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
