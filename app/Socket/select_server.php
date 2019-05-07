<?php
$port = 8000;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);

//绑定所有进入该端口的连接
socket_bind($sock, '127.0.0.1', $port);

socket_listen($sock);

$clients = array($sock);

while(true)
{
    //socket_select对读写套子节的数字是引用，为了保证clients不被改变，拷贝一份。
    $read = $clients;
    $write = null;
    $expect = null;

    //当没有套字节可以读写继续等待， 第四个参数为null为阻塞， 为0位非阻塞， 为 >0 为等待时间
    if(socket_select($read, $write, $expect, 0) < 1)
    {
        continue;
    }

    //查看是否有新的连接
    if(in_array($sock, $read))
    {
        $clients[] = $newsock = socket_accept($sock);
        socket_write($newsock, 'there are '.(count($clients) - 1)." clients connected\r\n");
        socket_getpeername($newsock, $ip);
        echo "\nnew client $ip\n";
        $key = array_search($sock, $read);
        unset($read[$key]);
    }

    //便利所有可读取数据套子节然后广播消息
    foreach ($read as $read_sock)
    {
        $data = @socket_read($read_sock, 1024);
        if($data === false)
        {
            $key = array_search($read_sock, $clients);
            socket_getpeername($clients[$key], $ip);
            unset($clients[$key]);
            echo "client $ip disconnected\n";
            continue;
        }
        $data = trim($data);
        if(!empty($data))
        {
            echo $data;
            foreach($clients as $send_sock)
            {
                if($send_sock == $sock || $send_sock == $read_sock)
                {
                    continue;
                }
                socket_write($send_sock, $data);
            }
        }
    }
}

socket_close($sock);