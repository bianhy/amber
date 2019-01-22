<?php
namespace Amber\System\Libraries\Http;

use Amber\System\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Response;

class Request
{
    protected $options;
    /**
     * @var Client
     */
    protected $client;

    /**
     * 最多重试几次
     * @var int
     */
    protected $max_retries = 3;

    protected $logger;

    protected static $instance;


    public function __construct()
    {
        $this->options['connect_timeout'] = 1;
        $this->options['timeout']         = 1;
        $this->client                     = new Client($this->options);
        $this->logger                     = Logger::get('curl');
    }

    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $method
     * @param $uri
     * @param $option
     * @return Response
     */
    public function send($method, $uri, $option)
    {
        $this->logger->debug($method . ":" . $uri . ":" . json_encode($option));
        $start    = microtime(true);
        $response = false;
        for ($i = 0; $i < $this->max_retries; $i++) {
            try {
                $response = $this->client->$method($uri, $option);
                break;
            } catch (RequestException $e) {
                if ($i == 2) {
                    $this->logger->error('request:' . str_replace("\r\n", ";", $e->getRequest()) . ', message:' . $e->getMessage());
                    throw new RequestException($e->getMessage(), $e->getRequest(), $e->getResponse());
                }
            }
            usleep(100000);
        }
        $use = microtime(true) - $start;
        if ($use > 0.3) {
            $this->logger->alert($method . ":" . $uri . ":" . json_encode($option) . ', time:' . $use);
        }
        return $response;
    }

    public static function init($options = [])
    {
        $class = get_called_class();
        return  new $class($options);
    }

    /**
     * @param $uri
     * @param array $option
     * @return Response
     */
    public static function get($uri, $option = [])
    {
        return self::init($option)->send('get', $uri, $option)->json();
    }

    /**
     * @param $uri
     * @param array $option
     * @param array $body
     * @return Response
     */
    public static function put($uri, $body = [], $option = [])
    {
        if ($body) {
            $option['body'] = $body;
        }
        return self::init($option)->send('put', $uri, $option)->json();
    }

    /**
     * @param $uri
     * @param array $body
     * @param array $option
     * @return Response
     */
    public static function post($uri, $body = [], $option = [])
    {
        if ($body) {
            $option['body'] = $body;
        }
        return self::init($option)->send('post', $uri, $option)->json();
    }

    /**
     * @param $uri
     * @param array $option
     * @return Response
     */
    public static function delete($uri, $option = [])
    {
        return self::init($option)->send('delete', $uri, $option)->json();
    }

    /**
     * @param $uri
     * @param array $body
     * @param array $option
     * @return Response
     */
    public static function patch($uri, $body = [], $option = [])
    {
        if ($body) {
            $option['body'] = $body;
        }
        return self::init($option)->send('patch', $uri, $option)->json();
    }

    /**
     * @param $method
     * @param $uri
     * @param array $body
     * @param array $option
     * @return string
     */
    public static function string($method, $uri, $body = [], $option = [])
    {
        if ($body) {
            $option['body'] = $body;
        }
        return (String)self::init($option)->send($method, $uri, $option)->getBody();
    }
}
