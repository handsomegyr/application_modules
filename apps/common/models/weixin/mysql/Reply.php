<?php
namespace Webcms\Common\Models\Mysql\Weixin;

use Webcms\Common\Models\Mysql\Base;

class Reply extends Base
{

    /**
     * 微信自动回复设定
     * This model is mapped to the table iweixin_reply
     */
    public function getSource()
    {
        return 'iweixin_reply';
    }
}