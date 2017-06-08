<?php
/**
 * MIT License
 *
 * Copyright (c) 2017 Berke Emrecan ARSLAN
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

session_start();

$PRINT_HTML = false;
$REMOTE_ADDRESS = "en.wikipedia.com"; // address to be proxified
$PROXY_ADDRESS = "localhost:8090"; // address of Broxy script

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
curl_setopt($ch, CURLOPT_URL, "https://" . prepareRemoteURL());
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $REQUEST_HEADERS);
curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'handleHeaderLine');
curl_setopt($ch, CURLOPT_VERBOSE, 1);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
}
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookies/' . session_id() . '_cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookies/' . session_id() . '_cookie.txt');
$response = replaceUrl(curl_exec($ch));
curl_close($ch);

header('Content-Type: ' . curl_getinfo($ch, CURLINFO_CONTENT_TYPE));

if ($PRINT_HTML)
    echo "<pre><code>" . htmlentities($response) . "</code></pre>";
else
    echo $response;
