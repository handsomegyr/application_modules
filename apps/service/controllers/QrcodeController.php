<?php

namespace App\Service\Controllers;

class QrcodeController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }

    /**
     * 提供生成2维码的服务
     */
    public function createAction()
    {
        // http://www.myapplicationmodule.com.com/service/qrcode/create?url=http%3A%2F%2Fwww.baidu.com%2F
        error_reporting(E_ERROR);
        require_once APP_PATH . 'library/phpqrcode/qrlib.php';

        $url = $this->get('url', '');
        $url = urldecode($url);
        \QRcode::png($url, false, QR_ECLEVEL_L, 6);
    }

    $url = 'http://www.baidu.com';
$api_url = 'http://lnurl.cn/tcn-api.json?key=hd3j2ryt&url=http://www.baidu.com;
$short_url = file_get_contents($api_url);
echo $short_url;

}
