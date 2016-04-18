<?php

namespace App\Common\Models\Mysql\Vote;

use App\Common\Models\Mysql\Base;

class Log extends Base
{

    /**
     * 投票-明细表管理
     * This model is mapped to the table ivote_log
     */
    public function getSource()
    {
        return 'ivote_log';
    }
    
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['vote_time'] = $this->changeToMongoDate($data['vote_time']);
        return $data;
    }
}