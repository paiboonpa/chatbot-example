<?php 
date_default_timezone_set('Asia/Bangkok');

// receive request from Dialogflow fulfillment
$request = file_get_contents('php://input');
$request = json_decode($request, true);

// get the intent name
$intent = $request['queryResult']['intent']['displayName'];

if ($intent == 'Default Welcome Intent') {
    // Set the response text
    $responseText = "Hello Software Park!";
    
    // Set the response
    $response = [
        'fulfillmentText' => $responseText,
        'source' => "webhook"
    ];
    
    // Respond to the request
    echo json_encode($response);
}