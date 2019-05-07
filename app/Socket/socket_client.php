<?php
set_time_limit(0);
$client_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($client_socket, '127.0.0.1', 8000);
if(isset($argv[1]))
{
    $send = 'client value '.$argv[1];
}
else
{
    $send = "default";
}
socket_write($client_socket, $send."\r\n");
$response = socket_read($client_socket, 1024);
echo "server: ".$response;
socket_close($client_socket);