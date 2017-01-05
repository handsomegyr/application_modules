<?php
namespace App\Common\Models\Weixincard\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Card extends Base
{

    /**
     * 微信卡券-卡券
     * This model is mapped to the table iweixincard_card
     */
    public function getSource()
    {
        return 'iweixincard_card';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['use_custom_code'] = $this->changeToBoolean($data['use_custom_code']);
        $data['bind_openid'] = $this->changeToBoolean($data['bind_openid']);
        $data['can_share'] = $this->changeToBoolean($data['can_share']);
        $data['can_give_friend'] = $this->changeToBoolean($data['can_give_friend']);
        $data['date_info_begin_timestamp'] = $this->changeToMongoDate($data['date_info_begin_timestamp']);
        $data['date_info_end_timestamp'] = $this->changeToMongoDate($data['date_info_end_timestamp']);
        $data['date_info_fixed_end_timestamp'] = $this->changeToMongoDate($data['date_info_fixed_end_timestamp']);
        $data['can_shake'] = $this->changeToBoolean($data['can_shake']);
        $data['member_card_supply_bonus'] = $this->changeToBoolean($data['member_card_supply_bonus']);
        $data['member_card_supply_balance'] = $this->changeToBoolean($data['member_card_supply_balance']);
        $data['member_card_need_push_on_view'] = $this->changeToBoolean($data['member_card_need_push_on_view']);
        
        return $data;
    }
}