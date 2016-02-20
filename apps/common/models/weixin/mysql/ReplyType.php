<?php
namespace Webcms\Common\Models\Mysql\Weixin;

use Webcms\Common\Models\Mysql\Base;

class ReplyType extends Base
{

    /**
     * 微信回复类型
     * This model is mapped to the table iweixin_reply_type
     */
    public function getSource()
    {
        return 'iweixin_reply_type';
    }
}