<?php

namespace Amber\System\Libraries\Upload\Driver;

class Curl
{
    /**
     * 上传文件根目录
     * @var string
     */
    private $rootPath;

    /**
     * 上传错误信息
     * @var string
     */
    private $errorStr = '2';

    /**
     * 构造函数，用于设置上传根路径
     * @param array $config FTP配置
     */
    public function __construct($config)
    {
        $this->config = null;
        /* 设置根目录 */
        $this->curl = new CurlUpload($config);
    }

    /**
     * 检测上传根目录(七牛上传时支持自动创建目录，直接返回)
     * @param string $rootpath 根目录
     * @return boolean true-检测通过，false-检测失败
     */
    public function checkRootPath($rootpath)
    {
        $this->rootPath = trim($rootpath, './') . '/';
        return true;
    }

    /**
     * 检测上传目录(七牛上传时支持自动创建目录，直接返回)
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkSavePath($savepath)
    {
        return true;
    }

    /**
     * 创建文件夹 (七牛上传时支持自动创建目录，直接返回)
     * @param  string $savepath 目录名称
     * @return boolean          true-创建成功，false-创建失败
     */
    public function mkdir($savepath)
    {
        return true;
    }

    /**
     * 保存指定文件
     * @param  array $file 保存的文件信息
     * @return boolean          保存状态，true-成功，false-失败
     */
    public function save(&$file)
    {
        $upfile = array(
            'savepath' => $file['savepath'],
            'savename' => $file['savename'],
            'file'     => $file['tmp_name'],
        );
        $result = $this->curl->upload($upfile);
        if (!$result) {
            $file = false;
        } else {
            $file['path'] = $file['savepath'] . $file['savename'];
        }
        return !$result ? false : true;
    }

    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError()
    {
        return $this->errorStr;
    }
}


class CurlUpload
{
    protected $url = null;

    public function __construct($config)
    {
        if (!isset($config['url'])) {
            throw new \Exception('the config[\'url\'] is empty');
        }

        $this->url = $config['url'];
    }


    public function upload($data)
    {
        $curl      = curl_init();
        $post_data = array(
            'savepath' => $data['savepath'],
            'savename' => $data['savename'],
            'upload'   => "@" . $data['file'],
        );
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
        $result = curl_exec($curl);
        $error  = curl_error($curl);
        return $error ? false : $result;
    }
}
