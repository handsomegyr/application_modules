<?php
namespace App\Common\Models\System\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Activity extends Base
{

    /**
     * 活动
     * This model is mapped to the table activity
     */
    public function getSource()
    {
        return 'activity';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);        
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        $data['is_actived'] = $this->changeToBoolean($data['is_actived']);        
        return $data;
    }
}