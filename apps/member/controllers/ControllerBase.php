<?php

namespace App\Member\Controllers;

use Respect\Validation\Validator as v;

class ControllerBase extends \App\Common\Controllers\ControllerBase
{

    protected $memberInfo;

    protected $modelConsignee = null;

    protected $modelPointsUser = null;
    protected $modelPointsService = null;

    protected $modelPointsRule = null;

    protected $modelMember = null;

    // 是否需要检查token
    protected $is_need_check_token = false;
    // 是否需要检查captcha
    protected $is_need_check_captcha = true;

    protected $errors = array();

    protected function initialize()
    {
        parent::initialize();

        $this->modelConsignee = new \App\Member\Models\Consignee();
        $this->modelPointsUser = new \App\Points\Models\User();
        $this->modelPointsRule = new \App\Points\Models\Rule();
        $this->modelMember = new \App\Member\Models\Member();
        $this->modelPointsService = new \App\Points\Service\Api();

        $this->errors = $this->getDI()->get('errors');
        $this->view->setVar("resourceUrl", "/member/");
        // token
        $formhash = $this->getToken();
        $this->assign('formhash', $formhash);

        if (!in_array($this->controllerName, array(
            'passport',
            'service'
        ))) {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            $this->doLogin($isLogin);

            // 获取会员信息
            $this->memberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            $isLogin = empty($this->memberInfo) || empty($this->memberInfo['state']) ? false : true;
            $this->doLogin($isLogin);
            $this->assign('memberInfo', $this->memberInfo);
        }
    }

    protected function getRefererUrl()
    {
        $ref_url = $this->get('ref_url', '');
        if (empty($ref_url)) {
            $ref_url = getReferer();
        }
        $this->assign('ref_url', $ref_url);
        return $ref_url;
    }

    protected function getToken()
    {
        $token = encrypt(TIMESTAMP, md5(MD5_KEY));
        return $token;
    }

    /**
     * 检查Token
     *
     * @return boolean
     */
    protected function validateToken()
    {
        if ($this->is_need_check_token) {
            $formhash = trim($this->get('formhash', '')); // formhash
            $data = decrypt($formhash, md5(MD5_KEY));
            $isOk = $data && (TIMESTAMP - $data < 5400);
        } else {
            $isOk = true;
        }
        if (!$isOk) {
            return $this->errors['e597'];
        }
        return $this->errors['none'];
    }

    /**
     * 检查验证码
     *
     * @return array
     */
    protected function validateCaptcha()
    {
        if ($this->is_need_check_captcha) {
            $captcha = trim($this->get('captcha', '')); // captcha
            $image = new \Securimage();
            if ($image->check($captcha) == true) {
                $isOk = true;
            } else {
                $isOk = false;
            }
        } else {
            $isOk = true;
        }
        if (!$isOk) {
            return $this->errors['e597'];
        }
        return $this->errors['none'];
    }

    /**
     * 检查操作是否已锁定
     *
     * @return array
     */
    protected function lock(\iLock $objLock)
    {
        $isLock = $objLock->lock();
        if ($isLock) {
            return $this->errors['e599'];
        }
        return $this->errors['none'];
    }

    protected function doLogin($isLogin)
    {
        if (!$isLogin) {
            if (!$this->getRequest()->isAjax()) {
                $loginUrl = $this->url->get("member/passport/login");
                $this->_redirect($loginUrl);
                exit();
            } else {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
        }
    }

    // -----------------------------------------------------检查----------------------------------------------------------------
    protected function validateAccount($name, $email, $mobile)
    {
        if (empty($name) && empty($mobile) && empty($email)) {
            return $this->errors['e596'];
        }
        return $this->errors['none'];
    }

    protected function validateName($name)
    {
        $nameValidator = v::notEmpty()->noWhitespace();
        $isOk = $nameValidator->validate($name);
        if (!$isOk) {
            return $this->errors['e501'];
        }

        return $this->errors['none'];
    }

    protected function validatePassword($password, $password_confirm = '')
    {
        $pwdValidator = v::notEmpty();
        $isOk = $pwdValidator->validate($password);
        if (!$isOk) {
            return $this->errors['e503'];
        }

        if (!empty($password_confirm)) {
            $isOk = $pwdValidator->validate($password_confirm);
            if (!$isOk) {
                return $this->errors['e504'];
            }
            if ($password_confirm != $password) {
                return $this->errors['e505'];
            }
        }
        return $this->errors['none'];
    }

    protected function checkNameIsExist($name)
    {
        // 验证用户名是否重复
        $checkMemberInfo = $this->modelMember->getInfoByName($name);
        if ($checkMemberInfo && !empty($checkMemberInfo['state'])) {
            return $this->errors['e502'];
        }
        $ret = $this->errors['none'];
        $ret['result'] = $checkMemberInfo;
        return $ret;
    }

    protected function checkEmailIsExist($email)
    {
        // 验证邮箱地址是否重复
        $checkMemberInfo = $this->modelMember->getInfoByEmail($email);
        if ($checkMemberInfo && !empty($checkMemberInfo['state'])) {
            return $this->errors['e507'];
        }
        $ret = $this->errors['none'];
        $ret['result'] = $checkMemberInfo;
        return $ret;
    }

    protected function checkMobileIsExist($mobile)
    {
        // 验证手机号是否重复
        $checkMemberInfo = $this->modelMember->getInfoByMobile($mobile);
        if ($checkMemberInfo && !empty($checkMemberInfo['state'])) {
            return $this->errors['e509'];
        }
        $ret = $this->errors['none'];
        $ret['result'] = $checkMemberInfo;
        return $ret;
    }

    protected function weixinauthorize()
    {
        $userInfo = empty($_SESSION['Weixin_userInfo']) ? array() : $_SESSION['Weixin_userInfo'];
        if (!empty($userInfo)) {
            $openid = $userInfo['user_id'];
            $nickname = $userInfo['user_name'];
            $headimgurl = $userInfo['user_headimgurl'];

            // 加锁
            $key = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $openid);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                $this->refreshPage(5);
            }
            // 获取
            $userInfo = $this->modelMember->getInfoByWeixinOpenid($openid);
            if (!empty($userInfo)) {
                // 登录处理
                $this->modelMember->login($userInfo);
                // 跳转地址
                $callbackUrl = $this->url->get("yungou/index/index");
                $this->_redirect($callbackUrl);
                exit();
            }
        }
    }

    /**
     * 微信授权之后的页面
     */
    public function weixinauthorizeAction()
    {
        try {
            // http://www./member/xxxx/weixinauthorize?callbackUrl=xxx
            $callbackUrl = trim($this->get('callbackUrl', ''));

            $FromUserName = trim($this->get('FromUserName', ''));
            $nickname = trim($this->get('nickname', ''));
            $headimgurl = trim($this->get('headimgurl', ''));
            $timestamp = trim($this->get('timestamp', ''));
            $signkey = trim($this->get('signkey', ''));

            // url的参数上已经有了FromUserName参数并且不是空的时候
            if (!empty($FromUserName)) {
                $config = $this->getDI()->get('config');
                $secretKey = $config['weixinAuthorize']['secretKey'];
                // 校验微信id,上线测试时需要加上去
                if ($this->validateOpenid($FromUserName, $timestamp, $secretKey, $signkey)) {
                    // 授权处理完成
                    unset($_SESSION['isWeixinAuthorizing']);
                    // 存储微信用户到session
                    $userInfo = array(
                        'user_id' => $FromUserName,
                        'user_name' => urldecode($nickname),
                        'user_headimgurl' => urldecode($headimgurl),
                        'subscribe' => 0
                    );
                    // 存储微信id到session
                    $_SESSION['Weixin_userInfo'] = $userInfo;
                    $_SESSION['login_from'] = 'weixin';

                    // 授权成功之后的处理
                    $this->weixinauthorize();
                }
            }

            // 跳转地址
            if (empty($callbackUrl)) {
                $callbackUrl = $this->url->get("member/passport/qcbind");
            }

            $this->_redirect($callbackUrl);
            exit();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // 获取用户信息
    protected function getWeixinUserInfo()
    {
        $userInfo = empty($_SESSION['Weixin_userInfo']) ? array() : $_SESSION['Weixin_userInfo'];
        if (!empty($userInfo)) {
            $this->assign('user_id', $userInfo['user_id']);
            $this->assign('user_name', $userInfo['user_name']);
            $this->assign('user_headimgurl', str_replace('/0', '/64', $userInfo['user_headimgurl']));
            return $userInfo;
        } else {
            // 不是接口调用的话
            return array();
        }
    }

    protected function tencentauthorize()
    {
        $userInfo = empty($_SESSION['Tencent_userInfo']) ? array() : $_SESSION['Tencent_userInfo'];
        if (!empty($userInfo)) {
            $openid = $userInfo['user_id'];
            $nickname = $userInfo['user_name'];
            $headimgurl = $userInfo['user_headimgurl'];

            // 加锁
            $key = \App\Common\Utils\Helper::myCacheKey(__CLASS__, $openid);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                $this->refreshPage(5);
            }
            // 获取
            $userInfo = $this->modelMember->getInfoByQQOpenid($openid);
            if (!empty($userInfo)) {
                // 登录处理
                $this->modelMember->login($userInfo);
                // 跳转地址
                $callbackUrl = $this->url->get("yungou/index/index");
                $this->_redirect($callbackUrl);
                exit();
            }
        }
    }

    /**
     * QQ授权页面
     */
    public function tencentauthorizeAction()
    {
        try {
            // http://www./member/xxxx/tencentauthorize?callbackUrl=xxx
            $callbackUrl = trim($this->get('callbackUrl', ''));

            $FromUserName = trim($this->get('FromUserName', ''));
            $nickname = trim($this->get('nickname', ''));
            $headimgurl = trim($this->get('headimgurl', ''));
            $timestamp = trim($this->get('timestamp', ''));
            $signkey = trim($this->get('signkey', ''));

            // url的参数上已经有了FromUserName参数并且不是空的时候
            if (!empty($FromUserName)) {
                $config = $this->getDI()->get('config');
                $secretKey = $config['tencentAuthorize']['secretKey'];
                // 校验微信id,上线测试时需要加上去
                if ($this->validateOpenid($FromUserName, $timestamp, $secretKey, $signkey)) {
                    // 授权处理完成
                    unset($_SESSION['isTencentAuthorizing']);
                    // 存储微信用户到session
                    $userInfo = array(
                        'user_id' => $FromUserName,
                        'user_name' => urldecode($nickname),
                        'user_headimgurl' => urldecode($headimgurl),
                        'subscribe' => 0
                    );
                    // 存储微信id到session
                    $_SESSION['Tencent_userInfo'] = $userInfo;
                    $_SESSION['login_from'] = 'tencent';
                    // 授权成功之后的处理
                    $this->tencentauthorize();
                }
            }

            // 跳转地址
            if (empty($callbackUrl)) {
                $callbackUrl = $this->url->get("member/passport/qcbind");
            }

            $this->_redirect($callbackUrl);
            exit();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    // 获取用户信息
    protected function getTencentUserInfo()
    {
        $userInfo = empty($_SESSION['Tencent_userInfo']) ? array() : $_SESSION['Tencent_userInfo'];
        if (!empty($userInfo)) {
            $this->assign('FromUserName', $userInfo['user_id']);
            $this->assign('nickname', $userInfo['user_name']);
            $this->assign('headimgurl', str_replace('/0', '/64', $userInfo['user_headimgurl']));
            return $userInfo;
        } else {
            // 不是接口调用的话
            if (!$this->getRequest()->isAjax()) {
                unset($_SESSION['isTencentAuthorizing']);
                unset($_SESSION['Tencent_userInfo']);
                $this->refreshPage(5);
            } else {
                return array();
            }
        }
    }
}
