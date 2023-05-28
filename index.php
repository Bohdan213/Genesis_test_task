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
    
function sendEmails() {
    // For this implementation servr must have smtp
    $databaseFile = 'emails.txt';
    $existingEmails = file($databaseFile, FILE_IGNORE_NEW_LINES);
    $rate = getBitcoinUSDValue();
    foreach ($existingEmails as $email) {
        $to = $email;
        $subject = 'Email woth actual BTC-UAH rate';
        $message = $rate;
        $headers = "From gses2.app";

        $success = mail($to, $subject, $message. $message);
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
        sendEmail();
    }
}
// Invalid request method
else {
    http_response_code(405);
    echo 'Invalid request method.';
}
?>
