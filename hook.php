<?php

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
try {
	$accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
	// When Graph returns an error	
	echo 'Graph returned an error: ' . $e->getMessage();
	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	// When validation fails or other local issues
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
	exit;
}

if (isset($accessToken)) {
	// Logged in!
	var_dump($accessToken);
	$_SESSION['facebook_access_token'] = (string) $accessToken;
	// Now you can redirect to another page and use the
	// access token from $_SESSION['facebook_access_token']
}
