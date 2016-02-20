<?php
namespace Webcms\Member\Controllers;

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
        // http://webcms.didv.cn/member/passport/login
        $account = $this->get('account'); // 手机号或邮箱地址
        $this->assign('account', $account);
    }


    /**
     * 会员注销操作
     */
    public function logoutAction()
    {
        // http://webcms.didv.cn/member/passport/logout
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
        // http://webcms.didv.cn/member/passport/register
    }

    /**
     * 会员注册验证页面
     */
    public function registercheckAction()
    {
        // http://webcms.didv.cn/member/passport/registercheck
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
        // http://webcms.didv.cn/member/passport/findpassword
    }

    

    /**
     * 验证信息页面
     */
    public function findcheckAction()
    {
        // http://webcms.didv.cn/member/passport/findcheck
    }

    /**
     * 重设密码成功页面
     */
    public function findresetsuccessAction()
    {
        // http://webcms.didv.cn/member/passport/findresetsuccess
    }
    
    /**
     * QQ绑定页面
     */
    public function qcbindAction()
    {
        // http://webcms.didv.cn/member/passport/qcbind
        //$this->view->setRenderLevel(View::LEVEL_ACTION);
    }
}

