# Broxy

    A simple transparent web proxy written with PHP & cURL with basic cookie support
    
## Usage
Change `$REMOTE_ADDRESS` and `$PROXY_ADDRESS`

~~~~
...

$REMOTE_ADDRESS = "beremaran.com"; // address to be proxified
$PROXY_ADDRESS = "localhost:8090"; // address of Broxy script

...
~~~~

Alternatively you can set `$PRINT_HTML` to `1` for printing response as text, without browser interpret it.