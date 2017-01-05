<?php
namespace App\Common\Models\Member\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Member extends Base
{

    /**
     * 会员-会员管理
     * This model is mapped to the table imember_member
     */
    public function getSource()
    {
        return 'imember_member';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['reg_time'] = $this->changeToMongoDate($data['reg_time']);
        $data['login_time'] = $this->changeToMongoDate($data['login_time']);
        $data['old_login_time'] = $this->changeToMongoDate($data['old_login_time']);
        $data['email_bind'] = $this->changeToBoolean($data['email_bind']);
        $data['mobile_bind'] = $this->changeToBoolean($data['mobile_bind']);
        $data['inform_allow'] = $this->changeToBoolean($data['inform_allow']);
        $data['is_buy'] = $this->changeToBoolean($data['is_buy']);
        $data['is_allowtalk'] = $this->changeToBoolean($data['is_allowtalk']);
        $data['state'] = $this->changeToBoolean($data['state']);
        
        $data['privacy'] = $this->changeToArray($data['privacy']);
        $data['noticesettings'] = $this->changeToArray($data['noticesettings']);
        $data['is_login_tip'] = $this->changeToBoolean($data['is_login_tip']);
        $data['is_smallmoney_open'] = $this->changeToBoolean($data['is_smallmoney_open']);
        
        return $data;
    }
}