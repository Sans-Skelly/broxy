<?php
/*
MIT License

Copyright (c) 2017 Berke Emrecan ARSLAN

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 */

session_start();

$config = json_decode(file_get_contents(__DIR__ . '/config.json'));

/**
 * @return boolean
*/
function isCli()
{
    return (php_sapi_name() === 'cli');
}

/**
 * @param $uri string
 * @return string
*/
function extractProtocol($uri)
{
    return explode(":", $uri)[0];
}

/**
 * @return string
*/
function prepareRemoteURL()
{
    global $config;

    return $config['remoteHost'] . $_SERVER['REQUEST_URI'];
}

/**
 * @param $url string
 * @return string
 */
function replaceUrl($url)
{
    global $config;

    return str_replace($config['remoteHost'], $config['host'], $url);
}

/**
 * @param $curl resource
 * @param $header_line string
 * @return int
 */
function handleHeaderLine($curl, $header_line)
{
    global $config;

    if (strpos($header_line, $config['remoteHost']) > 0) {
        header(replaceUrl($header_line));
    }
    return strlen($header_line);
}

$requestHeaders = getallheaders();
if (isset($requestHeaders["Host"])) {
    $requestHeaders["Host"] = $config['remoteHost'];
}

$ch = curl_init();

curl_setopt(
    $ch,
    CURLOPT_URL,
    extractProtocol($config['remoteHost']) . "://" . prepareRemoteURL()
);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'handleHeaderLine');
curl_setopt($ch, CURLOPT_VERBOSE, 1);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
}
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
curl_setopt(
    $ch,
    CURLOPT_COOKIEJAR,
    __DIR__ . '/cookies/' . session_id() . '_cookie.txt'
);
curl_setopt(
    $ch,
    CURLOPT_COOKIEFILE,
    __DIR__ . '/cookies/'.session_id() . '_cookie.txt'
);
$response = replaceUrl(curl_exec($ch));
curl_close($ch);

header('Content-Type: '.curl_getinfo($ch, CURLINFO_CONTENT_TYPE));

echo $response;
