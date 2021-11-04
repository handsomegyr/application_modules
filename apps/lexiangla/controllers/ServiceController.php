<?php

namespace App\Lexiangla\Controllers;

/**
 * 应用服务
 */
class ServiceController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 1;

    /**
     * @var \App\Lexiangla\Services\LexianglaService
     */
    private $serviceLexiangla = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();

        // 创建service
        $config = $this->getDI()->get('config');
        $lexianglaSettings = $config['lexiangla'];
        $this->serviceLexiangla = new \App\Lexiangla\Services\LexianglaService($lexianglaSettings['AppKey'], $lexianglaSettings['AppSecret']);
    }

    /**
     * 获取access token信息
     */
    public function getAccessTokenAction()
    {
        // http://www.myapplicationmodule.com/lexiangla/api/service/get-access-token
        try {
            $access_token = $this->serviceLexiangla->getAccessToken();

            $ret = array();
            $ret['access_token'] = $access_token;
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }
}
