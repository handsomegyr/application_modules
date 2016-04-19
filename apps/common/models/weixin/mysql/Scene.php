<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Scene extends Base
{

    /**
     * 微信二维码场景管理
     * This model is mapped to the table iweixin_scene
     */
    public function getSource()
    {
        return 'iweixin_scene';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        
        $data['is_temporary'] = $this->changeToBoolean($data['is_temporary']);
        $data['is_created'] = $this->changeToBoolean($data['is_created']);
        $data['ticket_time'] = $this->changeToMongoDate($data['ticket_time']);
        
        return $data;
    }
}