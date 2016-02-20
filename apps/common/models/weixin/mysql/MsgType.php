<?php
namespace Webcms\Common\Models\Mysql\Weixin;

use Webcms\Common\Models\Mysql\Base;

class MsgType extends Base
{

    /**
     * 微信消息类型
     * This model is mapped to the table iweixin_msg_type
     */
    public function getSource()
    {
        return 'iweixin_msg_type';
    }
}