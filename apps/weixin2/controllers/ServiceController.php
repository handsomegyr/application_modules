<?php

namespace App\Weixin2\Controllers;

/**
 * 应用服务
 */
class ServiceController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 1;

    /**
     * @var \App\Weixin2\Models\ScriptTracking
     */
    private $modelWeixinopenScriptTracking;
    /**
     * @var \App\Weixin2\Models\SnsApplication
     */
    private $modelWeixinopenSnsApplication;

    // // lock key
    // private $lock_key_prefix = 'weixinopen_application_service_';

    // private $cookie_session_key = 'weixinopen_application_service_';

    // private $sessionKey;

    private $trackingKey = "授权应用服务调用";

    private $appid;

    private $appConfig;

    private $component_appid;

    private $authorizer_appid;

    private $weixinopenService = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();

        $this->modelWeixinopenScriptTracking = new \App\Weixin2\Models\ScriptTracking();
        $this->modelWeixinopenSnsApplication = new \App\Weixin2\Models\SnsApplication();

        $_SESSION['service_start_time'] = microtime(true);
    }

    /**
     * 获取access token信息
     */
    public function getAccessTokenAction()
    {
        // http://www.myapplicationmodule.com/weixinopen/api/service/get-access-token?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/service/get-access-token?appid=4m9QOrJMzAjpx75Y
        try {
            // 初始化
            $this->doInitializeLogic();

            $authorizerInfo = $this->weixinopenService->getAppConfig4Authorizer();

            $ret = array();
            $ret['access_token'] = $authorizerInfo['access_token'];
            $ret['access_token_expire'] = $authorizerInfo['access_token_expire'];

            $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid, $this->trackingKey, $_SESSION['service_start_time'], microtime(true), "getAccessToken", $this->appConfig['_id']);
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 获取JsapiTicket信息
     */
    public function getJsapiTicketAction()
    {
        // http://www.myapplicationmodule.com/weixinopen/api/service/get-jsapi-ticket?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/service/get-jsapi-ticket?appid=4m9QOrJMzAjpx75Y
        try {
            // 初始化
            $this->doInitializeLogic();

            $authorizerInfo = $this->weixinopenService->getAppConfig4Authorizer();

            $ret = array();
            $ret['jsapi_ticket'] = $authorizerInfo['jsapi_ticket'];
            $ret['jsapi_ticket_expire'] = $authorizerInfo['jsapi_ticket_expire'];

            $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid, $this->trackingKey, $_SESSION['service_start_time'], microtime(true), "getJsapiTicket", $this->appConfig['_id']);
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 获取wx_card_api_ticket信息
     */
    public function getWxcardapiTicketAction()
    {
        // http://www.myapplicationmodule.com/weixinopen/api/service/get-wxcardapi-ticket?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/service/get-wxcardapi-ticket?appid=4m9QOrJMzAjpx75Y
        try {
            // 初始化
            $this->doInitializeLogic();

            $authorizerInfo = $this->weixinopenService->getAppConfig4Authorizer();
            if (empty($authorizerInfo['is_weixin_card'])) {
                return $this->error(50000, "未授权获取微信卡券的api ticket");
            }

            $ret = array();
            $ret['wx_card_api_ticket'] = $authorizerInfo['wx_card_api_ticket'];
            $ret['wx_card_api_ticket_expire'] = $authorizerInfo['wx_card_api_ticket_expire'];

            $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid, $this->trackingKey, $_SESSION['service_start_time'], microtime(true), "getWxcardapiTicket", $this->appConfig['_id']);
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 发送小程序统一消息
     */
    public function uniformSendAction()
    {
        // http://www.myapplicationmodule.com/weixinopen/api/service/uniform-send?appid=4m9QOrJMzAjpx75Y&touser=&mp_template_msg={}
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/service/uniform-send?appid=4m9QOrJMzAjpx75Y&touser=&mp_template_msg={}
        try {
            $touser = isset($_GET['touser']) ? (trim($_GET['touser'])) : '';
            if (empty($touser)) {
                return $this->error(50000, "touser is empty");
            }
            $mp_template_msg = isset($_GET['mp_template_msg']) ? (trim($_GET['mp_template_msg'])) : '';
            if (empty($mp_template_msg)) {
                return $this->error(50000, "mp_template_msg is empty");
            }
            /**
             * "mp_template_msg":{
             *      "appid":"APPID ",
             *      "template_id":"TEMPLATE_ID",
             *      "url":"http://weixin.qq.com/download",
             *      "miniprogram":{
             *          "appid":"xiaochengxuappid12345",
             *          "pagepath":"index?foo=bar"
             *      },
             *      "data":{
             *          "first":{
             *             "value":"恭喜你购买成功！",
             *             "color":"#173177"
             *          },
             *          "keyword1":{
             *             "value":"巧克力",
             *             "color":"#173177"
             *          },
             *          "keyword2":{
             *             "value":"39.8元",
             *             "color":"#173177"
             *          },
             *          "keyword3":{
             *             "value":"2014年9月22日",
             *             "color":"#173177"
             *          },
             *          "remark":{
             *             "value":"欢迎再次购买！",
             *             "color":"#173177"
             *          }
             *      }
             *  }
             */
            $mp_template_msg = \json_decode($mp_template_msg, true);
            if (empty($mp_template_msg)) {
                return $this->error(50000, "mp_template_msg is not correct");
            }

            // 初始化
            $this->doInitializeLogic();
            $weapp_template_msg = array();
            $ret = $this->weixinopenService->uniformSend($touser, $mp_template_msg, $weapp_template_msg);
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
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
        // 创建service
        $this->weixinopenService = new \App\Weixin2\Services\WeixinService($this->authorizer_appid, $this->component_appid);
    }
}
