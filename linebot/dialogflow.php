<?php
require('vendor/autoload.php');

use Dialogflow\WebhookClient;
ีuse \Dialogflow\RichMessage\Text;

class Logger {
	public function info($msg) {
		error_log(date('d/m/y H:i:s').' - '.$msg."\n", 3, 'output.log');
	}
}
$logger = new Logger();

$json_content = json_decode(file_get_contents('php://input'));

$agent = new WebhookClient($json_content,true);
$logger->info(print_r($json_content,true));
$intent = $agent->getIntent();
$parameters = $agent->getParameters();
$logger->info($intent);
$logger->info(print_r($parameters,TRUE));

$text = \Dialogflow\RichMessage\Text::create()
    ->text('This is text')
    ->ssml('<speak>This is <say-as interpret-as="characters">ssml</say-as></speak>')
;
$agent->reply($text);
header('Content-type: application/json');
$logger->info(json_encode($agent->render()));