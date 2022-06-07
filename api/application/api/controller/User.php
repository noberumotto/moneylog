<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use fast\Random;
use think\Config;
use think\Validate;

/**
 * 会员接口
 */
class User extends Api
{
    protected $noNeedLogin = ['login',  'register', 'third'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
        $this->auth->setAllowFields(['email']);
    }


    /**
     * 会员登录
     *
     * @ApiMethod (POST)
     * @param string $account  账号
     * @param string $password 密码
     */
    public function login()
    {
        $email = $this->request->post('email');
        $password = $this->request->post('password');
        if (!$email || !$password) {
            $this->error(__('请输入正确的邮箱和密码'));
        }
        $ret = $this->auth->login($email, $password);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }



    /**
     * 注册会员
     *
     * @ApiMethod (POST)
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email    邮箱
     * @param string $mobile   手机号
     * @param string $code     验证码
     */
    public function register()
    {
        $email = $this->request->post('email');
        $password = $this->request->post('password');
        $code = $this->request->post('code');


        if (!$email || !$password || !$code) {
            $this->error(__('Invalid parameters'));
        }
        if ($email && !Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }


        $ret = Ems::check($email, $code, "register");
        if (!$ret) {
            $this->error(__('验证码错误'));
        }

        $ret = $this->auth->register($email, $password);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Sign up successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 退出登录
     * @ApiMethod (POST)
     */
    public function logout()
    {
        if (!$this->request->isPost()) {
            $this->error(__('Invalid parameters'));
        }
        //  清除缓存
        cache('account_list_' . $this->auth->id, null);
        cache('tags_list_' . $this->auth->id, null);

        $this->auth->logout();
        $this->success(__('Logout successful'));
    }

    /**
     * 重置密码
     *
     * @ApiMethod (POST)
     * @param string $mobile      手机号
     * @param string $newpassword 新密码
     * @param string $captcha     验证码
     */
    public function resetpwd()
    {
        $oldpassword = $this->request->post("oldPassword");

        $newpassword = $this->request->post("newPassword");

        //验证Token
        if (!Validate::make()->check(['newpassword' => $newpassword], ['newpassword' => 'require|regex:\S{6,30}'])) {
            $this->error(__('Password must be 6 to 30 characters'));
        }

        if ($this->auth->password != $this->auth->getEncryptPassword($oldpassword, $this->auth->salt)) {
            $this->error('');
        }



        $ret = $this->auth->changepwd($newpassword, '', true);


        if ($ret) {
            //  登录
            $this->auth->direct($this->auth->id);

            $this->success(__('Reset password successful'), $this->auth->getToken());
        } else {
            $this->error($this->auth->getError());
        }
    }
}
