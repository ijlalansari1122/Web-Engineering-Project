<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cityName = isset($_GET['city']) ? $_GET['city'] : '';
$apiKey = 'ae567eb6f85b41419ea71402241408';  // Your WeatherAPI key

if (empty($cityName)) {
    echo json_encode(['error' => 'City parameter is missing.']);
    exit;
}

$apiUrl = "http://api.weatherapi.com/v1/forecast.json?q={$cityName}&key={$apiKey}&days=4";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Disable SSL verification for troubleshooting
$response = curl_exec($ch);

if (curl_errno($ch)) {
    $errorMessage = curl_error($ch);
    echo json_encode(['error' => 'Error fetching data from API.', 'details' => $errorMessage]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$data = json_decode($response, true);

if (isset($data['error'])) {
    echo json_encode(['error' => $data['error']['message']]);
} else {
    $city = $data['location']['name'];
    $forecast = [];
    foreach ($data['forecast']['forecastday'] as $day) {
        $forecast[] = [
            'date' => $day['date'],
            'temperature' => $day['day']['avgtemp_c'],
            'humidity' => $day['day']['avghumidity'],
            'weather' => $day['day']['condition']['text'],
            'icon' => $day['day']['condition']['icon']
        ];
    }

    echo json_encode([
        'city' => $city,
        'forecast' => $forecast
    ]);
}
?>
