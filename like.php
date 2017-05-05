<script type="text/javascript">
	setTimeout(function(){ location.reload(); }, 3000);
</script>

<?php

const BASEURL = 'https://api.gotinder.com/';

$recommendations = unserialize( file_get_contents('recs/out') );

if( count($recommendations) === 0 ) {
	header("Location: http://tinderoo.local/new_recs.php");
	die();
}

$indexToDrop = false;

foreach($recommendations as $index => $item) {
	echo "<img src='" . $item['photos'][0]['url'] . "'" . '>' . '<br>';
	ob_flush();
	$indexToDrop = $index;
	var_dump( sendLike($item) );
	break;
}

unset($recommendations[$indexToDrop]);
file_put_contents('recs/out', serialize($recommendations) );


function getAuthorizationHeader() {
	return [
		'User-Agent: Tinder Android Version 6.11.0',
		'X-Auth-Token: 52164c14-7b22-4a79-a587-52e437e42c9d'
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
		'&content_hash=' . $item['content_hash'] . '&s_number=' . $item['s_number'];
}
