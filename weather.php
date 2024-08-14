<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1
header('Pragma: no-cache'); // HTTP 1.0
header('Expires: 0'); // Proxies

$cityName = $_GET['city'];
$apiKey = '3e1efd621271c3ba3d46ff1ae0f30862';  // Your new OpenWeatherMap API key

$apiUrl = "https://api.openweathermap.org/data/2.5/forecast?q={$cityName}&appid={$apiKey}&units=metric";
$response = file_get_contents($apiUrl);
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
    echo json_encode(['error' => 'City not found.']);
}
?>
