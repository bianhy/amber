<?php
//设置脚本运行时间不限制
set_time_limit(0);
$server_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($server_socket, '127.0.0.1', '8000');
socket_listen($server_socket, 4);
//设置非阻塞模式
socket_set_nonblock($server_socket);
do{
//当有连接时读入数据，并返回
$msg_socket = socket_accept($server_socket);
if($msg_socket)
{
$buff = socket_read($msg_socket, 1024);
echo "client: ".$buff;
socket_write($msg_socket, $buff);
socket_close($msg_socket);
}
}while(true);

socket_close($server_socket);