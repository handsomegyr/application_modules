<?php
namespace App\Common\Models\Post\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Post extends Base
{
    
    /**
     * 帖子-帖子表管理
     * This model is mapped to the table ipost_post
     */
    public function getSource()
    {
        return 'ipost_post';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['post_time'] = $this->changeToMongoDate($data['post_time']);
        $data['goods_info'] = $this->changeToArray($data['goods_info']);
        $data['verify_time'] = $this->changeToMongoDate($data['verify_time']);
        
        return $data;
    }
}