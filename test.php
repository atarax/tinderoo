<?php

// fb-id 1621793891194829

if (!session_id()) {
    session_start();
}

require('vendor/autoload.php');

$fb = new Facebook\Facebook([
  'app_id' => '1107033749440427',
  'app_secret' => '23909b5b70219d26eb505182009ce058',
  'default_graph_version' => 'v2.9',
  ]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email', 'user_likes']; // optional
$loginUrl = $helper->getLoginUrl('http://tinderoo.local/hook.php', $permissions);

echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
