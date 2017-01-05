<?php
namespace App\Common\Models\Message\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Msg extends Base
{

    /**
     * 消息-消息表管理
     * This model is mapped to the table imessage_msg
     */
    public function getSource()
    {
        return 'imessage_msg';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['msg_time'] = $this->changeToMongoDate($data['msg_time']);
        return $data;
    }
}