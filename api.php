<?php
/**
 * Created by PhpStorm.
 * User: tk
 * Date: 5/5/17
 * Time: 12:44 PM
 */

const BASEURL = "https://api.gotinder.com/";
const USERAGENT = 'Tinder Android Version 6.11.0';


if( $_GET['method'] === 'recs' ) {
    sendResponse( getRecommendations() );
}

if( $_GET['method'] === 'like' ) {
    $item = $_POST;
    sendResponse( sendLike($item) );
}

function sendResponse($responseAsJson) {
    die( json_encode($responseAsJson) );
}

function getRecommendations() {
    $response = tinderRequest(BASEURL . 'recs');
    return $response;
}

function sendLike($item) {
    return tinderRequest( getLikeUrl($item) );
}

function getAuthorizationHeader() {
    return [
        'User-Agent: Tinder Android Version 6.11.0',
        'X-Auth-Token: 52164c14-7b22-4a79-a587-52e437e42c9d'
    ];
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
    $response = curl_exec($curl);

    if( $response === false ) {
        throw new Exception('Curl-Error: ' . curl_error($curl));
    }

    return json_decode( $response );
}


function getLikeUrl($item, $type = 'like') {
//    var_export($item);
//    die();
    return BASEURL . $type . "/" . $item['_id'] . '?photoId=' . $item['photos'][0]['id'] .
        '&user_traveling=true&content_hash=' . ['$item->content_hash'] . '&s_number=' . $item['s_number'];
}
