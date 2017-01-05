<?php
namespace App\Common\Models\Lottery\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Exchange extends Base
{

    /**
     * 抽奖中奖信息
     * This model is mapped to the table ilottery_exchange
     */
    public function getSource()
    {
        return 'ilottery_exchange';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_valid'] = $this->changeToBoolean($data['is_valid']);
        $data['got_time'] = $this->changeToMongoDate($data['got_time']);
        
        return $data;
    }
}