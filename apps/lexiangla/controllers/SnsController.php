<?php

namespace App\Lexiangla\Controllers;

/**
 * 应用授权
 */
class SnsController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 1;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 登录
     */
    public function loginAction()
    {
        // http://www.myapplicationmodule.com/lexiangla/api/sns/login?&redirect_uri=https%3A%2F%2Flexiangla.com%2Fsuites%2Fauth-callback&state=STATE
        try {
            $redirect_uri = isset($_GET['redirect_uri']) ? (trim($_GET['redirect_uri'])) : '';
            $state = isset($_GET['state']) ? (trim($_GET['state'])) : '';
            $code = "";
            if (empty($_SESSION['oauth_start_time'])) {
                $code = $_SESSION['lexiangla_code'];
            }
            if (!empty($code)) {
                $redirect_uri = $redirect_uri . "?state={$state}&code={$code}";
                header("location:{$redirect_uri}");
                exit();
            } else {
                // 存储跳转地址
                $_SESSION['redirect_uri'] = $redirect_uri;
                $_SESSION['state'] = $state;

                $scheme = $this->getRequest()->getScheme();
                $redirectUri = $scheme . '://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/';

                // 进行企业微信授权处理
                $redirect = $redirectUri . ('lexiangla/api/sns/callback');
                //qyweixin/api/applicationsns/snsauthorize?appid=4O9dl1v24jEmXJRD&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_base&refresh=1
                $redirectUri = $redirectUri . ("qyweixin/api/applicationsns/snsauthorize?appid=4O9dl1v24jEmXJRD&state={$state}&scope=snsapi_base&redirect={$redirect}");

                header("location:{$redirectUri}");
                exit();
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 回调
     */
    public function callbackAction()
    {
        // http://www.myapplicationmodule.com/lexiangla/api/sns/callback?it_userid=xxx&it_signkey=xxxx&it_timestamp=xxx
        try {
            $it_userid = isset($_GET['it_userid']) ? (trim($_GET['it_userid'])) : '';
            // $code = isset($_GET['it_signkey']) ? (trim($_GET['it_signkey'])) : '';
            $code = sha1(myMongoId(new \MongoId()));
            if (!empty($it_userid) && !empty($code)) {
                $cache = $this->getDI()->get("cache");
                // 加缓存处理
                $cache->save($code, $it_userid, 1200);
                $_SESSION["lexiangla_code"] = $code;
            }
            $redirect_uri = $_SESSION["redirect_uri"];
            $state = $_SESSION["state"];
            $redirect_uri = $redirect_uri . "?state={$state}&code={$code}";
            header("location:{$redirect_uri}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 获取用户信息
     */
    public function getuserinfoAction()
    {
        // http://www.myapplicationmodule.com/lexiangla/api/sns/getuserinfo?code=xxxx
        $ret = array();
        $ret['id'] = '';
        try {
            $code = isset($_GET['code']) ? ($_GET['code']) : '';
            if (!empty($code)) {
                $cache = $this->getDI()->get("cache");
                $ret['id'] = $cache->get($code);
            }
        } catch (\Exception $e) {
        }
        echo \json_encode($ret);
        return;
    }
}
