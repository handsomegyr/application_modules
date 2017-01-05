<?php
namespace App\Common\Models\Post\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Reply extends Base
{

    /**
     * 帖子-回复管理
     * This model is mapped to the table ipost_reply
     */
    public function getSource()
    {
        return 'ipost_reply';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['reply_time'] = $this->changeToMongoDate($data['reply_time']);
        return $data;
    }
}