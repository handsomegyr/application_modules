<?php
namespace App\Common\Models\Message\Mysql;

use App\Common\Models\Base\Mysql\Base;

class ReplyMsg extends Base
{

    /**
     * 消息-回复消息表管理
     * This model is mapped to the table imessage_replymsg
     */
    public function getSource()
    {
        return 'imessage_replymsg';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['msg_time'] = $this->changeToMongoDate($data['msg_time']);
        $data['is_read'] = $this->changeToBoolean($data['is_read']);
        return $data;
    }
}