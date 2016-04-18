<?php
namespace App\Member\Controllers;

/**
 * 账户安全
 *
 * @author Kan
 *        
 */
class SecurityController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 账户安全首页
     */
    public function indexAction()
    {
        // http://webcms.didv.cn/member/security/index
    }

    /**
     * 修改密码页面
     */
    public function updatepasswordAction()
    {
        // http://webcms.didv.cn/member/security/updatepassword
    }

    /**
     * 支付密码页面
     */
    public function setpaypwdAction()
    {
        // http://webcms.didv.cn/member/security/setpaypwd
    }

    /**
     * 小额免密码设置页面
     */
    public function setsmallmoneyAction()
    {
        // http://webcms.didv.cn/member/security/setsmallmoney
    }

    /**
     * 手机号或邮箱绑定页面
     */
    public function userauthAction()
    {
        // http://webcms.didv.cn/member/security/userauth
    }

    /**
     * 手机号绑定页面
     */
    public function userauth10Action()
    {
        // http://webcms.didv.cn/member/security/userauth10
    }

    /**
     * 手机号绑定页面
     */
    public function userauth40Action()
    {
        // http://webcms.didv.cn/member/security/userauth40
    }

    /**
     * 邮箱绑定页面
     */
    public function userauth50Action()
    {
        // http://webcms.didv.cn/member/security/userauth50
    }
}

