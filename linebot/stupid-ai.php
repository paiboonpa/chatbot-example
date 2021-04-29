<?php
/**************************************************************/
/************* EDIT YOUR LINE CONFIG HERE!! ******************/
/**************************************************************/

$channelSecret = 'bd129704ada285cd55d6c3a4a3b0d238';
$channelToken = '+M2fM81XabzN2EfUctE/xcphUqV/BHB7hhSV/DH0uLdsuoicr4T5FPuc5s53/hGKYdcXtWAxTEJ7uvPoSph24gMII0CY5Q0QVdQP4t245WLliJWkujncOWkQwXWS6a53fIQCHnbD0dMSrFuK6ojs8gdB04t89/1O/w1cDnyilFU=';
$your_directory_name = 'paiboon';


$base_url = 'https://'.$_SERVER['HTTP_HOST'].'/'.$your_directory_name.'/';
$_SERVER['DOCUMENT_ROOT'] .= '/'.$your_directory_name;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\AccountLinkEvent;
use LINE\LINEBot\Event\BeaconDetectionEvent;
use LINE\LINEBot\Event\FollowEvent;
use LINE\LINEBot\Event\JoinEvent;
use LINE\LINEBot\Event\LeaveEvent;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\AudioMessage;
use LINE\LINEBot\Event\MessageEvent\ImageMessage;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;
use LINE\LINEBot\Event\MessageEvent\StickerMessage;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\MessageEvent\UnknownMessage;
use LINE\LINEBot\Event\MessageEvent\VideoMessage;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Event\ThingsEvent;
use LINE\LINEBot\Event\UnfollowEvent;
use LINE\LINEBot\Event\UnknownEvent;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\KitchenSink\EventHandler\BeaconEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\FollowEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\JoinEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\LeaveEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\AudioMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\ImageMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\LocationMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\StickerMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\TextMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\MessageHandler\VideoMessageHandler;
use LINE\LINEBot\KitchenSink\EventHandler\PostbackEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\ThingsEventHandler;
use LINE\LINEBot\KitchenSink\EventHandler\UnfollowEventHandler;
use LINE\LINEBot\Constant\MessageType;
use LINE\LINEBot\MessageBuilder;

require('vendor/autoload.php');

class Logger {
	public function info($msg) {
		error_log(date('d/m/y H:i:s').' - '.$msg."\n", 3, 'output.log');
	}
}
$logger = new Logger();
function customAutoloader($className) {
    global $logger;
    $className = str_replace('LINE\LINEBot\KitchenSink\\', '',$className);
    $className = str_replace('\\','/',$className);
    if (file_exists($className . '.php')) {
        require($className . '.php');
        return true;
    }
    return false;
}
spl_autoload_register("customAutoloader");
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

class MyJsonMessage implements MessageBuilder {
    public $json;
    public function __construct($json) {
        $this->json = $json;
    }
    public function buildMessage()
    {
        return [json_decode($this->json, TRUE)];
    }
}

foreach ($events as $event) {
    /** @var EventHandler $handler */
    $handler = null;

    if ($event instanceof MessageEvent) {
        if ($event instanceof TextMessage) {
            $command = $event->getText();

            require_once('parsecsv.php');
            $parsecsv = new parseCSV();
            $a_data = $parsecsv->parse_file('bot_db.csv');
            $b_match = FALSE;
            foreach ($a_data as $data) {
                $a_keyword = explode(',', $data['input']);
                foreach ($a_keyword as $keyword) {
                    if ($data['type'] == 'word' && strpos($command,$keyword) !== FALSE) {
                            $b_match = TRUE;
                        break;
                    } else if ($command == $keyword) {
                        $b_match = TRUE;
                        break;
                    }
                }
                if ($b_match)
                    break;
            }


            if ($b_match) {
                if (substr($data['answer'], 0, 4) == 'http') {
                    $json = '{"type":"image","originalContentUrl":"'.$data['answer'].'","previewImageUrl":"'.$data['answer'].'","animated":false}';
                    $bot->replyMessage($event->getReplyToken(), new MyJsonMessage($json));
                } else {
                    $json = '{"type":"text","text":"'.$data['answer'].'"}';
                    $bot->replyMessage($event->getReplyToken(), new MyJsonMessage($json));
                }
            } else {
                $handler = new TextMessageHandler($bot, $logger, $base_url, $event);
            }
        } elseif ($event instanceof StickerMessage) {
            $handler = new StickerMessageHandler($bot, $logger, $event);
        } elseif ($event instanceof LocationMessage) {
            $handler = new LocationMessageHandler($bot, $logger, $event);
        } elseif ($event instanceof ImageMessage) {
            $handler = new ImageMessageHandler($bot, $logger, $base_url, $event);
        } elseif ($event instanceof AudioMessage) {
            $handler = new AudioMessageHandler($bot, $logger, $base_url, $event);
        } elseif ($event instanceof VideoMessage) {
            $handler = new VideoMessageHandler($bot, $logger, $base_url, $event);
        } elseif ($event instanceof UnknownMessage) {
            $logger->info(sprintf(
                'Unknown message type has come [message type: %s]',
                $event->getMessageType()
            ));
        } else {
            // Unexpected behavior (just in case)
            // something wrong if reach here
            $logger->info(sprintf(
                'Unexpected message type has come, something wrong [class name: %s]',
                get_class($event)
            ));
            continue;
        }
    } elseif ($event instanceof UnfollowEvent) {
        $handler = new UnfollowEventHandler($bot, $logger, $event);
    } elseif ($event instanceof FollowEvent) {
        $handler = new FollowEventHandler($bot, $logger, $event);
    } elseif ($event instanceof JoinEvent) {
        $handler = new JoinEventHandler($bot, $logger, $event);
    } elseif ($event instanceof LeaveEvent) {
        $handler = new LeaveEventHandler($bot, $logger, $event);
    } elseif ($event instanceof PostbackEvent) {
        $handler = new PostbackEventHandler($bot, $logger, $event);
    } elseif ($event instanceof BeaconDetectionEvent) {
        $handler = new BeaconEventHandler($bot, $logger, $event);
    } elseif ($event instanceof AccountLinkEvent) {
        $handler = new AccountLinkEventHandler($bot, $logger, $event);
    } elseif ($event instanceof ThingsEvent) {
        $handler = new ThingsEventHandler($bot, $logger, $event);
    } elseif ($event instanceof UnknownEvent) {
        $logger->info(sprintf('Unknown message type has come [type: %s]', $event->getType()));
    } else {
        // Unexpected behavior (just in case)
        // something wrong if reach here
        $logger->info(sprintf(
            'Unexpected event type has come, something wrong [class name: %s]',
            get_class($event)
        ));
        continue;
    }

    /**************************************************************/
    /************* EDIT YOUR JSON MESSAGE HERE!! ******************/
    /**************************************************************/
    //$json = '{"type":"location","title":"d","address":"a","latitude":35.65910807942215,"longitude":139.70372892916203}';
    //$bot->replyMessage($event->getReplyToken(), new MyJsonMessage($json));

    $handler->handle();
}