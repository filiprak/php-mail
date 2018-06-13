<?php

// Helper functions

function response_json($array, $code) {
    $array['code'] = $code;
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($array);
    exit;
}

function response_error($message, $code) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([ 'message' => $message, 'code' => $code ]);
    exit;
}

// Filter POST request type
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_error("Request method: ".$_SERVER['REQUEST_METHOD']." is not supported. Please use POST method.",
        400);
}


$req_data = json_decode(file_get_contents('php://input'), true);
if ($req_data === NULL) {
    response_error("Unable to parse request body", 400);
}

// Validate parameters
if (!isset($req_data['to']) || !isset($req_data['subject']) || !isset($req_data['message'])) {
    response_error("Missing parameters 'to', 'subject', 'message' are required and 'headers' optional", 400);
}

$to = $req_data['to'];
$subject = $req_data['subject'];
$message = $req_data['message'];
//$headers = $req_data['headers'];

$headers = 'From: filiprak@filiprak.com' . "\r\n" .
    'Reply-To: filiprak@filiprak.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();


// Validate data

if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    response_error("Invalid email format in 'to' attribute", 400);
}

if(!mail($to, $subject, $message)) {
    response_error("Failed to sent email", 503);
} else {
    response_json([ 'message' => 'Email was sent' ], 200);
};