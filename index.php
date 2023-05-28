<?php
require_once 'btc-uah.php';

function handleGetRequest() {
    if ($_SERVER['REQUEST_URI'] === '/api/rate') {
        $rate = getBitcoinUAHValue();
        return $rate;
    }
    return null;
}

function addEmail() {
    $requestData = json_decode(file_get_contents('php://input'), true);

    $email = $requestData['email'] ?? null;
    if ($email === null) {
        http_response_code(400);
        return;
    }

    $databaseFile = 'emails.txt';
    $existingEmails = file($databaseFile, FILE_IGNORE_NEW_LINES);

    if (in_array($email, $existingEmails)) {
        http_response_code(409);
        return;
    }

    file_put_contents($databaseFile, $email . PHP_EOL, FILE_APPEND);

    http_response_code(200);
}
    
function sendEmail($toEmail, $subject, $message, $headers) {
    $apiKey = '';
    $apiSecret = '';
    $mjVersion = 'v3.1';
    $fromEmail = "";

    $url = "https://api.mailjet.com/$mjVersion/send";

    $payload = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $fromEmail
                ],
                'To' => [
                    [
                        'Email' => $toEmail
                    ]
                ],
                'Subject' => $subject,
                'TextPart' => $message
            ]
        ]
    ];

    $headers = [
        'Content-Type: application/json'
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");

    $response = curl_exec($ch);

    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
}

function sendEmails() {
    // For this implementation, you should specify your Mailjet API and API_SECRET keys
    $databaseFile = 'emails.txt';
    $existingEmails = file($databaseFile, FILE_IGNORE_NEW_LINES);
    $rate = getBitcoinUAHValue();
    $subject = 'Email with actual BTC-UAH rate';
    $message = $rate;
    $headers = "From: gses2.app";

    foreach ($existingEmails as $email) {
        $to = $email;
        sendEmail($to, $subject, $message, $headers);
    }

    http_response_code(200);
    echo 'All emails sent successfully.';
}

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = handleGetRequest();
    if ($response !== null) {
        echo $response;
    }
} 
// Handle POST requests
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SERVER['REQUEST_URI'] === '/api/subscribe') {
        addEmail();
    }
    if ($_SERVER['REQUEST_URI'] === '/api/sendEmails') {
        sendEmails();
    }
}
// Invalid request method
else {
    http_response_code(405);
    echo 'Invalid request method.';
}
?>
