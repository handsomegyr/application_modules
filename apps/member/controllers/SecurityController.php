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
        // http://www.myapplicationmodule.com/member/security/index
    }

    /**
     * 修改密码页面
     */
    public function updatepasswordAction()
    {
        // http://www.myapplicationmodule.com/member/security/updatepassword
    }

    /**
     * 支付密码页面
     */
    public function setpaypwdAction()
    {
        // http://www.myapplicationmodule.com/member/security/setpaypwd
    }

    /**
     * 小额免密码设置页面
     */
    public function setsmallmoneyAction()
    {
        // http://www.myapplicationmodule.com/member/security/setsmallmoney
    }

    /**
     * 手机号或邮箱绑定页面
     */
    public function userauthAction()
    {
        // http://www.myapplicationmodule.com/member/security/userauth
    }

    /**
     * 手机号绑定页面
     */
    public function userauth10Action()
    {
        // http://www.myapplicationmodule.com/member/security/userauth10
    }

    /**
     * 手机号绑定页面
     */
    public function userauth40Action()
    {
        // http://www.myapplicationmodule.com/member/security/userauth40
    }

    /**
     * 邮箱绑定页面
     */
    public function userauth50Action()
    {
        // http://www.myapplicationmodule.com/member/security/userauth50
    }
}

