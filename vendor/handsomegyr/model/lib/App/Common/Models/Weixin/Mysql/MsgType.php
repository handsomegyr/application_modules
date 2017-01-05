<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

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