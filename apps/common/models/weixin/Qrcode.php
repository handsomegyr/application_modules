<?php
namespace Webcms\Common\Models\Weixin;

use Webcms\Common\Models\Base;

/**
 * 记录微信二维码扫描状况
 */
class Qrcode extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Weixin\Qrcode());
    }
}