<?php
namespace App\Common\Models\Activity\Mysql;

use App\Common\Models\Base\Mysql\Base;

class ErrorLog extends Base
{

    /**
     * 活动-错误日志
     * This model is mapped to the table errorlog
     */
    public function getSource()
    {
        return 'iactivity_errorlog';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['happen_time'] = $this->changeToMongoDate($data['happen_time']);
        return $data;
    }
}