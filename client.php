<?php
//The Client
error_reporting(E_ALL);

$address = "127.0.0.1";
$port = 10000;

/* Create a TCP/IP socket. */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
} else {
    echo "socket successfully created.\n";
}

echo "Attempting to connect to '$address' on port '$port'...";
$result = socket_connect($socket, $address, $port);
if ($result === false) {
    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
} else {
    echo "successfully connected to $address.\n";
}

$i = 0;
while (true == true)
{
    $i++;
    echo "Sending $i to server.\n";
    socket_write($socket, $i, strlen($i));
    
    $input = socket_read($socket, 2048);
    echo "Response from server is: $input\n";
}

echo "Closing socket...";
socket_close($socket);
?>