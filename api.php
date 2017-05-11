<?php
/**
 * Created by PhpStorm.
 * User: tk
 * Date: 5/5/17
 * Time: 12:44 PM
 */

const BASEURL = "https://api.gotinder.com/";
const BASEURL = "https://api.gotinder.com/";
const USERAGENT = 'Tinder Android Version 6.11.0';
const TOKENFILE = 'tmp/auth.token';

if( !file_exists(TOKENFILE) ) {
    authorize(); /** creates token-file */
}

handleRequest(1);

function handleRequest($tries)
{
    if($tries > 2) {
        throw new Exception('foo');
    }
    try {
        if ($_GET['method'] === 'recs') {
            sendResponse(getRecommendations());
        }
        if( $_GET['method'] === 'like' ) {
            $item = $_POST;
            sendResponse( sendLike($item) );
        }
    } catch (Exception $e) {
        authorize();
        handleRequest($tries + 1);
    }
}


function authorize() {
    $facebookToken = 'EAAGm0PX4ZCpsBAEMEpYDHVNpudlCZBKOOt8ECZCyR4JTznFKtc6Pn9dYhQd0gDcStkKHgDUN0bhhfMs9kx5Td7hctZCDMfYBpPZCcbnmQNksuy1hOiHAQWWlK5pphWtPZANoNT8JoIZCdwE9lo91oPQ2a1rVZAsXfjGu4VGXOKN9jOoGFZCj2ZCezBooC0UkP3qsaSIA70r0cGUcoWuYPgUzM0';
    $facebookId = '745892325451661';

    $data = [
        'token' => $facebookToken,
        'id' => $facebookId,
        'client_version' => '6.11.0',
        'install_id' => 'fmXZhUvS5rM'
    ];

    $response = tinderRequest(
        BASEURL . '/v2/auth/login/facebook',
        'POST',
        $data,
        false
    );

    $newApiToken = $response->data->api_token;

    if( null === $newApiToken ) {
        throw new Exception('Cannot get valid response from authorization-api');
    }
    file_put_contents(TOKENFILE, $newApiToken);
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
    $token = file_get_contents(TOKENFILE);
    return [
        'X-Auth-Token: ' . $token
    ];
}

function tinderRequest($url, $method = 'GET', $data = [], $includeAuthHeader = true) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

    $header = [
        'User-Agent: Tinder Android Version 6.11.0',
        'os-version: 23',
        'app-version: 2082',
        'platform: android',
        'Accept-Language: de',

    ];
    if($includeAuthHeader) {
        $header = array_merge($header, getAuthorizationHeader());
    }

    if($method === 'POST') {
        $json = json_encode($data);
        curl_setopt($curl,CURLOPT_POST, true);
        curl_setopt($curl,CURLOPT_POSTFIELDS, $json);

        $header = array_merge($header, [
            'app-session: 2509402401ac99ad7490e3e2249abf0f2037495e',
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);
    }

    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    $response = curl_exec($curl);

    if( $response === false ) {
        throw new Exception('Curl-Error: ' . curl_error($curl));
    }

    $decodedResponse = json_decode($response);

    if( isset($decodedResponse->status) && $decodedResponse->status === 401 ) {
        throw new Exception('not authorized');
    }

    return $decodedResponse;
}


function getLikeUrl($item, $type = 'like') {
//    var_export($item);
//    die();
    $item = $item['user'];
    return BASEURL . $type . "/" . $item['_id'] . '?photoId=' . $item['photos'][0]['id'] .
        '&user_traveling=true&content_hash=' . ['$item->content_hash'] . '&s_number=' . $item['s_number'];
}
//
//{
//    "token": "EAAGm0PX4ZCpsBAEMEpYDHVNpudlCZBKOOt8ECZCyR4JTznFKtc6Pn9dYhQd0gDcStkKHgDUN0bhhfMs9kx5Td7hctZCDMfYBpPZCcbnmQNksuy1hOiHAQWWlK5pphWtPZANoNT8JoIZCdwE9lo91oPQ2a1rVZAsXfjGu4VGXOKN9jOoGFZCj2ZCezBooC0UkP3qsaSIA70r0cGUcoWuYPgUzM0",
//	"client_version": "6.11.0",
//	"id": "745892325451661",
//	"install_id": "fmXZhUvS5rM"
//}

/**
 * POST /v2/auth/login/facebook HTTP/1.1
app-session: 2509402401ac99ad7490e3e2249abf0f2037495e
User-Agent: Tinder Android Version 6.11.0
os-version: 23
app-version: 2082
platform: android
Accept-Language: de
Content-Type: application/json; charset=UTF-8
Content-Length: 307
Host: api.gotinder.com
Connection: Keep-Alive
Accept-Encoding: gzip

{"token":"EAAGm0PX4ZCpsBAEMEpYDHVNpudlCZBKOOt8ECZCyR4JTznFKtc6Pn9dYhQd0gDcStkKHgDUN0bhhfMs9kx5Td7hctZCDMfYBpPZCcbnmQNksuy1hOiHAQWWlK5pphWtPZANoNT8JoIZCdwE9lo91oPQ2a1rVZAsXfjGu4VGXOKN9jOoGFZCj2ZCezBooC0UkP3qsaSIA70r0cGUcoWuYPgUzM0","client_version":"6.11.0","id":"745892325451661","install_id":"fmXZhUvS5rM"}
 *
 */

/**
 *
 * curl -H 'app-session: 46a044925c68930c7fc13d8b7895af50656272ca' -H 'User-Agent: Tinder Android Version 6.11.0' -H 'os-version: 23' -H 'app-version: 2082' -H 'platform: android' -H 'Accept-Language: de' -H 'Content-Type: application/json; charset=UTF-8' -H 'Host: api.gotinder.com' --data-binary '{"token":"EAAGm0PX4ZCpsBADZAJsDnR85shkCadsjbJLgHJon3WZAm2OzI1ZAxsPPzIIBy74k3xO29wOjPjGpjZA02Czuqt0zwXmjVJlVQLfB64K1ZBC6XyJZAq2xZAgmxypoMRmGpSkuC2JFJSGVnEZCCifUQgaWmvLNfJhb9XpRmOPVSEp6L1nFB0QdO5C4wKvmQBuYvVOdA9erYIkbWxAZDZD","client_version":"6.11.0","id":"745892325451661","install_id":"fmXZhUvS5rM"}' --compressed 'https://api.gotinder.com/v2/auth/login/facebook'
 *
 */