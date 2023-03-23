<?php

namespace App\Weixin2\Controllers;

/**
 * 应用服务
 */
class ThirdpartyController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 1;
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

        $this->modelWeixinopenSnsApplication = new \App\Weixin2\Models\SnsApplication();

        $_SESSION['service_start_time'] = microtime(true);
    }

    /**
     * 获取token信息
     */
    public function getTokenInfoAction()
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
            $ret['jsapi_ticket'] = $authorizerInfo['jsapi_ticket'];
            $ret['jsapi_ticket_expire'] = $authorizerInfo['jsapi_ticket_expire'];
            $ret['wx_card_api_ticket'] = $authorizerInfo['wx_card_api_ticket'];
            $ret['wx_card_api_ticket_expire'] = $authorizerInfo['wx_card_api_ticket_expire'];

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

        // 如果开启了IP检查
        $isIpValid = $this->modelWeixinopenSnsApplication->checkIsIpValid($this->appConfig, getIp());
        if (empty($isIpValid)) {
            throw new \Exception("不是合法的IP");
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
