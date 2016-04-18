<?php
namespace App\Common\Models\Mysql\Lottery;

use App\Common\Models\Mysql\Base;

class Limit extends Base
{

    /**
     * 抽奖参与者中奖限制
     * This model is mapped to the table ilottery_limit
     */
    public function getSource()
    {
        return 'ilottery_limit';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);        
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        return $data;
    }
}