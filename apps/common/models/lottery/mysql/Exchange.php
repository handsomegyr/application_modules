<?php
namespace Webcms\Common\Models\Mysql\Lottery;

use Webcms\Common\Models\Mysql\Base;

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
        $data['memo'] = $this->changeToArray($data['memo']);
        $data['got_time'] = $this->changeToMongoDate($data['got_time']);
        
        return $data;
    }
}