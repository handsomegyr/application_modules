<?php
namespace Webcms\Common\Models\Mysql\Message;

use Webcms\Common\Models\Mysql\Base;

class MsgCount extends Base
{

    /**
     * 消息-消息数量管理
     * This model is mapped to the table imessage_msg_count
     */
    public function getSource()
    {
        return 'imessage_msg_count';
    }
}