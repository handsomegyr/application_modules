<?php
namespace App\Common\Models\Invitation\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Invitation extends Base
{

    /**
     * 微信邀请-邀请函表管理
     * This model is mapped to the table iinvitation_invitation
     */
    public function getSource()
    {
        return 'iinvitation_invitation';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['send_time'] = $this->changeToMongoDate($data['send_time']);
        $data['expire'] = $this->changeToMongoDate($data['expire']);
        $data['lock'] = $this->changeToBoolean($data['lock']);
        $data['is_need_subscribed'] = $this->changeToBoolean($data['is_need_subscribed']);        
        return $data;
    }
}