<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class CardBag extends Base
{

    /**
     * 微信卡券-卡包
     * This model is mapped to the table iweixincard_card_bag
     */
    public function getSource()
    {
        return 'iweixincard_card_bag';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['IsGiveByFriend'] = $this->changeToBoolean($data['IsGiveByFriend']);
        
        $data['is_got'] = $this->changeToBoolean($data['is_got']);
        $data['got_time'] = $this->changeToMongoDate($data['got_time']);
        $data['is_consumed'] = $this->changeToBoolean($data['is_consumed']);
        $data['consume_time'] = $this->changeToMongoDate($data['consume_time']);
        $data['is_deleted'] = $this->changeToBoolean($data['is_deleted']);
        $data['delete_time'] = $this->changeToMongoDate($data['delete_time']);
        $data['is_unavailable'] = $this->changeToBoolean($data['is_unavailable']);
        $data['unavailable_time'] = $this->changeToMongoDate($data['unavailable_time']);
        return $data;
    }
}