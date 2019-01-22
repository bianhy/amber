<?php

namespace Amber\System;

use Amber\System\Libraries\Input;
use Amber\System\Libraries\Output;

if (!defined('APP_PATH')) {
    throw new \Exception('APP_PATH NOT defined!');
}

class Application
{
    /**
     * 程序开始时间
     * @var mixed
     */
    protected $script_start_time;

    /**
     * 程序结束时间
     * @var mixed
     */
    protected $script_end_time;

    /**
     * 异常处理器
     * @var callable
     */
    protected $exceptions_capture;

    /**
     * 路由器
     * @var
     */
    protected $dispatcher;

    /**
     * controller的命名空间
     * @var
     */
    protected $namespace = 'Application\Controller';

    /**
     * 控制器
     * @var
     */
    protected $controller = null;

    /**
     * 默认方法
     * @var
     */
    protected $method = null;
    

    /**
     * 日志类
     * @var \Monolog\Logger
     */
    public $logger;

    /**
     * 开始前要执行的方法
     * @callable
     */
    protected $before_callback;

    /**
     * 结束后要执行的方法
     * @callable
     */
    protected $end_callback;

    /**
     * 运行环境
     * @var
     */
    protected $environment;

    private $muti_version = null;

    /**
     * @var self
     */
    public static $instance;

    public function __construct($namespace = '', $route = '')
    {

        if (file_exists(SYS_PATH.'Config/'.ENVIRONMENT.'/constant.php')) {
            include_once SYS_PATH.'Config/'.ENVIRONMENT.'/constant.php';
        }

        Helper::load('helpers', true);

        $this->setNamespace($namespace);
        $this->setController();
        $this->setMethod();

        $this->script_start_time = microtime(true);

        if ($route) {
            $this->dispatcher = new Dispatcher($route, $this->controller_namespace);
        }

        $this->setLogger();

        $this->setGlobal();
    }

    public function setGlobal()
    {
        self::$instance = $this;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = ucfirst($namespace);
    }

    protected function setController($controller='')
    {
        $controller || $controller = $this->namespace . '\Controller\IndexController';
        $this->controller = $controller;
    }

    protected function setMethod($method='index')
    {
        $this->method = $method;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setDispatcher($route)
    {
        if (IS_CLI) {
            return true;
        }
        $this->dispatcher = new Dispatcher($route, $this->namespace. '\Controller');
    }

    public function setLogger($logger = null)
    {
        if (is_null($logger)) {
            $logger = Logger::get('system');
        }
        $this->logger = $logger;
    }

    public function setExceptionsCapture(callable $callback)
    {
        $this->exceptions_capture = $callback;
    }

    public function beforeCallback(callable $callback)
    {
        $this->before_callback = $callback;
    }

    public function endCallback(callable $callback)
    {
        $this->end_callback = $callback;
    }

    public function setMutiVersion($version)
    {
        $this->muti_version = $version;
    }

    public function run()
    {
        if (!defined('ENVIRONMENT')) {
            throw new \Exception('ENVIRONMENT not defined');
        }

        $this->logger->debug('start:' . $this->script_start_time);
        if (is_callable($this->before_callback) && $before_callback = $this->before_callback) {
            $before_callback();
        }

        try {
            $route  = $this->dispatch();
            $return = $this->execute($route);
            if ($return) {
                if (IS_CLI) {
                    echo json_encode(['code' => 200, 'data' => $return]);
                } else {
                    Output::json(['code' => 200, 'data' => $return]);
                }
            }
        } catch (\Exception $e) {
            if (is_callable($this->exceptions_capture)) {
                $capture = $this->exceptions_capture;
                $capture($e);
            } else {
                throw new $e;
            }
        }

        if (is_callable($this->end_callback) && $end_callback = $this->end_callback) {
            $end_callback();
        }

        $this->script_end_time = microtime(true);

        $script_use_time = $this->script_end_time - $this->script_start_time;

        if (IS_CLI === false) {
            $log = 'TIMEUSED:' . $script_use_time . '; SERVER_NAME:'. $_SERVER['SERVER_NAME'].';METHOD:' . $_SERVER['REQUEST_METHOD'] . '; URI:' . $_SERVER['REQUEST_URI'];
            if ($script_use_time > 0.1) {
                $this->logger->alert($log);
            } else {
                $this->logger->debug($log);
            }
        }

        $this->logger->debug('end:' . $this->script_end_time);
    }

    public function dispatchDoc($uri) {
        $uri = '/'.ltrim($uri, '/');
        $routeInfo = $this->dispatcher->dispatch('GET', $uri);
        $not_found = false;

        if ($routeInfo[0] != Dispatcher::FOUND) {
            $not_found = true;
        }

        if ($not_found) {
            $routeInfo  = $this->dispatcher->dispatch('POST', $uri);
        }

        return $routeInfo[1];
    }

    private function dispatch()
    {
        if (!$this->dispatcher) {
            if (IS_CLI === false) {
                $controller = ucfirst(Input::string('c'));
                $method     = Input::string('a', null);
            } else {
                set_time_limit(0);
                ini_set('memory_limit', '1024M');
                $opt        = getopt('c:a:d::');
                $tmp        = array_map('ucfirst', explode('/', $opt['c']));
                $controller = implode('\\', $tmp);
                $method     = isset($opt['a']) ? $opt['a'] : null;
            }

            $controller || $controller = $this->namespace.'\Controller\\'.$this->getController();
            $method     || $method     = $this->getMethod();
            return [[$controller, $method], ['vars' => []]];
        } else {
            $routeInfo = $this->dispatcher->dispatch($_SERVER['REQUEST_METHOD'], rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
            $this->logger->debug($_SERVER['REQUEST_METHOD'].', '.rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)).', routeInfo:'.json_encode($routeInfo));
            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                case Dispatcher::METHOD_NOT_ALLOWED:
                    if ($this->controller && $this->method) {
                        $routeInfo[1] = [$this->controller, $this->method];
                        $routeInfo[2] = [];
                    } else {
                        throw new \LogicException("Controller action method doesn't exist.");
                    }
                    break;
                case Dispatcher::FOUND:
                    break;
            }
            return [$routeInfo[1], ['vars' => $routeInfo[2]]];
        }
    }

    private function execute(array $route)
    {
        list($cb, $options) = $route;
        //设置真正执行的controller 和 method
        $this->setController($cb[0]);
        $this->setMethod($cb[1]);

        try {
            $rc = new \ReflectionClass($cb[0]);
        } catch (\Exception $e) {
            throw new \LogicException($e->getMessage(), $e->getCode());
        }

        $controller  = $rc->newInstance();
        $method      = $cb[1];

        if ($controller && !method_exists($controller, $method)) {
            throw new \LogicException("Controller action method '{$method}' doesn't exist.");
        }


        $rps  = $rc->getMethod($method)->getParameters();
        $vars = isset($options['vars']) ? $options['vars'] : [];

        $arguments = [];
        foreach ($rps as $param) {
            $n = $param->getName();
            if (isset($vars[$n])) {
                $arguments[] = $vars[$n];
            } elseif (!$param->isOptional() && !$param->allowsNull()) {
                throw new \LogicException('parameter is not defined.');
            }
        }

        if ($this->muti_version) {
            list($controller, $method) = $this->mutiVersion($controller, $this->getMethod(), $rc->getMethods(), $this->muti_version);
        }

        if (!IS_CLI && defined('ONLY_CLI')) {
            throw new \LogicException('only cli model can visit!');
        }

        return call_user_func_array([$controller, $method], $arguments);
    }


    private function mutiVersion($class_name, $method, $methods, $version)
    {
        $real_version = str_replace('.', '', $version);
        if (file_exists(APP_PATH . 'Configs/multiversion.php')) {
            require_once APP_PATH . 'Config/multiversion.php';
            if (isset($config['multiversion'][$version])) {
                $real_version = $config['multiversion'][$version];
            } else {
                foreach ($config['multiversion'] as $k => $val) {
                    if (version_compare($version, $k) > -1) {
                        $real_version = $val;
                        break;
                    }
                }
            }
        }

        $sames = array($method);
        if ($real_version) {
            foreach ($methods as $row) {
                if ($row->class == get_class($class_name) && strpos($row->name, $method) === 0) {
                    $method_version = intval(str_replace($method, '', $row->name));
                    if ($method_version && $method_version <= $real_version) {
                        $sames[] = $row->name;
                    }
                }
            }
        }
        rsort($sames);
        $method = array_shift($sames);

        return [$class_name, $method];
    }


    public static function setEnvironment($environments = array())
    {
        if (defined('ENVIRONMENT')) {
            return true;
        }

        $environment = 'production';
        foreach ($environments as $key => $hosts) {
            foreach ((array)$hosts as $host) {
                if ($host == gethostname()) {
                    $environment = $key;
                }
            }
        }

        defined('ENVIRONMENT') || define('ENVIRONMENT', $environment);
    }

    protected function getEnvironment()
    {
        return $this->environment;
    }

    public function setErrorReporting($error_reporting = true)
    {
        if ($error_reporting === false) {
            ini_set("display_errors", "off");
            error_reporting(0);
        } else {
            ini_set("display_errors", "on");
            error_reporting(E_ALL);
        }
    }
}
