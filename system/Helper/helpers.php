<?php

if (!function_exists('json')) {
    function json($data) {
        echo json_encode($data);
    }
}

/**
 * 获取用户真实ip地址
 * @return string
 */
function getClientIp()
{
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $uiClientIp = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $uiClientIp = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $uiClientIp = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $uiClientIp = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $uiClientIp = getenv("HTTP_CLIENT_IP");
        } else {
            $uiClientIp = getenv("REMOTE_ADDR");
        }
    }
    return $uiClientIp;
}

