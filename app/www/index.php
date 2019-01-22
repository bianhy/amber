<?php
require dirname(__DIR__) . '/Config/bootstrap.php';
require dirname(__DIR__) . '/Config/constant.php';
\Amber\System\Helper::load('func');
\Amber\System\Helper::load('array');
\Amber\System\Application::setEnvironment([
    'localhost'   => ['frame-local'],
    'development' => ['frame-dev', 'frame.dev'],
    'testing'     => ['frame-testing', 'frame-test'],
]);

$app = new \Amber\System\Application('App');
$app->setErrorReporting(IS_DEBUG || ENVIRONMENT != 'production');
$app->setDispatcher(\Amber\System\Libraries\Config::get('dispatch'));
$app->setExceptionsCapture(function (Exception $e) {
    \Amber\System\Libraries\Exceptions::capture($e);
});

$app->run();
register_shutdown_function(function () {
    //\Amber\System\Libraries\TcpLog::save('amber.frame');
});

