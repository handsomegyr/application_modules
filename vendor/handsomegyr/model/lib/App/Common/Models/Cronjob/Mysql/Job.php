<?php
namespace App\Common\Models\Cronjob\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Job extends Base
{

    /**
     * 计划任务表管理
     * This model is mapped to the table icronjob_job
     */
    public function getSource()
    {
        return 'icronjob_job';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['start_time'] = $this->changeToMongoDate($data['start_time']);
        $data['end_time'] = $this->changeToMongoDate($data['end_time']);
        $data['last_execute_time'] = $this->changeToMongoDate($data['last_execute_time']);
        return $data;
    }
}