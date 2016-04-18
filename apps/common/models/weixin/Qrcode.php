<?php
namespace App\Common\Models\Weixin;

use App\Common\Models\Base;

/**
 * 记录微信二维码扫描状况
 */
class Qrcode extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Weixin\Qrcode());
    }
}