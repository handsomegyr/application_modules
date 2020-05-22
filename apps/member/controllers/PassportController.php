<?php
namespace App\Member\Controllers;

use Phalcon\Mvc\View;

/**
 * 用户登录注册
 *
 * @author Kan
 *        
 */
class PassportController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->setRenderLevel(View::LEVEL_LAYOUT);
        $this->view->setVar("resourceUrl", "/member/passport/");
    }
    
    // -----------------------------------------------------用户登录和注册----------------------------------------------------------------
    /**
     * 会员登录页面
     */
    public function loginAction()
    {
        // http://www.applicationmodule.com/member/passport/login
        $account = $this->get('account'); // 手机号或邮箱地址
        $this->assign('account', $account);
    }

    /**
     * 会员注销操作
     */
    public function logoutAction()
    {
        // http://www.applicationmodule.com/member/passport/logout
        // 清理COOKIE
        $this->modelMember->clearCookies();
        $url = $this->getUrl("login");
        // $ref_url = $this->getRefererUrl();
        // if ($ref_url) {
        // $url .= '&ref_url=' . urlencode($ref_url);
        // }
        $this->_redirect($url);
    }

    /**
     * 会员注册页面
     */
    public function registerAction()
    {
        // http://www.applicationmodule.com/member/passport/register
    }

    /**
     * 会员注册验证页面
     */
    public function registercheckAction()
    {
        // http://www.applicationmodule.com/member/passport/registercheck
        $isLogin = $this->modelMember->checkloginMember();
        if (empty($isLogin)) {
            $url = $this->getUrl("register");
            $this->_redirect($url);
            exit();
        }
    }
    
    // -----------------------------------------------------找回密码----------------------------------------------------------------
    /**
     * 找回密码页面
     */
    public function findpasswordAction()
    {
        // http://www.applicationmodule.com/member/passport/findpassword
    }

    /**
     * 验证信息页面
     */
    public function findcheckAction()
    {
        // http://www.applicationmodule.com/member/passport/findcheck
    }

    /**
     * 重设密码成功页面
     */
    public function findresetsuccessAction()
    {
        // http://www.applicationmodule.com/member/passport/findresetsuccess
    }

    /**
     * QQ绑定页面
     */
    public function qcbindAction()
    {
        // http://www.applicationmodule.com/member/passport/qcbind
        // $this->view->setRenderLevel(View::LEVEL_ACTION);
        $userInfo = array();
        if (! empty($_SESSION['login_from'])) {
            if ($_SESSION['login_from'] == 'weixin') {
                $userInfo = $this->getWeixinUserInfo();
            } elseif ($_SESSION['login_from'] == 'tencent') {
                $userInfo = $this->getTencentUserInfo();
            }
        }
        if (empty($userInfo)) {
            die('非法访问');
        }
    }
}

