<?php
namespace Webcms\Common\Models\Mysql\Points;

use Webcms\Common\Models\Mysql\Base;

class User extends Base
{

    /**
     * 积分-积分用户表管理
     * This model is mapped to the table ipoints_user
     */
    public function getSource()
    {
        return 'ipoints_user';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['point_time'] = $this->changeToMongoDate($data['point_time']);
        return $data;
    }
}