<?php
/**
 * Created by PhpStorm.
 * User: beremaran
 * Date: 09.05.2017
 * Time: 14:21
 */

session_start();

$PRINT_HTML = false;
$REMOTE_ADDRESS = "beremaran.com";
$PROXY_ADDRESS = "localhost:8090";

//$ESCAPED_REMOTE_ADDRESS = str_replace(".", "\\.", $REMOTE_ADDRESS);

//$REMOTE_CONTENT_REPLACE = array(
//    '/(src|href)="(https?:\/\/)?(www\.|)?(beremaran\.com)?(.*)"/'
//);

function prepareRemoteURL()
{
    global $REMOTE_ADDRESS;
    return $REMOTE_ADDRESS . $_SERVER['REQUEST_URI'];
}

function replaceUrl($url)
{
    global $REMOTE_ADDRESS;
    global $PROXY_ADDRESS;
    return str_replace($REMOTE_ADDRESS, $PROXY_ADDRESS, $url);
}

function handleHeaderLine($curl, $header_line)
{
    global $REMOTE_ADDRESS;
    if (strpos($header_line, $REMOTE_ADDRESS) > 0)
        header(replaceUrl($header_line));
    return strlen($header_line);
}

$REQUEST_HEADERS = getallheaders();
if (isset($REQUEST_HEADERS["Host"]))
    $REQUEST_HEADERS["Host"] = $REMOTE_ADDRESS;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://" . prepareRemoteURL());
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $REQUEST_HEADERS);
curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'handleHeaderLine');
curl_setopt($ch, CURLOPT_VERBOSE, 1);
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
}
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookies/'.session_id().'_cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookies/'.session_id().'_cookie.txt');
$response = replaceUrl(curl_exec($ch));
curl_close($ch);

if ($PRINT_HTML)
    echo "<pre><code>" . htmlentities($response) . "</code></pre>";
else
    echo $response;
