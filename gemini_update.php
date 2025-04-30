<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost'; // Replace with your database host
$dbname = 'tms_db'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    file_put_contents('error.log', $e->getMessage());
    die("Database connection failed: " . $e->getMessage());
}

// Google API Key
$apiKey = 'AIzaSyA2jmHkVK0VO-Xh6gK2jbUyoztTUCNXENU'; // Replace with your actual Google API key

// Get the payload from the database trigger
$payload = json_decode(file_get_contents('php://input'), true);

if (!$payload) {
    die('No payload received.');
}

file_put_contents('debug_payload.log', print_r($payload, true));

// Extract the table and action from the payload
$table = $payload['table'];
$action = $payload['action'];
$data = $payload['data'];

file_put_contents('debug_extracted.log', "Table: $table\nAction: $action\nData: " . print_r($data, true));

// Prepare the prompt for Gemini
$prompt = "The following change occurred in the database:\n";
$prompt .= "Table: $table\n";
$prompt .= "Action: $action\n";
$prompt .= "Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";

file_put_contents('debug_prompt.log', $prompt);

// Call the Google Generative Language API
try {
    // Correct endpoint for the API
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";

    // Prepare the data payload
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    file_put_contents('debug_gemini_payload.log', json_encode($data, JSON_PRETTY_PRINT));

    // Set up cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute the cURL request
    $result = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $curlError = curl_error($ch);
        file_put_contents('error.log', "cURL Error: $curlError\n");
        throw new Exception("cURL Error: $curlError");
    }

    // Close the cURL session
    curl_close($ch);

    // Decode the JSON response
    $response = json_decode($result, true);

    // Log the response for debugging
    file_put_contents('gemini_update.log', print_r($response, true));

    echo "Gemini updated successfully.";
} catch (Exception $e) {
    file_put_contents('error.log', $e->getMessage());
    die("Error: " . $e->getMessage());
}
?>