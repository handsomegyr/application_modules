<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Event extends Base
{

    /**
     * 微信卡券-事件推送
     * This model is mapped to the table iweixincard_event
     */
    public function getSource()
    {
        return 'iweixincard_event';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['IsGiveByFriend'] = $this->changeToBoolean($data['IsGiveByFriend']);
        $data['CreateTime'] = $this->changeToMongoDate($data['CreateTime']);
        return $data;
    }
}