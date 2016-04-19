<?php
namespace App\Common\Models\Post\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Vote extends Base
{

    /**
     * 帖子-投票管理
     * This model is mapped to the table ipost_vote
     */
    public function getSource()
    {
        return 'ipost_vote';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['vote_time'] = $this->changeToMongoDate($data['vote_time']);
        return $data;
    }
}