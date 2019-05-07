<?php
//设置不超时
set_time_limit(0);

class SocketServer
{
    public function __construct($port)
    {
        global $errno, $errstr;

        $socket = stream_socket_server('tcp://127.0.0.1:'.$port, $errno, $errstr);
        while($conn = stream_socket_accept($socket, -1))
        {
            $buff = '';
            $data = '';

            //读取请求数据直到遇到\r\n结束符
            while(!preg_match('#\r\n#', $buff))
            {
                $buff = fread($conn, 1024);
                $data .= preg_replace('#\r\n#', '', $buff);
            }
            fwrite($conn, $data);
            fclose($conn);
        }
        fclose($socket);
    }
}

new SocketServer(8000);