<?php
namespace App\Common\Models\Vote\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Limit extends Base
{

    /**
     * 投票-选项表管理
     * This model is mapped to the table ivote_limit
     */
    public function getSource()
    {
        return 'ivote_limit';
    }
    
    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        return $data;
    }
}
