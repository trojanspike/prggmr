<?php
//The Server
error_reporting(E_ALL);
$server = function($address, $port){
    //$address = "127.0.0.1";
    //$port = "10000";
    
    /* create a socket in the AF_INET family, using SOCK_STREAM for TCP connection */
    $mysock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_bind($mysock, $address, $port);
    socket_listen($mysock);
    #socket_set_nonblock($mysock);
    //$client = socket_accept($mysock);
    echo "Listening on Server $address:$port\n";
    $i = 0;
    while(true):
        $client = @socket_accept($mysock);
        if (false !== $client) {
            $i++;
            echo "Sending $i to client.\n";
            socket_write($client, $i, strlen($i));
            
            $input = socket_read($client, 2048);
            
            if ($input == 'end') {
                socket_close($client);
        
                socket_close($mysock);
            }
            
            echo "Response from client is: $input\n";
        }
    endwhile;
};

$server('127.0.0.1', 1338);
