<?php

if(isset($argv[1]))
{
    $msg = $argv[1];
    $socket = stream_socket_client('tcp://127.0.0.1:8000', $errno, $errstr);
    if(!$socket)
    {
        die($errno.$errstr);
    }
    else
    {
        // stream_set_blocking($socket, 0);
        for($index = 0; $index < 3; $index++)
        {
            fwrite($socket, " client: $msg $index ");
            usleep(100000);
        }
        fwrite($socket, "\r\n");
        $response = fread($socket, 1024);
        file_put_contents('log.txt', date("[H:i:s] ", time()).$response."\n", FILE_APPEND);
        fclose($socket);
    }
}
else
{
    for($index = 0; $index < 3; $index++)
    {
        system('PHP '.__FILE__." $index:test");
    }
}