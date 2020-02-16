<?php
namespace App\Weixin\Controllers;

use App\Weixin2\Models\Application;
use App\Weixin2\Models\ScriptTracking;
use App\Weixin2\Models\Callbackurls;

class SubscribemsgController extends ControllerBase
{

    protected $_app;

    protected $_config;

    protected $_tracking;

    protected $_appConfig;

    protected $_callbackurls;

    protected $appid;

    protected $trackingKey = "一次性订阅消息授权";

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        
        $this->_tracking = new ScriptTracking();
        $this->_callbackurls = new Callbackurls();
        
        $this->_config = $this->getDI()->get('config');
        $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : $this->_config['weixin']['appid'];
        
        $this->doInitializeLogic();
    }

    /**
     * http://www.example.com/weixin/subscribemsg/index?redirect=https%3A%2F%2Fwww.baidu.com%2F&scene=1000&template_id=Hjp6-fcMDvTpZ0JQKcaZ-rygj7L5FpF1JpQHS3acssM&reserved=xxxx&appid=wx907e2f9c52a7df08&dc=1
     * 引导用户去往登录授权
     */
    public function indexAction()
    {
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            $redirect = isset($_GET['redirect']) ? trim(trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
            $dc = isset($_GET['dc']) ? intval($_GET['dc']) : 0; // 是否检查回调域名
            $reserved = isset($_GET['reserved']) ? trim($_GET['reserved']) : uniqid();
            $scene = isset($_GET['scene']) ? trim($_GET['scene']) : '0';
            $template_id = isset($_GET['template_id']) ? trim($_GET['template_id']) : '';
            
            if (empty($redirect)) {
                die('回调地址未指定');
            }
            
            if (empty($template_id)) {
                die('模板消息未指定');
            }
            
            if ($dc) {
                // 添加重定向域的检查
                $isValid = $this->_callbackurls->isValid($redirect);
                if (empty($isValid)) {
                    die('回调地址不合法');
                }
            }
            
            $_SESSION['subscribemsg_redirect'] = $redirect;
            
            $moduleName = 'weixin';
            $controllerName = $this->controllerName;
            
            $scheme = $this->getRequest()->getScheme();
            $redirectUri = $scheme . '://';
            $redirectUri .= $_SERVER["HTTP_HOST"];
            $redirectUri .= '/' . $moduleName;
            $redirectUri .= '/' . $controllerName;
            $redirectUri .= '/callback';
            $redirectUri .= '?appid=' . $this->appid;
            
            $client = new \Weixin\Client();
            $url = $client->getMsgManager()
                ->getTemplateSender()
                ->getAuthorizeUrl4Subscribemsg($this->appid, $template_id, $scene, $redirectUri, $reserved, 'get_confirm');
            
            header("location:{$url}");
            exit();
        } catch (\Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    /**
     * 处理微信的回调数据
     *
     * @return boolean
     */
    public function callbackAction()
    {
        try {
            $redirect = empty($_SESSION['subscribemsg_redirect']) ? '' : $_SESSION['subscribemsg_redirect'];
            if (empty($redirect)) {
                throw new \Exception("回调地址未定义");
            }
            
            $openid = ! empty($_GET['openid']) ? $_GET['openid'] : null;
            $template_id = ! empty($_GET['template_id']) ? $_GET['template_id'] : null;
            $action = ! empty($_GET['action']) ? $_GET['action'] : null;
            $scene = ! empty($_GET['scene']) ? $_GET['scene'] : null;
            $reserved = ! empty($_GET['reserved']) ? $_GET['reserved'] : null;
            
            // 用户点击动作，”confirm”代表用户确认授权，”cancel”代表用户取消授权
            if ($action == "confirm") {}
            $redirect = $this->addUrlParameter($appid, array(
                'subscribemsg_appid' => $appid
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'subscribemsg_action' => $action
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'subscribemsg_FromUserName' => $openid
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'subscribemsg_template_id' => $template_id
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'subscribemsg_scene' => $scene
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'subscribemsg_reserved' => $reserved
            ));
            
            // 计算signkey
            $timestamp = time();
            $signkey = $this->getSignKey($openid . "|" . $action . "|" . $template_id . "|" . $scene . "|" . $reserved, $timestamp);
            $redirect = $this->addUrlParameter($redirect, array(
                'subscribemsg_signkey' => $signkey
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'subscribemsg_timestamp' => $timestamp
            ));
            
            header("location:{$redirect}");
            // 调整数据库操作的执行顺序，优化跳转速度
            fastcgi_finish_request();
            
            $this->_tracking->record($this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
            
            $objService = \App\Weixin\Services\Base::getServiceObject();
            $objService->doSnsCallback($arrAccessToken);
            
            exit();
        } catch (\Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    protected function addUrlParameter($url, array $params)
    {
        if (! empty($params)) {
            foreach ($params as $key => $value) {
                //if (strpos($url, $key) === false || ($key == "FromUserName")) {
				if (strpos($url, '?') === false)
					$url .= "?{$key}=" . $value;
				else
					$url .= "&{$key}=" . $value;
                //}
            }
        }
        return $url;
    }

    protected function getSignKey($openid, $timestamp = 0)
    {
        return $this->_app->getSignKey($openid, $this->_appConfig['secretKey'], $timestamp);
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        $this->_app = new Application();
        $this->_appConfig = $this->_app->getApplicationInfoByAppId($this->appid);
        
        if (empty($this->_appConfig)) {
            throw new \Exception('appid所对应的记录不存在');
        }
    }
}

