<?php

const BASEURL = 'https://api.gotinder.com/';

$response = getRecommendations();

file_put_contents('recs/out',  serialize($response['results']) );
header("Location: http://tinderoo.local/like.php");
die();

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

function tinderRequest($url) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, getAuthorizationHeader() );
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

	$response = curl_exec($curl);
	
	if( $response === false ) {
		throw new Exception('Curl-Error: ' . $curl_error($curl));
	}

	return json_decode( $response, true );
}


function getLikeUrl($item) {
	return BASEURL . "like/" . $item['_id'] . '?photoId=' . $item['photos'][0]['id'] . 
		'&user_traveling=true&content_hash=' . $item['content_hash'] . '&s_number=' . $item['s_number'];
}
