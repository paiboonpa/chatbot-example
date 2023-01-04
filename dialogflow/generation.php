<?php 
date_default_timezone_set('Asia/Bangkok');

// receive request from Dialogflow fulfillment
$request = file_get_contents('php://input');
$request = json_decode($request, true);

// get the intent name
$intent = $request['queryResult']['intent']['displayName'];

if ($intent == 'Generation - custom - yes') {
    // save the "year" parameter in the "year" variable
    $year = $request['queryResult']['parameters']['year'];

    if ($year < 2443) {
        $gen = 'Lost Generation';
    } else if ($year < 2467) {
        $gen = 'Greatest Generation';
    } else if ($year < 2488) {
        $gen = 'Silent Generation';
    } else if ($year < 2507) {
        $gen = 'Baby Boomer';
    } else if ($year < 2522) {
        $gen = 'Generation X';
    } else if ($year < 2540) {
        $gen = 'Generation Y';
    } else {
        $gen = 'Generation Z';
    }

    header('Content-Type: application/json');

    // Set the response text
    $responseText = "คุณอยู่ในเจน $gen";
    
    // Set the response
    $response = [
        'fulfillmentText' => $responseText,
        'source' => "webhook"
    ];
    
    // Respond to the request
    echo json_encode($response);
}