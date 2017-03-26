<?php
namespace App\Common\Models\Bargain\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Log extends Base
{

    /**
     * 砍价-日志
     * This model is mapped to the table ibargain_log
     */
    public function getSource()
    {
        return 'ibargain_log';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_system_bargain'] = $this->changeToBoolean($data['is_system_bargain']);
        $data['bargain_time'] = $this->changeToMongoDate($data['bargain_time']);
        return $data;
    }
}
