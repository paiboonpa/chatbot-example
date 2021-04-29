<?php

ini_set('display_errors', 1);

/**************************************************************/
/************* EDIT YOUR LINE CONFIG HERE!! ******************/
/**************************************************************/

$channelSecret = 'bd129704ada285cd55d6c3a4a3b0d238';
$channelToken = '+M2fM81XabzN2EfUctE/xcphUqV/BHB7hhSV/DH0uLdsuoicr4T5FPuc5s53/hGKYdcXtWAxTEJ7uvPoSph24gMII0CY5Q0QVdQP4t245WLliJWkujncOWkQwXWS6a53fIQCHnbD0dMSrFuK6ojs8gdB04t89/1O/w1cDnyilFU=';

require('vendor/autoload.php');

use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class Logger {
	public function info($msg) {
		error_log(date('d/m/y H:i:s').' - '.$msg."\n", 3, 'output.log');
	}
}
$logger = new Logger();

$bot = new LINEBot(new CurlHTTPClient($channelToken), [
    'channelSecret' => $channelSecret
]);

$signature = '';
foreach (getallheaders() as $name => $value) {
    if ($name == 'X-Line-Signature') {
        $signature = $value;
        break;
    }
}

if (empty($signature)) {
    $logger->info('No signature found');
    exit();
}
$post_http_body = file_get_contents('php://input');

// Check request with signature and parse request
try {
    $events = $bot->parseEventRequest($post_http_body, $signature);
} catch (InvalidSignatureException $e) {
    $logger->info('Invalid signature');
    exit();
} catch (InvalidEventRequestException $e) {
    $logger->info('Invalid event request');
    exit();
}

foreach ($events as $event) {
    if (!($event instanceof MessageEvent)) {
        $logger->info('Non message event has come');
        continue;
    }

    if (!($event instanceof TextMessage)) {
        $logger->info('Non text message has come');
        continue;
    }

    $replyText = $event->getText();
    $logger->info('Reply text: '.$replyText);
    $resp = $bot->replyText($event->getReplyToken(), $replyText);
    $logger->info($resp->getHTTPStatus() . ': ' . $resp->getRawBody().$replyText);
}
