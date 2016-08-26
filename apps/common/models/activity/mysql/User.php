<?php
namespace App\Common\Models\Activity\Mysql;

use App\Common\Models\Base\Mysql\Base;

class User extends Base
{

    /**
     * 活动-用户表管理
     * This model is mapped to the table iactivity_user
     */
    public function getSource()
    {
        return 'iactivity_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['log_time'] = $this->changeToMongoDate($data['log_time']);
        $data['expire'] = $this->changeToMongoDate($data['expire']);
        $data['lock'] = $this->changeToBoolean($data['lock']);
        return $data;
    }
}