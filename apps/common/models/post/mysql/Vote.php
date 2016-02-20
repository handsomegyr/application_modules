<?php
namespace Webcms\Common\Models\Mysql\Post;

use Webcms\Common\Models\Mysql\Base;

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