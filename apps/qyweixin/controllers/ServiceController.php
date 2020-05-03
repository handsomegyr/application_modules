<?php

namespace App\Qyweixin\Controllers;

/**
 * 应用服务
 */
class ServiceController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 6;

    /**
     *
     * @var \App\Qyweixin\Models\ScriptTracking
     */
    private $modelQyweixinScriptTracking;

    private $modelQyweixinCallbackurls;

    private $modelQyweixinSnsApplication;

    // lock key
    private $lock_key_prefix = 'qyweixin_application_service_';

    private $cookie_session_key = 'qyweixin_application_service_';

    private $sessionKey;

    private $trackingKey = "授权应用服务调用";

    private $appid;

    private $appConfig;

    private $provider_appid;

    private $authorizer_appid;

    private $agentid = 0;

    private $weixinopenService = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();

        $this->modelQyweixinScriptTracking = new \App\Qyweixin\Models\ScriptTracking();
        $this->modelQyweixinCallbackurls = new \App\Qyweixin\Models\Callbackurls();
        $this->modelQyweixinSnsApplication = new \App\Qyweixin\Models\SnsApplication();

        $_SESSION['service_start_time'] = microtime(true);
    }

    /**
     * 获取access token信息
     */
    public function getAccessTokenAction()
    {
        // http://wxcrm.eintone.com/qyweixin/api/service/get-access-token?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/service/get-access-token?appid=4m9QOrJMzAjpx75Y
        try {
            // 初始化
            $this->doInitializeLogic();

            $authorizerInfo = $this->weixinopenService->getAppConfig4Authorizer();

            $ret = array();
            $ret['access_token'] = $authorizerInfo['access_token'];
            $ret['access_token_expire'] = $authorizerInfo['access_token_expire'];

            $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['service_start_time'], microtime(true), "getAccessToken", $this->appConfig['_id']);
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
        // http://wxcrm.eintone.com/qyweixin/api/service/get-jsapi-ticket?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/service/get-jsapi-ticket?appid=4m9QOrJMzAjpx75Y
        try {
            // 初始化
            $this->doInitializeLogic();

            $authorizerInfo = $this->weixinopenService->getAppConfig4Authorizer();

            $ret = array();
            $ret['jsapi_ticket'] = $authorizerInfo['jsapi_ticket'];
            $ret['jsapi_ticket_expire'] = $authorizerInfo['jsapi_ticket_expire'];

            $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['service_start_time'], microtime(true), "getJsapiTicket", $this->appConfig['_id']);
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
        // http://wxcrm.eintone.com/qyweixin/api/service/get-wxcardapi-ticket?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/service/get-wxcardapi-ticket?appid=4m9QOrJMzAjpx75Y
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

            $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['service_start_time'], microtime(true), "getWxcardapiTicket", $this->appConfig['_id']);
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

        $this->appConfig = $this->modelQyweixinSnsApplication->getInfoByAppid($this->appid);
        if (empty($this->appConfig)) {
            throw new \Exception("appid:{$this->appid}所对应的记录不存在");
        }

        $isValid = $this->modelQyweixinSnsApplication->checkIsValid($this->appConfig, $this->now);
        if (empty($isValid)) {
            throw new \Exception("appid:{$this->appid}所对应的记录已无效");
        }

        // 第三方服务商运用ID
        $this->provider_appid = $this->appConfig['provider_appid'];
        if (empty($this->provider_appid)) {
            throw new \Exception("provider_appid为空");
        }
        // 授权方ID
        $this->authorizer_appid = $this->appConfig['authorizer_appid'];
        if (empty($this->authorizer_appid)) {
            throw new \Exception("authorizer_appid为空");
        }
        // 创建service
        $this->weixinopenService = new \App\Qyweixin\Services\WeixinService($this->authorizer_appid, $this->provider_appid);
    }
}
