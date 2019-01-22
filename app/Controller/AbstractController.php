<?php

namespace App\Controller;

use Amber\System\Controller;
use Amber\System\Libraries\Openssl;
use Amber\System\Libraries\User\UserToken;
use App\Model\StartModel;
use App\Model\UsersModel;

class AbstractController extends Controller
{

    /**
     * 使用token作为唯一令牌
     * @var mixed|string $token
     */
    protected $token = '';

    /**
     * 用户id，未登录状态下为0
     * @var int $uid
     */
    protected $uid = 0;

    /**
     * 保存用户基本信息的属性
     * @var array $login_user
     */
    protected $login_user = [];

    /**
     * @var StartModel
     */
    protected $StartModel;

    /**
     * @var UsersModel
     */
    protected $UsersModel;


    public function __construct()
    {
        parent::__construct();
        $this->token = str_replace(' ', '+', $this->getToken('token'));
        $this->setContainerModel();
        $this->setLoginUser($this->token);

    }

    //初始化容器
    private function setContainerModel()
    {
        $this->setProperty('StartModel', function () {
            return new StartModel();
        });

        $this->setProperty('UsersModel', function () {
            return new UsersModel();
        });
    }

    public function setLoginUser($token)
    {
        if (!$token) {
            return false;
        }

        $content = Openssl::decrypt($token);
        if (!$content) {
            return false;
        }
        $info = json_decode($content, true);
        if (!isset($info['uid']) || !isset($info['client_type'])) {
            return false;
        } elseif ($token != UserToken::get($info['uid'], $info['client_type'])) {
            $this->setToken('token', '', -1);//清掉客户端的token
            return false;
        }
        $this->login_user = $this->UsersModel->getUserInfoByUid($info['uid']);
        $this->uid = $info['uid'];

    }

    protected function getToken($key, $default = null)
    {
        if (!isset($_REQUEST[$key])) {
            $val = $default;
            if (isset($_COOKIE[$key])) {
                $val = $_COOKIE[$key];
            }
        } else {
            $val = urldecode($_REQUEST[$key]);
            $this->setToken($key, $val);
        }
        return $val;
    }

    protected function setToken($key, $value, $expire = null)
    {
        header('P3P: CP="NOI DEV PSA PSD IVA PVD OTP OUR OTR IND OTC"');
        setcookie($key, $value, $expire, '/');
    }
}
