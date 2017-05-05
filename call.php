
<?php

const BASEURL = 'https://api.gotinder.com/';

authorize();

die();
$response = getRecommendations();

foreach($response['results'] as $item) {
	var_dump( sendLike($item) );
	echo "<img src='" . $item['photos'][0]['url'] . "'" . '>' . '<br>';
	ob_flush();
	sleep(3);
	die();
}

//var_export($response);

function authorize() {
	$facebookToken = 'EAAPu12Fjx6sBAELgWVyW77j55v4Jy49n8LX6R8oKwvjiRF9ZClMJOgqAQdIfuQTtHJyTJgpqZALUvasjh23QM1MG4uSZCT5SCei7B6oZCiSgQfVt6SZBXtJT7u2rAweGHcQJGIk4mpd4rMPq9pUSq8xdt5BIG53X0i2ogvokHMgZDZD';
	$facebookId = '1621793891194829';
	
	var_dump( tinderRequest( BASEURL . 'auth', 'POST', ['facebook_token' => $facebookToken, 'facebook_id' => $facebookId] ) );
}

function getAuthorizationHeader() {
	return [
		'User-Agent: Tinder Android Version 6.11.0',
		'X-Auth-Token: fb674ffc-edc5-4c0c-a483-8b5622d61538'
	];
}

function getRecommendations() {
	return tinderRequest( BASEURL . "recs");
}

function sendLike($item) {
var_dump( getLikeUrl($item) );
	return tinderRequest( getLikeUrl($item) );
}

function tinderRequest($url, $method = 'GET', $data = []) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

	$header = getAuthorizationHeader();

	if($method === 'POST') {
		$json = json_encode($data);
		curl_setopt($curl,CURLOPT_POST, count($json));

		$header = array_merge($header, [
			'Content-Type: application/json',
    			'Content-Length: ' . strlen($json)
		]);
	}

	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
var_dump($json); die();
	$response = curl_exec($curl);

	die('dsfaadsfasfsafd');

	if( $response === false ) {
		throw new Exception('Curl-Error: ' . $curl_error($curl));
	}

	return json_decode( $response, true );
}


function getLikeUrl($item) {
	return BASEURL . "like/" . $item['_id'] . '?photoId=' . $item['photos'][0]['id'] .
		'&user_traveling=true&content_hash=' . $item['content_hash'] . '&s_number=' . $item['s_number'];
}
