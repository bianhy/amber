<?php
set_time_limit(0);
$client = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($client, '127.0.0.1', 8000);
socket_write($client, "Form Client client.php \r\n");
while(true)
{
    $response = socket_read($client, 1024);
    echo $response;
}
socket_close($client);