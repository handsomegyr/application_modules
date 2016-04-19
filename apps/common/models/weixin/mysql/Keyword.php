<?php
namespace App\Common\Models\Weixin\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Keyword extends Base
{

    /**
     * 微信关键词设定
     * This model is mapped to the table iweixin_keyword
     */
    public function getSource()
    {
        return 'iweixin_keyword';
    }
    
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
    
        $data['fuzzy'] = $this->changeToBoolean($data['fuzzy']);
        $data['reply_ids'] = $this->changeToArray($data['reply_ids']);
    
        return $data;
    }
}