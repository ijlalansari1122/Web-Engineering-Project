<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1
header('Pragma: no-cache'); // HTTP 1.0
header('Expires: 0'); // Proxies

$cityName = isset($_GET['city']) ? $_GET['city'] : '';
$apiKey = '241993c804a060173c5e0e0cc0e4e6dd';  // Your new OpenWeatherMap API key

if (empty($cityName)) {
    echo json_encode(['error' => 'City parameter is missing.']);
    exit;
}

$apiUrl = "https://api.openweathermap.org/data/2.5/forecast?q={$cityName}&appid={$apiKey}&units=metric";

$response = @file_get_contents($apiUrl);

if ($response === FALSE) {
    echo json_encode(['error' => 'Error fetching data from API.']);
    exit;
}

$data = json_decode($response, true);

if ($data && $data['cod'] == '200') {
    $city = $data['city']['name'];
    $forecast = [];
    foreach ($data['list'] as $entry) {
        $date = new DateTime($entry['dt_txt']);
        if ($date->format('H:i:s') === '12:00:00') { // Get the forecast for 12:00 PM each day
            $forecast[] = [
                'date' => $date->format('Y-m-d'),
                'temperature' => $entry['main']['temp'],
                'humidity' => $entry['main']['humidity'],
                'weather' => $entry['weather'][0]['description'],
                'icon' => $entry['weather'][0]['icon']
            ];
        }
        if (count($forecast) === 4) {
            break; // Stop after getting 4 days of data
        }
    }

    echo json_encode([
        'city' => $city,
        'forecast' => $forecast
    ]);
} else {
    echo json_encode(['error' => 'City not found or API error.']);
}
?>
