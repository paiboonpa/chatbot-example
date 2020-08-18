<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed

$fb = new \Facebook\Facebook([
  'app_id' => '{your-app-id}',
  'app_secret' => '{your-secret-id}',
  'default_graph_version' => 'v3.1',
  //'default_access_token' => '{access-token}', // optional
]);

$fb->setDefaultAccessToken('{your-page-access-token}');

$a_post = array(
    'message' => "My test post"
);
$a_post_photo = array(
  'caption' => 'Test upload photo2',
  'url' => 'https://codekit.co/img/logo.png'
);

try {
  //$response = $fb->get('/218056112203420_218064452202586/comments');
  //$response = $fb->post('/me/feed', $a_post); // post to page
  $response = $fb->post('/me/photos',$a_post_photo);
} catch(Exception $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$me = $response->getDecodedBody();
var_dump($me);

?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>php-sdk</title>
  </head>
  <body>
    
  </body>
</html>
