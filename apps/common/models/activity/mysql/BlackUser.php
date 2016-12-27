<?php
namespace App\Common\Models\Activity\Mysql;

use App\Common\Models\Base\Mysql\Base;

class BlackUser extends Base
{

    /**
     * 活动-黑名单用户表管理
     * This model is mapped to the table iactivity_black_user
     */
    public function getSource()
    {
        return 'iactivity_black_user';
    }
}