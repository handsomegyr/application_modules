<?php
namespace App\Common\Models\Activity\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Activity extends Base
{

    /**
     * 活动-活动表管理
     * This model is mapped to the table iactivity_activity
     */
    public function getSource()
    {
        return 'iactivity_activity';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        $data['is_actived'] = $this->changeToBoolean($data['is_actived']);
        $data['is_paused'] = $this->changeToBoolean($data['is_paused']);
        $data['config'] = $this->changeToArray($data['config']);
        return $data;
    }
}