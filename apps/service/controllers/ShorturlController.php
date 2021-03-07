<?php

namespace App\Service\Controllers;

class ShorturlController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 提供短地址服务
     */
    public function getAction()
    {
        // http://www.myapplicationmodule.com.com/service/shorturl/get?url=http%3A%2F%2Fwww.baidu.com%2F
        error_reporting(E_ERROR);
        $url = $this->get('url', '');
        $url = urldecode($url);
        $api_url = 'http://lnurl.cn/tcn-api.json?key=hd3j2ryt&url=' . $url;
        $short_url = file_get_contents($api_url);
        echo $short_url;
    }
}
