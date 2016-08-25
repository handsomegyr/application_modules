<?php

/**
 * 代公众号发起网页授权
 * @author Administrator
 *
 */
namespace App\Weixin\Controllers;

use App\Weixin\Models\ComponentApplication;

class ComponentsnsController extends SnsController
{

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        $this->controllerName = 'componentsns';
        $this->cookie_session_key = "iWeixinComponent";
        $this->trackingKey = "代公众号发起网页授权";
        $this->_app = new ComponentApplication();
        $this->_appConfig = $this->_app->getApplicationInfoByAuthorizerAppId($this->appid);
        if (empty($this->_appConfig)) {
            throw new \Exception('appid所对应的记录不存在');
        }
    }

    protected function getAuthorizeUrl($redirectUri)
    {
        $component_appid = $this->_appConfig['appid'];
        $component_access_token = $this->_appConfig['component_access_token'];
        $appid = $this->appid;
        
        $sns = new \Weixin\Token\Component($appid, $component_appid, $component_access_token);
        $sns->setRedirectUri($redirectUri);
        $sns->setScope($this->scope);
        $sns->getAuthorizeUrl();
    }

    protected function getAccessToken()
    {
        $component_appid = $this->_appConfig['appid'];
        $component_access_token = $this->_appConfig['component_access_token'];
        $appid = $this->appid;
        $sns = new \Weixin\Token\Component($appid, $component_appid, $component_access_token);
        $arrAccessToken = $sns->getAccessToken();
        return $arrAccessToken;
    }
}

