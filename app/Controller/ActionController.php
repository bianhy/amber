<?php

namespace App\Controller;

use Amber\System\Libraries\Input;
use Amber\System\Libraries\Strings;
use Amber\System\Libraries\TcpLog;
use Amber\System\Libraries\User\UserCounter;
use Amber\System\Libraries\User\UserPostTime;
use Amber\System\Libraries\User\UserVerifyCode;
use Amber\System\View;

class ActionController extends AbstractController
{

    /**
     * 用户登录接口
     * @desc  支持手机/用户名登录操作
     * @apiparam {"name":"mobile", "type":"string", "desc":"手机号", "require":true}
     * @apiparam {"name":"password","type":"string", "desc":"用户密码，密文", "require":true}
     * @apiparam {"name":"clientType", "type":"string", "desc":"客户端类型，默认 ios，(ios-苹果手机， android -安卓,h5 -html5页面)", "require":true}
     * @apireturn {"name":"token", "type":"string", "desc":"登录token", "require":true}
     * @example   http://www.amber.com/login?mobile=15088888888&password=e10adc3949ba59abbe56e057f20f883e&clientType=h5
     */
    public function login()
    {
        if (!$_POST){
            View::show('action/login');exit;
        }
        $mobile   = Input::string('mobile');
        $password = Input::string('password');

        if (!$mobile) {
            $this->outError('请输入手机号');
        } else if (!$password) {
            $this->outError('请输入密码');
        }

        $user_info = $this->UsersModel->getUserByMobile($mobile);

        if (!$user_info){
            $this->outError("账号或密码错误");
        }
        if ($user_info['password'] != md5($user_info['token'] . '|' . $password)){
            $this->outError("账号密码不匹配");
        }
        //登陆成功重置用户token，更新表里密码
        $this->UsersModel->updatePassword($user_info['uid'], $password);
        $token = $this->genToken($user_info['uid'], $this->clientType);
        $this->outResult(['token' => $token]);
    }

    /**
     * 用户注册接口
     * @desc  仅限手机号码注册操作
     * @apiparam {"name":"mobile", "type":"string", "desc":"用户手机号码", "require":true}
     * @apiparam {"name":"password","type":"string", "desc":"用户密码，密文", "require":true}
     * @apiparam {"name":"clientType", "type":"string", "desc":"客户端类型:默认 ios，(ios-苹果手机， android -安卓,h5 -html5页面)", "require":true}
     * @apiparam {"name":"verifyCode", "type":"string", "desc":"验证码", "require":true}
     * @apireturn {"name":"token", "type":"string", "desc":"登录token", "require":true}
     * @example   http://www.amber.com/register?mobile=15088888888&password=e10adc3949ba59abbe56e057f20f883e&clientType=h5&verifyCode=123456
     */
    public function register()
    {
        $mobile      = Input::int('mobile');
        $verify_Code = Input::string('verifyCode');
        $password    = Input::string('password');

        if (!$mobile || !Strings::isMobile($mobile)) {
            $this->outError('请填写正确的手机号码');
        } elseif (!$password) {
            $this->outError('请输入密码');
        } elseif (!$verify_Code) {
            $this->outError('请输入验证码');
        }

        $msg_code = UserVerifyCode::get($mobile);
        //验证验证码
        if (!$msg_code) {
            $this->outError('验证码已过期，请重新获取');
        } elseif ($verify_Code != $msg_code) {
            $this->outError('验证码错误');
        }

        $user = $this->UsersModel->getUserByMobile($mobile);
        if ($user) {
            $this->outError('手机号已经被注册');
        }

        $uid = $this->AccountsModel->newAccount($mobile, $password);

        $nickname = $this->UsersModel->getNickname('Amber' . substr($mobile, 7));

        //生成用户
        $this->UsersModel->newUser(['uid'=>$uid,'phone'=>$mobile,'nickname'=>$nickname]);
        $token = $this->genToken($uid, $this->clientType);

        $this->outResult(['token' => $token]);
    }

    /**
     * 发送验证码接口
     * @desc  第三方登录发送验证码接口（不需要验证手机号是否存在，老版本的注册和找回密码的验证码也在使用这个接口，所以这个接口不在变动了）
     * @apiparam {"name":"mobile", "type":"string", "desc":"用户手机号码", "require":true}
     * @apiparam {"name":"type", "type":"string", "desc":"获取验证码类型:(1 - 登陆，2 - 注册， 3 - 找回密码或重置密码， 4 - 第三方登陆绑定手机号)", "require":true}
     * @apireturn {"name":"code", "type":"string", "desc":"200", "require":true}
     * @example   http://www.amber.com/user/genVerifyCode?mobile=15088888888&type=2
     */
    public function genVerifyCode()
    {
        $mobile = Input::string('mobile');
        $type   = Input::string('type', [1, 2, 3, 4]);
        if (!$mobile || !Strings::isMobile($mobile)) {
            $this->outError('请填写正确的手机号码');
        }

        $exists_mobile = $this->AccountsModel->getAccountByMobile($mobile);

        //判断 1-登陆或 3 -找回密码
        if (in_array($type, [1, 3]) && !$exists_mobile) {
            $this->outError('手机号不存在');
        }

        // 2 - 手机号注册, 4 - 第三方登陆绑定手机号
        if (in_array($type, [2, 4]) && $exists_mobile) {
            $this->outError('手机号已经存在');
        }

        UserPostTime::record($mobile, 60, 'sms_code');
        //验证此手机号是否超过当天请求上限
        if (UserCounter::add($mobile, 'sms_code') > 10) {
            $this->outError('已超过当天获取次数上限');
        }

        $verify_code = Strings::randString();
        if (!Sms::send($mobile, $verify_code)) {
            $this->outError('验证码发送失败');
        }
        //把验证码计入到redis
        UserVerifyCode::set($mobile, $verify_code, 1800);
        //记录发送日志
        TcpLog::record('user/genVerifyCode', json_encode(['mobile' => $mobile, 'verifyCode' => $verify_code]));
        $this->outResult(true);
    }

    /**
     * 重置密码接口
     * @desc  重置用户登录密码
     * @apiparam {"name":"mobile", "type":"string", "desc":"用户手机号码", "require":true}
     * @apiparam {"name":"verifyCode", "type":"string", "desc":"验证码", "require":true}
     * @apiparam {"name":"password","type":"string", "desc":"用户密码，密文", "require":true}
     * @apireturn {"name":"code", "type":"200", "desc":"操作成功", "require":true}
     * @example   http://www.amber.com/user/resetPassword?mobile=15083389023&password=e10adc3949ba59abbe56e057f20f883e&verifyCode=123456
     */
    public function resetPassword()
    {
        $mobile      = Input::string('mobile');
        $verify_Code = Input::string('verifyCode');
        $password    = Input::string('password');

        if (!$mobile || !Strings::isMobile($mobile)) {
            $this->outError('请填写正确的手机号码');
        }
        if (!$verify_Code) {
            $this->outError('请输入验证码');
        }
        if (!UserVerifyCode::get($mobile)) {
            $this->outError('验证码已过期，请重新获取');
        }
        if ($verify_Code != UserVerifyCode::get($mobile)) {
            $this->outError('验证码错误');
        }

        if (!$password) {
            $this->outError('请输入新密码');
        }

        //验证是否存在相同手机号码的用户
        $user_info = $this->AccountsModel->getAccountByMobile($mobile);
        if (!$user_info) {
            $this->outError("用户不存在");
        }

        if ($this->UsersModel->updatePassword($user_info['uid'], ['password' => $password]) === false) {
            $this->outError('操作失败');
        }

        $this->outResult(['msg' => 'true']);
    }
}
