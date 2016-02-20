<?php
namespace Webcms\Common\Models\Mysql\Weixin;

use Webcms\Common\Models\Mysql\Base;

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