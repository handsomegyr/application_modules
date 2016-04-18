<?php
namespace App\Common\Models\Mysql\Weixin;

use App\Common\Models\Mysql\Base;

class User extends Base
{

    /**
     * 微信用户
     * This model is mapped to the table iweixin_user
     */
    public function getSource()
    {
        return 'iweixin_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        
        $data['subscribe_time'] = $this->changeToMongoDate($data['subscribe_time']);
        $data['subscribe'] = $this->changeToBoolean($data['subscribe']);
        $data['privilege'] = $this->changeToArray($data['privilege']);
        $data['access_token'] = $this->changeToArray($data['access_token']);
        
        return $data;
    }
}