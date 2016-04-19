<?php
namespace App\Common\Models\Message\Mysql;

use App\Common\Models\Base\Mysql\Base;

class SysMsg extends Base
{

    /**
     * 消息-系统消息表管理
     * This model is mapped to the table imessage_sysmsg
     */
    public function getSource()
    {
        return 'imessage_sysmsg';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['msg_time'] = $this->changeToMongoDate($data['msg_time']);
        $data['is_read'] = $this->changeToBoolean($data['is_read']);        
        return $data;
    }
}