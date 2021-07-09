<?php

namespace App\Weixin2\Controllers;

/**
 * 一次性订阅消息授权
 */
class SubscribemsgController extends ControllerBase
{

    // 活动ID
    protected $activity_id = 1;

    /**
     * @var \App\Weixin2\Models\ScriptTracking
     */
    private $modelWeixinopenScriptTracking;

    /**
     * @var \App\Weixin2\Models\Callbackurls
     */
    private $modelWeixinopenCallbackurls;

    /**
     * @var \App\Weixin2\Models\SnsApplication
     */
    private $modelWeixinopenSnsApplication;

    /**
     * @var \App\Weixin2\Models\SubscribeMsg\SubscribeLog
     */
    private $modelWeixinopenSubscribeMsgSubscribeLog;

    // lock key
    private $lock_key_prefix = 'weixinopen_subscribemsg_';

    // private $cookie_session_key = 'weixinopen_subscribemsg_';

    // private $sessionKey;

    private $trackingKey = "一次性订阅消息授权";

    private $appid;

    private $appConfig;

    private $component_appid;

    // private $componentConfig;

    private $authorizer_appid;

    // private $authorizerConfig;

    private $scope;

    private $reserved;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();

        $this->modelWeixinopenScriptTracking = new \App\Weixin2\Models\ScriptTracking();
        $this->modelWeixinopenCallbackurls = new \App\Weixin2\Models\Callbackurls();
        $this->modelWeixinopenSnsApplication = new \App\Weixin2\Models\SnsApplication();
        $this->modelWeixinopenSubscribeMsgSubscribeLog = new \App\Weixin2\Models\SubscribeMsg\SubscribeLog();
    }

    /**
     * 第一步：需要用户同意授权，获取一次给用户推送一条订阅模板消息的机会
     *
     * 在确保微信公众帐号拥有订阅消息授权的权限的前提下（已认证的公众号即有权限，可登陆公众平台在接口权限列表处查看），引导用户在微信客户端打开如下链接：
     *
     * https://mp.weixin.qq.com/mp/subscribemsg?action=get_confirm&appid=wxaba38c7f163da69b&scene=1000&template_id=1uDxHNXwYQfBmXOfPJcjAS3FynHArD8aWMEFNRGSbCc&redirect_url=http%3a%2f%2fsupport.qq.com&reserved=test#wechat_redirect
     * 参数说明
     *
     * 参数 是否必须 说明
     * action 是 直接填get_confirm即可
     * appid 是 公众号的唯一标识
     * scene 是 重定向后会带上scene参数，开发者可以填0-10000的整形值，用来标识订阅场景值
     * template_id 是 订阅消息模板ID，登录公众平台后台，在接口权限列表处可查看订阅模板ID
     * redirect_url 是 授权后重定向的回调地址，请使用UrlEncode对链接进行处理。 注：要求redirect_url的域名要跟登记的业务域名一致，且业务域名不能带路径。 业务域名需登录公众号，在设置-公众号设置-功能设置里面对业务域名设置。
     * reserved 否 用于保持请求和回调的状态，授权请后原样带回给第三方。该参数可用于防止csrf攻击（跨站请求伪造攻击），建议第三方带上该参数，可设置为简单的随机数加session进行校验，开发者可以填写a-zA-Z0-9的参数值，最多128字节，要求做urlencode
     * #wechat_redirect 是 无论直接打开还是做页面302重定向时，必须带此参数
     */
    public function indexAction()
    {
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/subscribemsg/index?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&reserved=qwerty&scope=snsapi_userinfo&refresh=1&scene=xxx&template_id=xxx
        // http://www.myapplicationmodule.com/weixinopen/api/subscribemsg/index?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&reserved=qwerty&scope=snsapi_userinfo&refresh=1&scene=xxx&template_id=xxx
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            // 初始化
            $this->doInitializeLogic();

            $redirect = isset($_GET['redirect']) ? (trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
            $reserved = isset($_GET['reserved']) ? trim($_GET['reserved']) : uniqid();
            $scene = isset($_GET['scene']) ? trim($_GET['scene']) : '0';
            $template_id = isset($_GET['template_id']) ? trim($_GET['template_id']) : '';

            $dc = empty($this->appConfig['is_cb_url_check']) ? 0 : 1; // 是否检查回调域名

            if ($dc) {
                // 添加重定向域的检查
                $isValid = $this->modelWeixinopenCallbackurls->isValid($this->authorizer_appid, $this->component_appid, $redirect);
                if (empty($isValid)) {
                    throw new \Exception("回调地址不合法");
                }
            }

            // 存储跳转地址
            $_SESSION['redirect'] = $redirect;
            $_SESSION['reserved'] = $this->reserved;
            // $_SESSION['scene'] = $this->scene;
            // $_SESSION['template_id'] = $this->template_id;
            $_SESSION['appid'] = $this->appid;

            $moduleName = 'weixin2';
            $controllerName = $this->controllerName;

            $scheme = $this->getRequest()->getScheme();
            $redirectUri = $scheme . '://';
            $redirectUri .= $_SERVER["HTTP_HOST"];
            $redirectUri .= '/' . $moduleName;
            $redirectUri .= '/' . $controllerName;
            $redirectUri .= '/callback';
            // $redirectUri .= '?appid=' . $this->appid;

            // 授权处理
            $client = new \Weixin\Client();
            $uri = $client->getMsgManager()
                ->getTemplateSender()
                ->getAuthorizeUrl4Subscribemsg($this->authorizer_appid, $template_id, $scene, $redirectUri, $reserved, 'get_confirm');

            header("location:{$uri}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 用户同意或取消授权后会返回相关信息
     *
     * 如果用户点击同意或取消授权，页面将跳转至：
     *
     * redirect_url/?openid=OPENID&template_id=TEMPLATE_ID&action=ACTION&scene=SCENE
     * 参数说明
     *
     * 参数 说明
     * openid 用户唯一标识，只在用户确认授权时才会带上
     * template_id 订阅消息模板ID
     * action 用户点击动作，”confirm”代表用户确认授权，”cancel”代表用户取消授权
     * scene 订阅场景值
     * reserved 请求带入原样返回
     */
    public function callbackAction()
    {
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/subscribemsg/callback?appid=xxx&code=xxx&scope=auth_user&reserved=xxx
        try {
            $appid4Sns = !empty($_GET['appid']) ? $_GET['appid'] : null;

            $appid = empty($_SESSION['appid']) ? "" : $_SESSION['appid'];
            if (empty($appid)) {
                throw new \Exception("appid未定义");
            }
            $_GET['appid'] = $appid;

            // 初始化
            $this->doInitializeLogic();

            $openid = !empty($_GET['openid']) ? $_GET['openid'] : null;
            $template_id = !empty($_GET['template_id']) ? $_GET['template_id'] : null;
            $action = !empty($_GET['action']) ? $_GET['action'] : null;
            $scene = !empty($_GET['scene']) ? $_GET['scene'] : null;
            $scene = intval($scene);
            $reserved = !empty($_GET['reserved']) ? $_GET['reserved'] : null;

            $redirect = empty($_SESSION['redirect']) ? '' : $_SESSION['redirect'];
            if (empty($redirect)) {
                throw new \Exception("回调地址未定义");
            }

            $reserved4Session = empty($_SESSION['reserved']) ? '' : $_SESSION['reserved'];
            if ($reserved != $reserved4Session) {
                throw new \Exception("reserved发生了改变");
            }

            // 用户点击动作，”confirm”代表用户确认授权，”cancel”代表用户取消授权
            if ($action == "confirm") {
                // 检查是否锁定，如果没有锁定加锁
                $lock = new \iLock($this->lock_key_prefix . $scene . $openid . $template_id . $this->authorizer_appid . $this->component_appid);
                if ($lock->lock()) {
                    throw new \Exception("上次操作还未完成,请等待");
                }

                // 查找是否有记录
                $msgInfo = $this->modelWeixinopenSubscribeMsgSubscribeLog->getInfoByOpenidAndTemplateIdAndScene($openid, $template_id, $scene, $this->authorizer_appid, $this->component_appid);

                // 如果没有的话日志记录
                if (empty($msgInfo)) {
                    $msgInfo = $this->modelWeixinopenSubscribeMsgSubscribeLog->log($this->component_appid, $this->authorizer_appid, $appid4Sns, $openid, $template_id, $action, $scene, $reserved, $this->now);
                }
            }
            $redirect = $this->addUrlParameter($redirect, array(
                'it_subscribemsg_appid' => $appid4Sns
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'it_subscribemsg_action' => $action
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'it_subscribemsg_FromUserName' => $openid
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'it_subscribemsg_template_id' => $template_id
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'it_subscribemsg_scene' => $scene
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'it_subscribemsg_reserved' => $reserved
            ));
            if (!empty($msgInfo)) {
                $redirect = $this->addUrlParameter($redirect, array(
                    'it_subscribemsg_id' => $msgInfo['_id']
                ));
            }
            // 计算signkey
            $timestamp = $this->now;
            $signkey = $this->getSignKey($openid . "|" . $action . "|" . $template_id . "|" . $scene . "|" . $reserved, $timestamp);
            $redirect = $this->addUrlParameter($redirect, array(
                'it_subscribemsg_sk' => $signkey
            ));
            $redirect = $this->addUrlParameter($redirect, array(
                'it_subscribemsg_ts' => $timestamp
            ));
            $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $openid, $this->appConfig['_id']);
            header("location:{$redirect}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    protected function addUrlParameter($url, array $params)
    {
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (strpos($url, $key) === false) {
                    if (strpos($url, '?') === false)
                        $url .= "?{$key}=" . $value;
                    else
                        $url .= "&{$key}=" . $value;
                }
            }
        }
        return $url;
    }

    protected function getSignKey($openid, $timestamp = 0)
    {
        return $this->modelWeixinopenSnsApplication->getSignKey($openid, $this->appConfig['secretKey'], $timestamp);
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        // 应用ID
        $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : "";
        if (empty($this->appid)) {
            throw new \Exception("appid为空");
        }

        $this->appConfig = $this->modelWeixinopenSnsApplication->getInfoByAppid($this->appid);
        if (empty($this->appConfig)) {
            throw new \Exception("appid:{$this->appid}所对应的记录不存在");
        }

        $isValid = $this->modelWeixinopenSnsApplication->checkIsValid($this->appConfig, $this->now);
        if (empty($isValid)) {
            throw new \Exception("appid:{$this->appid}所对应的记录已无效");
        }
        // 第三方平台运用ID
        $this->component_appid = $this->appConfig['component_appid'];
        // if (empty($this->component_appid)) {
        //     throw new \Exception("component_appid为空");
        // }

        // 授权方ID
        $this->authorizer_appid = $this->appConfig['authorizer_appid'];
        if (empty($this->authorizer_appid)) {
            throw new \Exception("authorizer_appid为空");
        }
    }
}
