<?php
/**************************************************************/
/************* EDIT YOUR LINE CONFIG HERE!! ******************/
/**************************************************************/

$channelSecret = '{your-channel-secret}';
$channelToken = '{your-channel-token}';

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