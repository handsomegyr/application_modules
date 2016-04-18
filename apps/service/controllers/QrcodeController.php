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
        // http://webcms.didv.cn/service/qrcode/create?url=http%3A%2F%2Fwww.baidu.com%2F
        error_reporting(E_ERROR);
        require_once APP_PATH . 'library/phpqrcode/qrlib.php';
        
        $url = $this->get('url', '');
        $url = urldecode($url);
        \QRcode::png($url, false, QR_ECLEVEL_L, 6);
    }
}

