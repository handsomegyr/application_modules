<?php
namespace App\Common\Models\Mysql\Weixin;

use App\Common\Models\Mysql\Base;

class Qrcode extends Base
{

    /**
     * 微信二维码推广场景数据
     * This model is mapped to the table iweixin_qrcode
     */
    public function getSource()
    {
        return 'iweixin_qrcode';
    }
}