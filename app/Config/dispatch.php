<?php

//路由规则
return [
    ['ANY', '/start',  ['StartController', 'banner']],
    ['ANY', '/s',      ['Script\TestController', 'index']],
    ['ANY', '/time',   ['TestController', 'time']],
    ['GET', '/home/{id:\d+}', ['HomeController', 'index']],
    ['GET', '/login',  ['ActionController', 'login']],
];
