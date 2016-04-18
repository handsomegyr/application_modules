<?php
namespace App\Common\Models\Mysql\Weixin;

use App\Common\Models\Mysql\Base;

class Source extends Base
{

    /**
     * 微信原始数据
     * This model is mapped to the table iweixin_source
     */
    public function getSource()
    {
        return 'iweixin_source';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['coordinate'] = $this->changeToArray($data['coordinate']);
        $data['response_time'] = $this->changeToMongoDate($data['response_time']);
        
        return $data;
    }
}