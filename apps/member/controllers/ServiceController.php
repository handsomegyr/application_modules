<?php

namespace App\Member\Controllers;

use Imagine\Image\Box;
use Imagine\Image\Point;

/**
 * 用户登录注册
 *
 * @author Kan
 *        
 */
class ServiceController extends ControllerBase
{

    protected $modelMemberVisitor = null;

    protected $modelMemberReport = null;

    protected $modelMemberFriend = null;

    protected $modelMemberGrade = null;

    protected $modelInvitation = null;

    protected $modelInvitationGotDetail = null;

    protected $modelInvitationUser = null;

    protected $modelSysMsg = null;

    protected $modelMsgCount = null;

    protected $serviceCart = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelMemberReport = new \App\Member\Models\Report();
        $this->modelMemberVisitor = new \App\Member\Models\Visitor();
        $this->modelMemberFriend = new \App\Member\Models\Friend();
        $this->modelMemberGrade = new \App\Member\Models\Grade();
        $this->modelInvitation = new \App\Invitation\Models\Invitation();
        $this->modelInvitation->setIsExclusive(false);
        $this->modelInvitationGotDetail = new \App\Invitation\Models\InvitationGotDetail();
        $this->modelInvitationUser = new \App\Invitation\Models\User();
        $this->modelInvitationUser->setIsExclusive(false);
        $this->modelSysMsg = new \App\Message\Models\SysMsg();
        $this->modelMsgCount = new \App\Message\Models\MsgCount();
        $this->serviceCart = new \App\Order\Services\Cart();
    }

    /**
     * 会员名称检测的接口
     */
    public function checknameAction()
    {
        // http://www.applicationmodule.com/member/service/checkname?username=xxx
        try {
            $username = $this->get('username', '');

            // 用户名检查
            $validateRet = $this->validateName($username);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 验证用户名是否已注册
            $validateRet = $this->checkNameIsExist($username);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 电子邮箱地址检测的接口
     */
    public function checkemailAction()
    {
        // http://www.applicationmodule.com/member/service/checkemail?email=xxx
        try {
            $email = $this->get('email', '');

            // 邮箱地址检查
            $validateRet = $this->validateEmail($email);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 验证邮箱地址是否已注册
            $validateRet = $this->checkEmailIsExist($email);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 手机号码检测的接口
     */
    public function checkmobileAction()
    {
        // http://www.applicationmodule.com/member/service/checkmobile?mobile=1356410096
        try {
            $mobile = $this->get('mobile', '');

            // 手机号检查
            $validateRet = $this->validateMobile($mobile);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 验证手机号码是否已注册
            $validateRet = $this->checkMobileIsExist($mobile);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取会员登录信息的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function logininfoAction()
    {
        // http://www.applicationmodule.com/member/service/logininfo
        try {
            if (!empty($_SESSION['member_id'])) {
                $memberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            } else {
                $memberInfo = array();
            }
            if (empty($memberInfo)) {
                echo ($this->error(-1, '未登录'));
                return false;
            } else {
                // 返回结果
                $ret = array();
                $ret['userID'] = $memberInfo['_id'];
                $ret['userPhoto'] = $this->modelMember->getImagePath($this->baseUrl, $memberInfo['avatar']);
                $ret['userWeb'] = $memberInfo['_id'];
                $ret['username'] = $this->modelMember->getRegisterName($memberInfo);
                echo ($this->result("OK", $ret));
                return true;
            }
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 会员登录的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function userloginAction()
    {
        // http://www.applicationmodule.com/member/service/userlogin?username=xxx&mobile=xxx&password=xxx&email=xxx
        try {
            $this->is_need_check_captcha = false;
            $username = $this->get('username', '');
            $mobile = $this->get('mobile', '');
            $email = $this->get('email', '');
            $password = $this->get('password', '');

            // 帐号检查
            $validateRet = $this->validateAccount($username, $mobile, $email);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 姓名检查
            if (!empty($username)) {
                $validateRet = $this->validateName($username);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }
            // Email检查
            if (!empty($email)) {
                $validateRet = $this->validateEmail($email);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // 手机号检查
            if (!empty($mobile)) {
                $validateRet = $this->validateMobile($mobile);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // 密码检查
            $validateRet = $this->validatePassword($password);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查Token
            $validateRet = $this->validateToken();
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查验证码
            $validateRet = $this->validateCaptcha();
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查操作是否已锁定
            $objLock = new \iLock('login');
            $validateRet = $this->lock($objLock);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查数据库是否有相应的数据
            // 用户名
            if (!empty($username)) {
                $memberInfo = $this->modelMember->getInfoByName($username);
            }

            // email
            elseif (!empty($email)) {
                $memberInfo = $this->modelMember->getInfoByEmail($email);
            }

            // mobile
            elseif (!empty($mobile)) {
                $memberInfo = $this->modelMember->getInfoByMobile($mobile);
            }

            if (empty($memberInfo) || empty($memberInfo['state'])) {
                $errorInfo = $this->errors['e510'];
                echo ($this->error($errorInfo['error_code'], $errorInfo['error_msg']));
                return false;
            } else {
                if ($memberInfo['passwd'] != md5($password)) {
                    $errorInfo = $this->errors['e510'];
                    echo ($this->error($errorInfo['error_code'], $errorInfo['error_msg']));
                    return false;
                }

                if (!$memberInfo['state']) {
                    $errorInfo = $this->errors['e511'];
                    echo ($this->error($errorInfo['error_code'], $errorInfo['error_msg']));
                    return false;
                }
            }

            // 登录处理
            $this->loginMember($memberInfo);

            // 返回结果
            echo ($this->result("登录成功"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 会员注册的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function saveregisterAction()
    {
        // http://www.applicationmodule.com/member/service/saveregister?username=xxx&mobile=xxx&password=xxx&password_confirm=xxx&email=xxx&vcode=xxx
        try {
            $username = $this->get('username', '');
            $mobile = $this->get('mobile', '');
            $email = $this->get('email', '');
            $password = $this->get('password', '');
            $password_confirm = $this->get('password_confirm', '');
            $vcode = $this->get('vcode', '');

            // 帐号检查
            $validateRet = $this->validateAccount($username, $mobile, $email);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 姓名检查
            if (!empty($username)) {
                $validateRet = $this->validateName($username);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // Email检查
            elseif (!empty($email)) {
                $validateRet = $this->validateEmail($email);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
                // 验证码检查
                $validateRet = $this->validateVcode($vcode, $email);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // 手机号检查
            elseif (!empty($mobile)) {
                $validateRet = $this->validateMobile($mobile);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
                // 验证码检查
                $validateRet = $this->validateVcode($vcode, $mobile);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // 密码检查
            $validateRet = $this->validatePassword($password, $password_confirm);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查Token
            $validateRet = $this->validateToken();
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查验证码
            $validateRet = $this->validateCaptcha();
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 验证用户名是否重复
            if (!empty($username)) {
                $validateRet = $this->checkNameIsExist($username);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // 验证email是否重复
            elseif (!empty($email)) {
                $validateRet = $this->checkEmailIsExist($email);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // 验证mobile是否重复
            elseif (!empty($mobile)) {
                $validateRet = $this->checkMobileIsExist($mobile);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // 会员注册
            $this->registerMember($username, $email, $mobile, $password);

            echo ($this->result("注册成功"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 输入手机号或邮箱地址以及验证码，发送短信处理的接口
     */
    public function sendfindpwdsmsAction()
    {
        // http://www.applicationmodule.com/member/service/sendfindpwdsms?mobile=xxx&email=xxx&hash=xxx
        try {
            $mobile = $this->get('mobile', '');
            $email = $this->get('email', '');

            // 帐号检查
            $validateRet = $this->validateAccount('', $mobile, $email);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // Email检查
            if (!empty($email)) {
                $validateRet = $this->validateEmail($email);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // 手机号检查
            if (!empty($mobile)) {
                $validateRet = $this->validateMobile($mobile);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // // 重复注册验证
            // if (process::islock('forget')) {
            // echo ($this->error(599, "您的操作过于频繁，请稍后再试"));
            // return false;
            // }

            // 检查Token
            $validateRet = $this->validateToken();
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查验证码
            $validateRet = $this->validateCaptcha();
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查用户是否存在
            if (!empty($email)) {
                $member = $this->modelMember->getInfoByEmail($email);
                if (empty($member)) {
                    $validateRet = $this->errors['512'];
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            } elseif (!empty($mobile)) {
                $member = $this->modelMember->getInfoByMobile($email);
                if (empty($member)) {
                    $validateRet = $this->errors['512'];
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            echo ($this->result("新密码已经发送至您的邮箱地址，请尽快登录并更改密码！"));
            fastcgi_finish_request();

            // 通过邮件或短信发送验证码
            // $email = $this->get('email', '');
            // $code = $this->get('code', '');
            // $contents = $this->get('contents', '');
            // doPost("http://{$_SERVER['HTTP_HOST']}/service/mail/send-by-template");

            // 重置密码
            // $newPassword = $this->modelMember->resetPwd($member['_id']);

            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 检查是否登录的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function checkloginAction()
    {
        // http://www.applicationmodule.com/member/service/checklogin
        try {
            if (!empty($_SESSION['member_id'])) {
                $memberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            } else {
                $memberInfo = array();
            }
            if (empty($memberInfo)) {
                echo ($this->error(-1, '未登录'));
                return false;
            } else {
                // 返回结果
                echo ($this->result("OK"));
                return true;
            }
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 密码修改的接口
     *
     * @return boolean
     */
    public function updateuserpwdAction()
    {
        // http://www.applicationmodule.com/member/service/updateuserpwd?userOldPwd=xxx&userNewPwd=xxx
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 获取会员信息
            $memberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            $isLogin = empty($memberInfo) ? false : true;
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $userOldPwd = $this->get('userOldPwd', ''); // 原有密码
            $userNewPwd = $this->get('userNewPwd', ''); // 新密码
            $password_confirm = $this->get('password_confirm', ''); // 确认密码

            // 密码检查
            $validateRet = $this->validatePassword($userNewPwd, $password_confirm);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查原有的密码是否正确
            if ($memberInfo['passwd'] != md5($userOldPwd)) {
                $validateRet = $this->errors['e514'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            // 会员密码修改
            $this->modelMember->updatePwd($_SESSION['member_id'], $userNewPwd);
            // 增加积分处理
            echo ($this->result("修改成功"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 手机号绑定的接口
     *
     * @return boolean
     */
    public function bindmobileAction()
    {
        // http://www.applicationmodule.com/member/service/bindmobile?mobile=xxx&vcode=xx
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $member_id = $_SESSION['member_id'];
            $mobile = $this->get('mobile', '');
            $vcode = $this->get('vcode', '');

            // Mobile检查
            $validateRet = $this->validateMobile($mobile);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            // 验证码检查
            $validateRet = $this->validateVcode($vcode, $mobile);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 验证mobile是否重复
            $validateRet = $this->checkMobileIsExist($mobile);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 绑定mobile操作
            $this->modelMember->bindMobile($member_id, $mobile);

            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 支付密码修改的接口
     *
     * @return boolean
     */
    public function updatepaypwdAction()
    {
        // http://www.applicationmodule.com/member/service/updatepaypwd?paypwd=xxx&key=xx&vcode=xxx
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $vcode = ($this->get('vcode', ''));
            $key = ($this->get('key', ''));
            $paypwd = $this->get('paypwd', ''); // 新的支付密码

            // 验证码检查
            $validateRet = $this->validateVcode($vcode, $key);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 会员密码修改
            $this->modelMember->updatePaypwd($_SESSION['member_id'], $paypwd);
            // 增加积分处理
            echo ($this->result("修改成功"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 邮箱绑定的接口
     *
     * @return boolean
     */
    public function bindemailAction()
    {
        // http://www.applicationmodule.com/member/service/bindemail?email=xxx&vcode=xxx
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $member_id = $_SESSION['member_id'];
            $email = $this->get('email', '');
            $vcode = $this->get('vcode', '');

            // Email检查
            $validateRet = $this->validateEmail($email);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            // 验证码检查
            $validateRet = $this->validateVcode($vcode, $email);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 验证email是否重复
            $validateRet = $this->checkEmailIsExist($email);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 绑定email操作
            $this->modelMember->bindEmail($member_id, $email);

            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 小额免密码设置的接口
     *
     * @return boolean
     */
    public function setsmallmoneyAction()
    {
        // http://www.applicationmodule.com/member/service/setsmallmoney?money=500&is_open=1&vcode=xx&key=xxx
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $member_id = $_SESSION['member_id'];
            $is_open = intval($this->get('is_open', '0'));
            $is_open = empty($is_open) ? false : true;
            $money = intval($this->get('money', '0'));
            $vcode = ($this->get('vcode', ''));
            $key = ($this->get('key', ''));

            // 验证码检查
            $validateRet = $this->validateVcode($vcode, $key);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 开启和关闭登录保护
            $this->modelMember->setSmallMoney($member_id, $is_open, $money);

            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 开启和关闭登录保护的接口
     * 账户安全-登录保护
     *
     * @return boolean
     */
    public function setlogintipAction()
    {
        // http://www.applicationmodule.com/member/service/setlogintip?is_open=1
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $member_id = $_SESSION['member_id'];
            $is_open = intval($this->get('is_open', '0'));
            $is_open = empty($is_open) ? false : true;

            // 开启和关闭登录保护
            $this->modelMember->setLoginTip($member_id, $is_open);

            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 个人资料修改的接口
     *
     * @return boolean
     */
    public function updateusertoAction()
    {
        // http://www.applicationmodule.com/member/service/updateuserto?nickname=xxx&tel_mobile=xxx&sex=xxx&birthday=xxx&constellation=xxx&location=xxx&hometown=xxx&qq=xxx&monthly_income=xxx&signature=xxx
        try {
            $nickname = $this->get('nickname', ''); // 昵称
            $tel_mobile = $this->get('tel_mobile', ''); // 备用电话
            $sex = $this->get('sex', ''); // 性别
            $birthday = $this->get('birthday', ''); // 生日
            $constellation = $this->get('constellation', ''); // 星座
            $location = $this->get('location', ''); // 所在地
            $hometown = $this->get('hometown', ''); // 家乡
            $qq = $this->get('qq', ''); // QQ号码
            $monthly_income = $this->get('monthly_income', ''); // 月收入
            $signature = $this->get('signature', ''); // 签名

            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 获取会员信息
            $memberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            $isLogin = empty($memberInfo) ? false : true;
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 会员修改
            $memo = $memberInfo['memo'];
            $data = array();
            if (!empty($nickname)) {
                $data['nickname'] = $nickname;
                $memo['nickname_unique'] = empty($memo['nickname_unique']) ? getNewId() : $memo['nickname_unique'];
            }
            if (!empty($tel_mobile)) {
                $data['tel_mobile'] = $tel_mobile;
                $memo['tel_mobile_unique'] = empty($memo['tel_mobile_unique']) ? getNewId() : $memo['tel_mobile_unique'];
            }
            if (!empty($sex)) {
                $data['sex'] = $sex;
                $memo['sex_unique'] = empty($memo['sex_unique']) ? getNewId() : $memo['sex_unique'];
            }
            if (!empty($birthday)) {
                // 检查是否是有效的日期格式
                if (!is_date($birthday)) {
                    echo ($this->error(-1, '生日的日期格式不正确'));
                    return false;
                }
                // 检查是否是在一年内修改的
                if (!empty($memberInfo['memo']['birthday_change_time']) && (time() - $memberInfo['memo']['birthday_change_time']) <= 3600 * 24 * 365) {
                    echo ($this->error(-2, '一年后才能再次编辑'));
                    return false;
                }
                // 转换成YYYY-MM-DD的格式
                $birthday = date('Y-m-d', strtotime($birthday));
                if ($memberInfo['birthday'] != $birthday) {
                    $data['birthday'] = $birthday;
                    $memo['birthday_unique'] = empty($memo['birthday_unique']) ? getNewId() : $memo['birthday_unique'];
                    $memo['birthday_change_time'] = time();
                }
            }
            if (!empty($constellation)) {
                $data['constellation'] = $constellation;
                $memo['constellation_unique'] = empty($memo['constellation_unique']) ? getNewId() : $memo['constellation_unique'];
            }
            if (!empty($location)) {
                $data['location'] = $location;
                $memo['location_unique'] = empty($memo['location_unique']) ? getNewId() : $memo['location_unique'];
            }
            if (!empty($hometown)) {
                $data['hometown'] = $hometown;
                $memo['hometown_unique'] = empty($memo['hometown_unique']) ? getNewId() : $memo['hometown_unique'];
            }
            if (!empty($qq)) {
                $data['qq'] = $qq;
                $memo['qq_unique'] = empty($memo['qq_unique']) ? getNewId() : $memo['qq_unique'];
            }
            if (!empty($monthly_income)) {
                $data['monthly_income'] = $monthly_income;
                $memo['monthly_income_unique'] = empty($memo['monthly_income_unique']) ? getNewId() : $memo['monthly_income_unique'];
            }
            if (!empty($signature)) {
                $data['signature'] = $signature;
                $memo['signature_unique'] = empty($memo['signature_unique']) ? getNewId() : $memo['signature_unique'];
            }
            if (!empty($data)) {
                $data['memo'] = $memo;
                // 更新处理
                $this->modelMember->updateMemberInfo($_SESSION['member_id'], $data);
                // 增加积分处理
                $headimgurl = empty($_SESSION['avatar']) ? '' : $_SESSION['avatar'];
                $nickname = !empty($nickname) ? $nickname : (empty($_SESSION['nickname']) ? '' : $_SESSION['nickname']);
                // 增加昵称的积分
                if (!empty($nickname)) {
                    $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'member_nickname');
                    $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $_SESSION['member_id'], $nickname, $headimgurl, $memo['nickname_unique'], $this->now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                }
                // 增加性别的积分
                if (!empty($sex)) {
                    $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'member_sex');
                    $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $_SESSION['member_id'], $nickname, $headimgurl, $memo['sex_unique'], $this->now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                }
                // 增加生日的积分
                if (!empty($birthday)) {
                    $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'member_birthday');
                    $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $_SESSION['member_id'], $nickname, $headimgurl, $memo['birthday_unique'], $this->now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                }
                // 增加现居地的积分
                if (!empty($location)) {
                    $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'member_location');
                    $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $_SESSION['member_id'], $nickname, $headimgurl, $memo['location_unique'], $this->now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                }
                // 增加家乡的积分
                if (!empty($hometown)) {
                    $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'member_hometown');
                    $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $_SESSION['member_id'], $nickname, $headimgurl, $memo['hometown_unique'], $this->now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                }
                // 增加QQ的积分
                if (!empty($qq)) {
                    $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'member_qq');
                    $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $_SESSION['member_id'], $nickname, $headimgurl, $memo['qq_unique'], $this->now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                }
                // 增加月收入的积分
                if (!empty($monthly_income)) {
                    $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'member_monthly_income');
                    $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $_SESSION['member_id'], $nickname, $headimgurl, $memo['monthly_income_unique'], $this->now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                }
                // 增加签名的积分
                if (!empty($signature)) {
                    $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'member_signature');
                    $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $_SESSION['member_id'], $nickname, $headimgurl, $memo['signature_unique'], $this->now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                }
            }
            echo ($this->result("修改成功"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 上传头像的接口
     *
     * @return boolean
     */
    public function uploadphotoAction()
    {
        // http://www.applicationmodule.com/member/service/uploadphoto?avatar=xxx&x=xxx&y=xxx&width=xxx&height=xxx
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $avatar = $this->get('avatar', '');
            if (empty($avatar)) {
                echo ($this->error('-2', '头像图片未指定'));
                return false;
            }

            $x = $this->get('x', '0');
            $y = $this->get('y', '0');
            $width = $this->get('width', '336');
            $height = $this->get('height', '336');

            $uploadPath = $this->modelMember->getUploadPath();
            makeDir(APP_PATH . "public/upload/{$uploadPath}");
            $filename = APP_PATH . "public/upload/{$uploadPath}/{$avatar}";
            if (!file_exists($filename)) {
                echo ($this->error('-1', '头像图片未找到'));
                return false;
            }
            // 图片处理
            $imagine = new \Imagine\Imagick\Imagine();
            $image = $imagine->open($filename);
            $image->resize(new Box(400, 400))
                ->crop(new Point($x, $y), new Box($width, $height))
                ->save($filename);
            // 更新头像
            $this->modelMember->updateAvatar($_SESSION['member_id'], $avatar);
            echo ($this->result("修改成功"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 收货地址增加和修改的接口
     *
     * @return boolean
     */
    public function saveaddressAction()
    {
        // http://www.applicationmodule.com/member/service/saveaddress?id=xxx&name=xxx&province=xxx&city=xxx&district=xxx&address=xxx&zipcode=xxx&telephone=xxx&mobile=xxx&is_default=xxx
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $id = $this->get('id', '');
            $name = $this->get('name', '');
            $province = $this->get('province', '');
            $city = $this->get('city', '');
            $district = $this->get('district', '');
            $address = $this->get('address', '');
            $zipcode = $this->get('zipcode', '');
            $telephone = $this->get('telephone', '');
            $mobile = $this->get('mobile', '');
            $is_default = intval($this->get('is_default', '0'));
            if (!empty($id)) {
                $consignee = $this->modelConsignee->getInfoById($id);
                if (empty($consignee)) {
                    echo ($this->error('-2', 'id不正确'));
                    return false;
                }
                if ($consignee['member_id'] != $_SESSION['member_id']) {
                    echo ($this->error('-3', 'id不正确'));
                    return false;
                }
            }
            // 增加和修改收货人处理
            $consignee = $this->modelConsignee->insertOrUpdate($id, $_SESSION['member_id'], $name, $province, $city, $district, $address, $zipcode, $telephone, $mobile, $is_default);
            // 如果是否默认为true的话
            if (!empty($is_default)) {
                if (empty($id)) {
                    $id = $consignee['_id'];
                }
                $this->modelConsignee->setDefault($id);
            }
            echo ($this->result("OK", $consignee));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 设置默认地址
     *
     * @return boolean
     */
    public function setmembercontactdefaultAction()
    {
        // http://www.applicationmodule.com/member/service/setmembercontactdefault?id=xxx
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $id = $this->get('id', '');
            if (empty($id)) {
                echo ($this->error('-1', 'id为空'));
                return false;
            }
            $consignee = $this->modelConsignee->getInfoById($id);
            if (empty($consignee)) {
                echo ($this->error('-2', 'id不正确'));
                return false;
            }
            $consignee = $this->modelConsignee->getInfoById($id);
            if ($consignee['member_id'] != $_SESSION['member_id']) {
                echo ($this->error('-3', 'id不正确'));
                return false;
            }
            $this->modelConsignee->setDefault($id);
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 收货地址刪除的接口
     *
     * @return boolean
     */
    public function deleteaddressAction()
    {
        // http://www.applicationmodule.com/member/service/deleteaddress?id=xxx
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $id = $this->get('id', '');
            if (empty($id)) {
                echo ($this->error('-1', 'id为空'));
                return false;
            }
            $consignee = $this->modelConsignee->getInfoById($id);
            if (empty($consignee)) {
                echo ($this->error('-2', 'id不正确'));
                return false;
            }
            $consignee = $this->modelConsignee->getInfoById($id);
            if ($consignee['member_id'] != $_SESSION['member_id']) {
                echo ($this->error('-3', 'id不正确'));
                return false;
            }
            $this->modelConsignee->remove(array(
                '_id' => $id
            ));
            // 增加收货人处理
            echo ($this->result("删除成功"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 设置隐私设置的接口
     */
    public function membercenterupdateprivsetAction()
    {
        // http://www.applicationmodule.com/member/service/membercenterupdateprivset?msgSet=1&areaSet=1&searchSet=1&buySet=1&rafSet=1&postSet=1&buyShowNum=1&rafShowNum=1&postShowNum=1
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $msgSet = $this->get('msgSet', '1'); // 私信 1:仅限好友 2 禁止
            $areaSet = $this->get('areaSet', '0'); // 地理位置 0:允许 1:禁止
            $searchSet = $this->get('searchSet', '0'); // 好友搜索 0:允许 1:禁止
            $buySet = $this->get('buySet', '0'); // 个人主页-云购记录 0:所有人可见 1:好友可见 2:仅自己可见
            $buyShowNum = $this->get('buyShowNum', '0'); // 个人主页-云购记录 显示
            $rafSet = $this->get('rafSet', '0'); // 个人主页-获得的商品 0:所有人可见 1:好友可见 2:仅自己可见
            $rafShowNum = $this->get('rafShowNum', '0'); // 个人主页-获得的商品 显示
            $postSet = $this->get('postSet', '0'); // 个人主页-晒单 0:所有人可见 1:好友可见 2:仅自己可见
            $postShowNum = $this->get('postShowNum', '0'); // 个人主页-晒单 显示

            // 更新处理
            $data['privacy'] = $this->modelMember->getPrivacyInfo($msgSet, $areaSet, $searchSet, $buySet, $buyShowNum, $rafSet, $rafShowNum, $postSet, $postShowNum);
            $this->modelMember->updateMemberInfo($_SESSION['member_id'], $data);
            echo ($this->result("修改成功"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 设置接受参与云购未获得商品的提醒通知的接口
     */
    public function membercenternoticesetAction()
    {
        // http://www.applicationmodule.com/member/service/membercenternoticeset?sysMsgSet=0&wxMailSet=0
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $sysMsgSet = $this->get('sysMsgSet', '0');
            $wxMailSet = $this->get('wxMailSet', '0');
            // 更新处理
            $data = array();
            $data['noticesettings'] = $this->modelMember->getNoticeSettings($sysMsgSet, $wxMailSet);
            $this->modelMember->updateMemberInfo($_SESSION['member_id'], $data);
            echo ($this->result("修改成功"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 查找好友的接口
     * 会员-我的好友-查找好友
     *
     * @throws \Exception
     * @return boolean
     */
    public function getmembersearchfriendsAction()
    {
        // http://www.applicationmodule.com/member/service/getmembersearchfriends?type=0&key=13564100096
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            // 搜索 获得商品最多 活跃会员 最新加入
            $type = intval($this->get('type', '0'));
            $key = urldecode($this->get('key', ''));
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '9'));
            $query = array();
            $query['_id'] = array(
                '$ne' => $_SESSION['member_id']
            );
            // 排除已经是朋友的会员
            $friend_ids = $this->modelMemberFriend->getMyFriendIds($_SESSION['member_id'], 1, 1000);
            if (!empty($friend_ids)) {
                $friend_ids = array_merge($friend_ids, array(
                    $_SESSION['member_id']
                ));
                $query['_id'] = array(
                    '$nin' => $friend_ids
                );
            }
            // 好友搜索 设置是否允许让其它会员搜索到您
            $query['privacy'] = array(
                '$like' => '%"searchSet":0%'
            );
            if (!empty($key)) {
                $query['__OR__'] = array(
                    'mobile' => $key,
                    'email' => $key,
                    'nickname' => array(
                        '$like' => '%' . $key . '%'
                    )
                );
            }

            $sort = array();
            if (empty($type)) { // 搜索
                $sort['__RANDOM__'] = 1;
            } elseif ($type == 1) { // 获得商品最多
                $sort['prized_num'] = -1;
            } elseif ($type == 2) { // 活跃会员??怎么算活跃
                $sort['buy_num'] = -1;
            } elseif ($type == 3) { // 最新加入
                $sort['_id'] = -1;
            }
            $list = $this->modelMember->getSearchFriendsList($page, $limit, $query, $sort);

            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    $user_ids[] = $item['_id'];
                }
                $pointUserList = $this->modelPointsUser->getListByUserIds($user_ids, POINTS_CATEGORY2);

                foreach ($list['datas'] as $item) {
                    // address: ""
                    // grade: "01"
                    // gradeName: "云购小将"
                    // sign: "签　　名"
                    // userID: "9563477"
                    // userName: "郭永荣"
                    // userPhoto: "20151106195125381.jpg"
                    // userWeb: "1010381532"
                    if (!isset($pointUserList[$item['_id']])) {
                        throw new \Exception("{$item['_id']}对应的积分账户不存在");
                    }
                    $exp = $pointUserList[$item['_id']]['current'];
                    $gradeInfo = $this->modelMemberGrade->getGradeInfo($exp);

                    $datas[] = array(
                        'address' => '',
                        'grade' => str_pad($gradeInfo['current']['level'], 2, '0', STR_PAD_LEFT),
                        'gradeName' => $gradeInfo['current']['name'],
                        'sign' => $item['signature'],
                        'userID' => $item['_id'],
                        'userName' => $this->modelMember->getRegisterName($item, true),
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['avatar']),
                        'userWeb' => $item['_id']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 我的好友的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function getmemberfriendsAction()
    {
        // http://www.applicationmodule.com/member/service/getmemberfriends?page=1&limit=9&key=云购技
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '9'));
            $key = urldecode($this->get('key', ''));
            $query1 = array();
            if (!empty($_SESSION['member_id'])) {
                $query1['to_user_id'] = $_SESSION['member_id']; // '56757a39887c22034a8b4596';
            }
            $query1['state'] = \App\Member\Models\Friend::STATE1;
            if (!empty($key)) {
                $query1['__OR__'] = array(
                    'from_user_mobile' => $key,
                    'from_user_email' => $key,
                    'from_user_nickname' => array(
                        '$like' => '%' . $key . '%'
                    )
                );
            }

            $query2 = array();
            if (!empty($_SESSION['member_id'])) {
                $query2['from_user_id'] = $_SESSION['member_id']; // '56761153887c22184e8b45b5'; // ;
            }
            $query2['state'] = \App\Member\Models\Friend::STATE1;
            if (!empty($key)) {
                $query2['__OR__'] = array(
                    'to_user_mobile' => $key,
                    'to_user_email' => $key,
                    'to_user_nickname' => array(
                        '$like' => '%' . $key . '%'
                    )
                );
            }
            $query = array(
                '__QUERY_OR__' => array(
                    $query1,
                    $query2
                )
            );
            $sort = array();
            $list = $this->modelMemberFriend->getAgreeList($page, $limit, $query, $sort);

            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    if ($item['from_user_id'] != $_SESSION['member_id']) {
                        $user_ids[] = $item['from_user_id'];
                    } elseif ($item['to_user_id'] != $_SESSION['member_id']) {
                        $user_ids[] = $item['to_user_id'];
                    }
                }
                $pointUserList = $this->modelPointsUser->getListByUserIds($user_ids, POINTS_CATEGORY2);
                $memberList = $this->modelMember->getListByIds($user_ids);

                foreach ($list['datas'] as $item) {
                    // address: ""
                    // grade: "01"
                    // gradeName: "云购小将"
                    // sign: ""
                    // userID: "10605005"
                    // userName: "18917****57"
                    // userPhoto: "00000000000000000.jpg"
                    // userWeb: "1011789946"
                    $user_id = "";
                    if ($item['from_user_id'] != $_SESSION['member_id']) {
                        $user_id = $item['from_user_id'];
                    } elseif ($item['to_user_id'] != $_SESSION['member_id']) {
                        $user_id = $item['to_user_id'];
                    }

                    if (!isset($pointUserList[$user_id])) {
                        throw new \Exception("{$user_id}对应的积分账户不存在");
                    }
                    if (!isset($memberList[$user_id])) {
                        throw new \Exception("{$user_id}对应的会员信息不存在");
                    }
                    $exp = $pointUserList[$user_id]['current'];
                    $gradeInfo = $this->modelMemberGrade->getGradeInfo($exp);
                    $memberInfo = $memberList[$user_id];
                    $datas[] = array(
                        'address' => '',
                        'grade' => str_pad($gradeInfo['current']['level'], 2, '0', STR_PAD_LEFT),
                        'gradeName' => $gradeInfo['current']['name'],
                        'sign' => $memberInfo['signature'],
                        'userID' => $memberInfo['_id'],
                        'userName' => $this->modelMember->getRegisterName($memberInfo, true),
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $memberInfo['avatar']),
                        'userWeb' => $memberInfo['_id']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * +好友的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function insertuserfriendapplyAction()
    {
        // http://www.applicationmodule.com/member/service/insertuserfriendapply?userID=xxx
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $userID = $this->get('userID', '');
            if (empty($userID)) {
                echo ($this->error(-2, '会员ID为空'));
                return false;
            }

            $toMemberInfo = $this->modelMember->getInfoById($userID);
            if (empty($toMemberInfo)) {
                echo ($this->error(-3, '会员ID不正确'));
                return false;
            }

            $fromMemberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            if (empty($fromMemberInfo)) {
                echo ($this->error(-4, '会员ID不正确'));
                return false;
            }

            $friendInfo = $this->modelMemberFriend->check($_SESSION['member_id'], $userID);
            if (empty($friendInfo)) {
                $this->modelMemberFriend->apply($_SESSION['member_id'], $fromMemberInfo['nickname'], $fromMemberInfo['email'], $fromMemberInfo['mobile'], $fromMemberInfo['register_by'], $userID, $toMemberInfo['nickname'], $toMemberInfo['email'], $toMemberInfo['mobile'], $toMemberInfo['register_by']);
                $this->modelMsgCount->incFriendMsgCount($userID);
            }
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 同意好友的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function agreeuserfriendAction()
    {
        // http://member.1yyg.com/JPData?action=agreeUserFriend&applyID=0&fun=jsonp1451047046690&_=1451047055444
        // http://www.applicationmodule.com/member/service/agreeuserfriend?applyID=36969458
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $applyID = $this->get('applyID', '');
            $friendInfo = $this->modelMemberFriend->getInfoById($applyID);
            if (empty($friendInfo)) {
                echo ($this->error(-1, 'applyId不正确'));
                return false;
            }

            $this->modelMemberFriend->agree($_SESSION['member_id'], $applyID);
            // 发送系统消息
            $friend = array();
            $friend['nickname'] = $friendInfo['to_user_nickname'];
            $friend['mobile'] = $friendInfo['to_user_mobile'];
            $friend['email'] = $friendInfo['to_user_email'];
            $friend['register_by'] = $friendInfo['to_user_register_by'];

            $name = $this->modelMember->getLoginName($friend);
            $content = "<a href=\"{$this->baseUrl}yungou/member/index?id={$_SESSION['member_id']}\" class=\"blue\" target=\"_blank\">{$name}</a> 已通过您的好友请求。";
            $this->modelSysMsg->log($friendInfo['from_user_id'], $content);
            $this->modelMsgCount->incSysMsgCount($friendInfo['from_user_id']);
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 忽略好友的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function ignoreuserfriendAction()
    {
        // http://member.1yyg.com/JPData?action=ignoreUserFriend&applyID=37301058&fun=jsonp1451046553987&_=1451046593893
        // http://member.1yyg.com/JPData?action=ignoreUserFriend&applyID=0&fun=jsonp1451046733341&_=1451046741989
        // http://www.applicationmodule.com/member/service/ignoreuserfriend?applyID=37233515
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $applyID = $this->get('applyID', '');
            $this->modelMemberFriend->ignore($_SESSION['member_id'], $applyID);
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 检查是否好友的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function checkuserfriendAction()
    {
        // http://u.1yyg.com/JPData?action=checkUserFriend&userID=9563477&fun=jsonp1452090079360&_=1452090079671
        // jsonp1452090079360({'code':2})
        // http://www.applicationmodule.com/member/service/checkuserfriend?userID=10605005
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $userID = $this->get('userID', '');
            if (empty($userID)) {
                echo ($this->error(-2, '用户ID为空'));
                return false;
            }
            $friendInfo = $this->modelMemberFriend->check($_SESSION['member_id'], $userID);
            if (empty($friendInfo)) {
                $isFriend = false;
            } else {
                $isFriend = true;
            }
            echo ($this->result("OK", $isFriend));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取好友请求列表的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function getmemberfriendsapplyAction()
    {
        // http://www.applicationmodule.com/member/service/getmemberfriendsapply?page=1&limit=5
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '5'));
            $query = array();
            if (!empty($_SESSION['member_id'])) {
                $query['to_user_id'] = $_SESSION['member_id'];
            }
            $sort = array();
            $list = $this->modelMemberFriend->getApplyList($page, $limit, $query, $sort);

            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    $user_ids[] = $item['from_user_id'];
                }
                $pointUserList = $this->modelPointsUser->getListByUserIds($user_ids, POINTS_CATEGORY2);
                $memberList = $this->modelMember->getListByIds($user_ids);

                foreach ($list['datas'] as $item) {
                    // address: ""
                    // applyID: "37233515"
                    // applyTime: "昨天 23:38"
                    // grade: "01"
                    // gradeName: "云购小将"
                    // sign: ""
                    // userID: "10605005"
                    // userName: "18917****57"
                    // userPhoto: "00000000000000000.jpg"
                    // userWeb: "1011789946"
                    if (!isset($pointUserList[$item['from_user_id']])) {
                        throw new \Exception("{$item['from_user_id']}对应的积分账户不存在");
                    }
                    if (!isset($memberList[$item['from_user_id']])) {
                        throw new \Exception("{$item['from_user_id']}对应的会员信息不存在");
                    }
                    $exp = $pointUserList[$item['from_user_id']]['current'];
                    $gradeInfo = $this->modelMemberGrade->getGradeInfo($exp);
                    $memberInfo = $memberList[$item['from_user_id']];

                    $datas[] = array(
                        'address' => '',
                        'applyID' => $item['_id'],
                        'applyTime' => date('Y-m-d H:i:s', $item['apply_time']->sec),
                        'grade' => str_pad($gradeInfo['current']['level'], 2, '0', STR_PAD_LEFT),
                        'gradeName' => $gradeInfo['current']['name'],
                        'sign' => $memberInfo['signature'],
                        'userID' => $memberInfo['_id'],
                        'userName' => $this->modelMember->getRegisterName($memberInfo),
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $memberInfo['avatar']),
                        'userWeb' => $memberInfo['_id']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 删除好友
     *
     * @return boolean
     */
    public function deleteuserfriendAction()
    {
        // http://member.1yyg.com/JPData?action=deleteUserFriend&friendID=10605005&fun=jsonp1451046481245&_=1451046496646
        // http://www.applicationmodule.com/member/service/deleteuserfriend?friendID=10605005
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            $friendID = $this->get('friendID', '');
            if (empty($friendID)) {
                echo ($this->error(-2, 'ID为空'));
                return false;
            }
            $this->modelMemberFriend->delete($_SESSION['member_id'], $friendID);
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取最近访问者列表的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function getrecentvisitorsAction()
    {
        // http://u.1yyg.com/JPData?action=getRecentVisitors&userId=1011789946&fun=jsonp1452090079361&_=1452090079726
        // jsonp1450879058083({"code":0,"str":[{"userWeb":"1010381532","userPhoto":"20151106195125381.jpg","userName":"郭永荣","userBirthAreaName":"","browserTime":"16分钟前","birthAreaNameState":" hidden","gradeLevel":"01"}]})
        // http://www.applicationmodule.com/member/service/getrecentvisitors?userId=1011789946
        try {
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '5'));
            $userId = ($this->get('userId', ''));
            $query = array();
            if (empty($_SESSION['browser_time'])) {
                $browser_time = getCurrentTime();
            } else {
                $browser_time = $_SESSION['browser_time'];
            }
            $query['browser_time'] = array(
                '$lt' => $browser_time
            );
            $sort = array();
            $list = $this->modelMemberVisitor->getList($userId, $page, $limit, $query, $sort);

            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    $user_ids[] = $item['visit_user_id'];
                }
                $pointUserList = $this->modelPointsUser->getListByUserIds($user_ids, POINTS_CATEGORY2);
                $memberList = $this->modelMember->getListByIds($user_ids);

                foreach ($list['datas'] as $item) {

                    if (!isset($pointUserList[$item['visit_user_id']])) {
                        throw new \Exception("{$item['visit_user_id']}对应的积分账户不存在");
                    }
                    if (!isset($memberList[$item['visit_user_id']])) {
                        throw new \Exception("{$item['visit_user_id']}对应的会员信息不存在");
                    }
                    $exp = $pointUserList[$item['visit_user_id']]['current'];
                    $gradeInfo = $this->modelMemberGrade->getGradeInfo($exp);
                    $memberInfo = $memberList[$item['visit_user_id']];

                    // userID: "10605005"
                    // userName: "18917****57"
                    // userPhoto: "00000000000000000.jpg"
                    // userWeb: "1011789946"
                    // userBirthAreaName":"",
                    // browserTime":"16分钟前",
                    // birthAreaNameState":" hidden",
                    // gradeLevel":"01"

                    $datas[] = array(
                        'browserTime' => date('Y-m-d H:i:s', $item['browser_time']->sec),
                        'gradeLevel' => str_pad($gradeInfo['current']['level'], 2, '0', STR_PAD_LEFT),
                        'gradeName' => $gradeInfo['current']['name'],
                        'sign' => $memberInfo['signature'],
                        'userID' => $memberInfo['_id'],
                        'userName' => $this->modelMember->getRegisterName($memberInfo, true),
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $memberInfo['avatar']),
                        'userWeb' => $memberInfo['_id'],
                        'userBirthAreaName' => '',
                        'birthAreaNameState' => ''
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 个人主页
     * +好友的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function applyfriendAction()
    {
        // http://u.1yyg.com/JPData?action=applyFriend&userWeb=1001851285&fun=jsonp1452173191831&_=1452173921792
        // jsonp1452173191831({'code':1})
        // http://www.applicationmodule.com/member/service/applyfriend?userWeb=1001851285
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $userWeb = $this->get('userWeb', '');
            if (empty($userWeb)) {
                echo ($this->error(-2, '用户ID为空'));
                return false;
            }

            $toMemberInfo = $this->modelMember->getInfoById($userWeb);
            if (empty($toMemberInfo)) {
                echo ($this->error(-3, '用户ID不正确'));
                return false;
            }

            $fromMemberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            if (empty($fromMemberInfo)) {
                echo ($this->error(-4, '会员ID不正确'));
                return false;
            }

            $friendInfo = $this->modelMemberFriend->check($_SESSION['member_id'], $userWeb);
            if (empty($friendInfo)) {
                $this->modelMemberFriend->apply($fromMemberInfo['_id'], $fromMemberInfo['nickname'], $fromMemberInfo['email'], $fromMemberInfo['mobile'], $fromMemberInfo['register_by'], $toMemberInfo['_id'], $toMemberInfo['nickname'], $toMemberInfo['email'], $toMemberInfo['mobile'], $toMemberInfo['register_by']);
                $this->modelMsgCount->incFriendMsgCount($toMemberInfo['_id']);
            }
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 个人主页
     * 举报的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function insertreportuserAction()
    {
        // http://u.1yyg.com/JPData?action=insertReportUser&&type=4&userWeb=1011468555&content=%u6635%u79F0%u7684%u540D%u5B57%u4E0D%u597D%uFF0C%u5E26%u6709%u810F%u5B57&fun=jsonp1452265301622&_=1452265354451
        // jsonp1452265301622({"code":0})
        // http://www.applicationmodule.com/member/service/insertreportuser?type=4&userWeb=1011468555&content=%u6635%u79F0%u7684%u540D%u5B57%u4E0D%u597D%uFF0C%u5E26%u6709%u810F%u5B57
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $type = $this->get('type', '1');
            $content = $this->get('content', '');
            if ($type == 4) {
                if (empty($content)) {
                    echo ($this->error(-1, '举报内容为空'));
                    return false;
                }
            }

            $userWeb = $this->get('userWeb', '');
            if (empty($userWeb)) {
                echo ($this->error(-2, '用户ID为空'));
                return false;
            }

            $toMemberInfo = $this->modelMember->getInfoById($userWeb);
            if (empty($toMemberInfo)) {
                echo ($this->error(-3, '用户ID不正确'));
                return false;
            }

            $fromMemberInfo = $this->modelMember->getInfoById($_SESSION['member_id']);
            if (empty($fromMemberInfo)) {
                echo ($this->error(-4, '会员ID不正确'));
                return false;
            }

            $this->modelMemberReport->log($fromMemberInfo['_id'], $fromMemberInfo['nickname'], $fromMemberInfo['email'], $fromMemberInfo['mobile'], $fromMemberInfo['register_by'], $toMemberInfo['_id'], $toMemberInfo['nickname'], $toMemberInfo['email'], $toMemberInfo['mobile'], $toMemberInfo['register_by'], $type, $content);
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 成功邀请列表的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function getinvitedmemberinfolistAction()
    {
        // http://member.1yyg.com/JPData?action=getInvitedMemberInfoList&FIdx=1&EIdx=10&isCount=1&fun=jsonp1451610337872&_=1451610338286
        // jsonp1451610337872({"code":0,"str":{"totalCount":1,"buyCount":0,"listItems":[{"userName":"18917****57","regTime":"2015.12.17 23:50:38","state":"0","userWeb":"1011789946","userPhoto":"00000000000000000.jpg","userCode":"1011789946"}]}})
        // http://www.applicationmodule.com/member/service/getinvitedmemberinfolist?page=1&limit=10
        try {
            // 会员登录检查
            $isLogin = $this->modelMember->checkloginMember();
            if (!$isLogin) {
                $validateRet = $this->errors['e595'];
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }
            $page = intval($this->get('page', '1'));
            $limit = intval($this->get('limit', '10'));

            // 获取邀请记录
            $invitationInfo = $this->modelInvitation->getInfoByUserId($_SESSION['member_id'], YUNGOU_ACTIVITY_ID);
            if (empty($invitationInfo)) {
                echo ($this->error(-1, '未找到邀请记录'));
                return false;
            }
            // 获取邀请明细
            $list = $this->modelInvitationGotDetail->getListByPage($invitationInfo['_id'], $page, $limit);

            $ret = array();
            $ret['total'] = $list['total'];
            $datas = array();
            if (!empty($list['datas'])) {
                foreach ($list['datas'] as $item) {
                    // "userName":"18917****57",
                    // "regTime":"2015.12.17 23:50:38",
                    // "state":"0",
                    // "userWeb":"1011789946",
                    // "userPhoto":"00000000000000000.jpg",
                    // "userCode":"1011789946"
                    $datas[] = array(
                        'userName' => $item['got_user_name'],
                        'regTime' => date('Y-m-d H:i:s', $item['got_time']->sec),
                        'state' => $item['got_worth2'],
                        'userWeb' => $item['got_user_id'],
                        'userPhoto' => $this->modelMember->getImagePath($this->baseUrl, $item['got_user_headimgurl']),
                        'userCode' => $item['got_user_id']
                    );
                }
            }
            $ret['datas'] = $datas;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取短链接
     */
    public function getshorturlAction()
    {
        // url:https://api.weibo.com/2/short_url/shorten.json?source=1681459862&url_long=http%3A//www.1yyg.com/api/referrals.ashx%3Frid%3D9563477%26url%3Dhttp%253A//www.1yyg.com%253Fs%253Dshare-u9563477
    }

    /**
     * 输入重置密码，进行重置处理的接口
     */
    public function resetpwdAction()
    {
        // http://www.applicationmodule.com/member/service/resetpwd?vcode=xxx&mobile=xxx&email=xxx&password=xxx&password_confirm=xxx
        try {
            $username = $this->get('username', '');
            $mobile = $this->get('mobile', '');
            $email = $this->get('email', '');
            $password = $this->get('password', '');
            $password_confirm = $this->get('password_confirm', '');
            $vcode = $this->get('vcode', '');

            // 帐号检查
            $validateRet = $this->validateAccount($username, $mobile, $email);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 姓名检查
            if (!empty($username)) {
                $validateRet = $this->validateName($username);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // Email检查
            elseif (!empty($email)) {
                $validateRet = $this->validateEmail($email);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
                // 验证码检查
                $validateRet = $this->validateVcode($vcode, $email);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // 手机号检查
            elseif (!empty($mobile)) {
                $validateRet = $this->validateMobile($mobile);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
                // 验证码检查
                $validateRet = $this->validateVcode($vcode, $mobile);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }

            // 密码检查
            $validateRet = $this->validatePassword($password, $password_confirm);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查数据库是否有相应的数据
            // 用户名
            if (!empty($username)) {
                $memberInfo = $this->modelMember->getInfoByName($username);
            }

            // email
            elseif (!empty($email)) {
                $memberInfo = $this->modelMember->getInfoByEmail($email);
            }

            // mobile
            elseif (!empty($mobile)) {
                $memberInfo = $this->modelMember->getInfoByMobile($mobile);
            }

            if (empty($memberInfo) || empty($memberInfo['state'])) {
                $errorInfo = $this->errors['e510'];
                echo ($this->error($errorInfo['error_code'], $errorInfo['error_msg']));
                return false;
            } else {
                if (!$memberInfo['state']) {
                    $errorInfo = $this->errors['e511'];
                    echo ($this->error($errorInfo['error_code'], $errorInfo['error_msg']));
                    return false;
                }
                $this->modelMember->resetPwd($memberInfo['_id'], $password);
            }
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * QQ绑定页面
     * 绑定QQ的接口
     *
     * @throws \Exception
     * @return boolean
     */
    public function qcbindaccountAction()
    {
        // https://passport.1yyg.com/JPData?action=qcbindaccount&account=13564100096&pwd=vcvcvcvcvcvcvcvcv&token=EF8YhZ3E.IreswjLpooYM59qxLyWSqopw4LPjdQAfe4j2mE7y9tmTtJJp3TB0AYZqamcGPxJmCTEQPccWst6YtKK33HdQvZgObKwduVde6iUuPLJuL1oTT7DeGiEvW5Qx2y26AU.u75KuVymoMKJmA==&fun=jsonp1453468901120&_=1453468922071
        // jsonp1453468901120({"state":1, "num":-1})
        // http://www.applicationmodule.com/member/service/qcbindaccount?username=xxx&mobile=xxx&password=xxx&email=xxx&password_confirm=xxx&vcode=xxx
        try {
            $username = $this->get('username', '');
            $mobile = $this->get('mobile', '');
            $email = $this->get('email', '');
            $password = $this->get('password', '');
            $password_confirm = $this->get('password_confirm', '');
            $vcode = $this->get('vcode', '');

            if (empty($_SESSION['Tencent_userInfo']) && empty($_SESSION['Weixin_userInfo'])) {
                echo ($this->error(-1, '非法访问'));
                return false;
            }

            // 检查QQ号是否使用了
            if (!empty($_SESSION['Tencent_userInfo'])) {
                $openid = $_SESSION['Tencent_userInfo']['user_id'];
                $userInfo = $this->modelMember->getInfoByQQOpenid($openid);
                if (!empty($userInfo)) {
                    echo ($this->error(-1, '非法访问,QQ号已经绑定了其他账号了'));
                    return false;
                }
            }

            // 检查微信号是否使用了
            if (!empty($_SESSION['Weixin_userInfo'])) {
                $openid = $_SESSION['Weixin_userInfo']['user_id'];
                $userInfo = $this->modelMember->getInfoByWeixinOpenid($openid);
                if (!empty($userInfo)) {
                    echo ($this->error(-1, '非法访问,微信号已经绑定了其他账号了'));
                    return false;
                }
            }

            // 帐号检查
            $validateRet = $this->validateAccount($username, $mobile, $email);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 姓名检查
            if (!empty($username)) {
                $validateRet = $this->validateName($username);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
            }
            // Email检查
            if (!empty($email)) {
                $validateRet = $this->validateEmail($email);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
                if (!empty($vcode)) {
                    // 验证码检查
                    $validateRet = $this->validateVcode($vcode, $email);
                    if (!empty($validateRet['error_code'])) {
                        echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                        return false;
                    }
                }
            }

            // 手机号检查
            if (!empty($mobile)) {
                $validateRet = $this->validateMobile($mobile);
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }
                if (!empty($vcode)) {
                    // 验证码检查
                    $validateRet = $this->validateVcode($vcode, $mobile);
                    if (!empty($validateRet['error_code'])) {
                        echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                        return false;
                    }
                }
            }

            // 密码检查
            $validateRet = $this->validatePassword($password, $password_confirm);
            if (!empty($validateRet['error_code'])) {
                echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                return false;
            }

            // 检查数据库是否有相应的数据
            // 用户名
            if (!empty($username)) {
                $memberInfo = $this->modelMember->getInfoByName($username);
            }

            // email
            elseif (!empty($email)) {
                $memberInfo = $this->modelMember->getInfoByEmail($email);
            }

            // mobile
            elseif (!empty($mobile)) {
                $memberInfo = $this->modelMember->getInfoByMobile($mobile);
            }

            if (empty($memberInfo)) {
                // 检查验证码
                $validateRet = $this->validateCaptcha();
                if (!empty($validateRet['error_code'])) {
                    echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                    return false;
                }

                // 验证用户名是否重复
                if (!empty($username)) {
                    $validateRet = $this->checkNameIsExist($username);
                    if (!empty($validateRet['error_code'])) {
                        echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                        return false;
                    }
                }

                // 验证email是否重复
                elseif (!empty($email)) {
                    $validateRet = $this->checkEmailIsExist($email);
                    if (!empty($validateRet['error_code'])) {
                        echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                        return false;
                    }
                }

                // 验证mobile是否重复
                elseif (!empty($mobile)) {
                    $validateRet = $this->checkMobileIsExist($mobile);
                    if (!empty($validateRet['error_code'])) {
                        echo ($this->error($validateRet['error_code'], $validateRet['error_msg']));
                        return false;
                    }
                }

                // 会员注册
                $memberInfo = $this->registerMember($username, $email, $mobile, $password);
            } else {
                if ($memberInfo['passwd'] != md5($password)) {
                    $errorInfo = $this->errors['e510'];
                    echo ($this->error($errorInfo['error_code'], $errorInfo['error_msg']));
                    return false;
                }

                if (!$memberInfo['state']) {
                    $errorInfo = $this->errors['e511'];
                    echo ($this->error($errorInfo['error_code'], $errorInfo['error_msg']));
                    return false;
                }
            }

            // 绑定QQ操作
            if (!empty($_SESSION['Tencent_userInfo']) && empty($memberInfo['qqopenid'])) {
                $this->modelMember->bindQQOpenid($memberInfo['_id'], $_SESSION['Tencent_userInfo']['user_id'], $_SESSION['Tencent_userInfo']);
            }
            // 绑定微信操作
            if (!empty($_SESSION['Weixin_userInfo']) && empty($memberInfo['weixinopenid'])) {
                $this->modelMember->bindWeixinOpenid($memberInfo['_id'], $_SESSION['Weixin_userInfo']['user_id'], $_SESSION['Weixin_userInfo']);
            }
            // 登录处理
            $this->loginMember($memberInfo);
            echo ($this->result("OK"));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    private function getInvitation($invitationInfo, $userInfo)
    {
        if (empty($invitationInfo)) {
            return;
        }
        $invitationId = $invitationInfo['_id'];

        // 是否发起者和领取者是同一个人
        $isSame = $this->modelInvitation->isSame($invitationInfo, $userInfo['_id']);

        // 检查是否已经领完了
        $isOver = $this->modelInvitation->isOver($invitationInfo);

        // 检查是否已经领取过了
        $invitationInfoGotDetail = $this->modelInvitationGotDetail->getInfoByInvitationIdAndGotUserId($invitationId, $userInfo['_id']);
        $isGot = empty($invitationInfoGotDetail) ? false : true;

        if (!$isSame && !$isOver && !$isGot) {
            // 如果没有领取过并且领取者不是发起人的话,就进行领邀请函处理
            // 领取一下
            $register_name = $this->modelMember->getRegisterName($userInfo);
            $invitationInfoGotDetail = $this->modelInvitationGotDetail->create($invitationId, $invitationInfo['user_id'], $invitationInfo['user_name'], $invitationInfo['user_headimgurl'], $userInfo['_id'], $register_name, $userInfo['avatar'], 0, 0, YUNGOU_ACTIVITY_ID, array(), array(
                'rand' => mt_rand(0, 2)
            ));
            // 邀請函领取次数也加1,同时价值加$worth
            $otherIncData = array();
            $otherUpdateData = array();
            $this->modelInvitation->incInvitedNum($invitationInfo, 1, 0, 0, $otherIncData, $otherUpdateData);
        }
    }

    private function registerMember($username, $email, $mobile, $password)
    {
        // 检查邀请ID存在否
        $invited_id = getCookieValue('invited_id');
        $invitationInfo = array();
        if (!empty($invited_id)) {
            $invitationInfo = $this->modelInvitation->getInfoById($invited_id);
            if (empty($invitationInfo)) {
                $invited_id = '';
                setCookieValue('invited_id', '', time() - 3600, '/');
            }
        }
        $now = time();
        try {
            $this->modelMember->begin();

            // 会员添加
            $memberInfo = $this->modelMember->register($username, $email, $mobile, $password, $invited_id);
            if (empty($memberInfo)) {
                throw new \Exception('会员生成失败', -80);
            }
            // 登录处理
            $this->loginMember($memberInfo, false);

            // 注册3个积分用户
            $memo = array(
                'member_id' => $memberInfo['_id'],
                'member_nickname' => $memberInfo['nickname'],
                'member_name' => $memberInfo['name'],
                'member_email' => $memberInfo['email'],
                'member_mobile' => $memberInfo['mobile'],
                'member_register_by' => $memberInfo['register_by']
            );
            $register_name = $this->modelMember->getRegisterName($memberInfo);
            $this->modelPointsUser->create(POINTS_CATEGORY1, $memberInfo['_id'], $register_name, $memberInfo['avatar'], 0, 0, 0, 0, $memo);
            $this->modelPointsUser->create(POINTS_CATEGORY2, $memberInfo['_id'], $register_name, $memberInfo['avatar'], 0, 0, 0, 0, $memo);
            $this->modelPointsUser->create(POINTS_CATEGORY3, $memberInfo['_id'], $register_name, $memberInfo['avatar'], 0, 0, 0, 0, $memo);

            // 注册获得福分
            $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'register');
            $this->modelPointsService->addOrReduce(POINTS_CATEGORY1, $memberInfo['_id'], $register_name, $memberInfo['avatar'], $memberInfo['_id'], $now, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);

            // 生成邀请用户
            $invitationUserInfo = $this->modelInvitationUser->getOrCreateByUserId($memberInfo['_id'], $register_name, $memberInfo['avatar'], 0, 0, YUNGOU_ACTIVITY_ID, $memo);

            // 生成自己的邀请函
            $invitation_memo = array_merge($memo, array(
                'invitation_user_id' => $invitationUserInfo['_id']
            ));
            $myInvitationInfo = $this->modelInvitation->getOrCreateByUserId($memberInfo['_id'], $register_name, $memberInfo['avatar'], "", '云购', 0, 0, 0, 1, false, '', YUNGOU_ACTIVITY_ID, array(), $invitation_memo);
            // 生成个人消息记录
            $this->modelMsgCount->log($memberInfo['_id']);

            // 接受他人邀请处理
            $this->getInvitation($invitationInfo, $memberInfo);

            $this->modelMember->commit();
            return $memberInfo;
        } catch (\Exception $e) {
            $this->modelMember->rollback();
            throw $e;
        }
    }

    private function loginMember($memberInfo, $isLogin = true)
    {
        $this->modelMember->login($memberInfo, $isLogin);

        // 如果是登录的话,送积分
        if (true) {
            // // 添加会员积分
            // $this->addPoint($memberInfo);
            // // 添加会员经验值
            // $this->addExppoint($memberInfo);
        }

        // cookie中的cart存入数据库
        $this->serviceCart->mergeCart($memberInfo['_id']);
    }
}
