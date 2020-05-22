<?php

namespace App\Service\Controllers;

class VcodeController extends ControllerBase
{

    private $smsSettings = null;

    private $messageTemplate = null;

    private $mailSettings = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->messageTemplate = new \App\Message\Models\Template();
        $this->mailSettings = new \App\Mail\Models\Settings();
        $this->smsSettings = new \App\Sms\Models\Settings();
    }

    /**
     * 提供发送邮件验证码的服务
     */
    public function sendcodeemailAction()
    {
        // http://www.applicationmodule.com/service/vcode/sendcodeemail?userEmail=handsomegyr@hotmail.com
        try {
            $userEmail = $this->get('userEmail', '');
            if (empty($userEmail)) {
                echo ($this->error(-1, '邮箱为空'));
                return false;
            }

            // if (isset($_SESSION['vcode']) && (time() < $_SESSION['vcode']['vcode_expire_time'])) {
            // echo ($this->error(- 99, '请求过于频繁,请稍后再试'));
            // return false;
            // }

            $captcha = trim($this->get('captcha', '')); // captcha
            if (!empty($captcha)) {
                $image = new \Securimage();
                if ($image->check($captcha) == true) {
                    // echo "Correct!";
                } else {
                    echo ($this->error(-3, "验证码不正确"));
                    return false;
                }
            } else {
                // 该手机号一天n次的话,就出现验证码
                $isCaptchaNeed = $this->isCaptchaNeed($userEmail, 10);
                if (!empty($isCaptchaNeed)) {
                    echo ($this->error(-4, "请输入验证码"));
                    return false;
                }

                // 记录调用次数
                $key = $this->getCacheKey($userEmail);
                $cache = $this->getDI()->get("cache");
                $count = $cache->get($key);
                if (empty($count)) {
                    $count = 0;
                }
                $cache->save($key, $count + 1, 60 * 60 * 24); // 24小时
            }

            // 发送验证码处理
            $vcode = createRandNumber10();
            $vcode = substr($vcode, 0, 6);
            $tpl_info = $this->messageTemplate->getValidateEmailTemplate($userEmail, $vcode);
            // $message = "【家宝网站】您的验证码是4567";
            $ret = $this->mailSettings->sendEmail($userEmail, $tpl_info['subject'], $tpl_info['content']);
            echo $this->result('OK');
            fastcgi_finish_request();

            $now = time();
            $_SESSION['vcode'] = array(
                'vkey' => $userEmail,
                'vcode' => $vcode,
                'expire_time' => ($now + 20 * 60),
                'vcode_expire_time' => ($now + 60)
            );
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 输入手机号码,发送验证码的接口
     */
    public function sendcodesmsAction()
    {
        // http://www.applicationmodule.com/service/vcode/sendcodesms?mobile=13564100096&captcha=xxx
        try {
            $mobile = $this->get('mobile', '');
            if (empty($mobile)) {
                echo ($this->error(-1, '手机号的值为空'));
                return false;
            }

            if (!isValidMobile($mobile)) {
                echo ($this->error(-2, '手机号的格式不正确'));
                return false;
            }

            // if (isset($_SESSION['vcode']) && (time() < $_SESSION['vcode']['vcode_expire_time'])) {
            // echo ($this->error(- 99, '请求过于频繁,请稍后再试'));
            // return false;
            // }

            $captcha = trim($this->get('captcha', '')); // captcha
            if (!empty($captcha)) {
                $image = new \Securimage();
                if ($image->check($captcha) == true) {
                    // echo "Correct!";
                } else {
                    echo ($this->error(-3, "验证码不正确"));
                    return false;
                }
            } else {
                // 该手机号一天n次的话,就出现验证码
                $isCaptchaNeed = $this->isCaptchaNeed($mobile, 10);
                if (!empty($isCaptchaNeed)) {
                    echo ($this->error(-4, "请输入验证码"));
                    return false;
                }

                // 记录调用次数
                $key = $this->getCacheKey($mobile);
                $cache = $this->getDI()->get("cache");
                $count = $cache->get($key);
                if (empty($count)) {
                    $count = 0;
                }
                $cache->save($key, $count + 1, 60 * 60 * 24); // 24小时
            }

            // 发送验证码处理
            $vcode = createRandNumber10();
            $vcode = substr($vcode, 0, 6);
            $tpl_info = $this->messageTemplate->getValidateMobileTemplate($mobile, $vcode);
            // $message = "【家宝网站】您的验证码是4567";
            $ret = $this->smsSettings->sendSms($mobile, $tpl_info['subject'], $tpl_info['content']);
            echo $this->result('OK');
            fastcgi_finish_request();

            $now = time();
            $_SESSION['vcode'] = array(
                'vkey' => $mobile,
                'vcode' => $vcode,
                'expire_time' => ($now + 20 * 60),
                'vcode_expire_time' => ($now + 60)
            );
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 验证码验证的服务
     */
    public function verifyemailsnAction()
    {
        // http://www.applicationmodule.com/service/vcode/verifyemailsn?userEmail=handsomegyr@hotmail.com&sn=434343
        try {
            $sn = $this->get('sn', '');
            $userEmail = $this->get('userEmail', '');

            // 验证码检查
            $validateRet = $this->validateVcode($sn, $userEmail);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            echo $this->result('OK');
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 验证 验证码的接口
     */
    public function verifymobilesnAction()
    {
        // http://www.applicationmodule.com/service/vcode/verifymobilesn?sn=xxxx&mobile=xxxx
        try {
            $sn = $this->get('sn', '');
            $mobile = $this->get('mobile', '');

            // 验证码检查
            $validateRet = $this->validateVcode($sn, $mobile);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            echo $this->result('OK');
            return true;
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    private function isCaptchaNeed($key, $num = 3)
    {
        return false;
        // 一个手机号的次数超过$num的话就出现验证码
        $key = $this->getCacheKey($key);
        $cache = $this->getDI()->get("cache");
        $count = $cache->get($key);
        $isCaptchaNeed = ($count >= $num);

        return $isCaptchaNeed;
    }

    private function getCacheKey($key)
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, date('Ymd'), $key);
        return $key;
    }
}
