<?php 
date_default_timezone_set('Asia/Bangkok');

require('vendor/autoload.php');

$database_uri = '<Firebase Database URI ของคุณ>';

use Kreait\Firebase\Factory;

// receive request from Dialogflow fulfillment
$request = file_get_contents('php://input');
$request = json_decode($request, true);

// get the intent name
$intent = $request['queryResult']['intent']['displayName'];

if ($intent == 'Generation - custom - yes') {
    // Initialize the Firebase Admin SDK
    $firebase = (new Factory)->withServiceAccount('key.json')->withDatabaseUri($database_uri);

    // Get a reference to the database
    $database = $firebase->CreateDatabase();

    // Set the value at the specified path
    $dataObject = $database->getReference('generation')->getValue();
    
    // save the "year" parameter in the "year" variable
    $year = $request['queryResult']['parameters']['year'];

    if ($year < $dataObject['lost']) {
        $gen = 'Lost Generation';
    } else if ($year < $dataObject['greatest']) {
        $gen = 'Greatest Generation';
    } else if ($year < $dataObject['silent']) {
        $gen = 'Silent Generation';
    } else if ($year < $dataObject['baby']) {
        $gen = 'Baby Boomer';
    } else if ($year < $dataObject['x']) {
        $gen = 'Generation X';
    } else if ($year < $dataObject['y']) {
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