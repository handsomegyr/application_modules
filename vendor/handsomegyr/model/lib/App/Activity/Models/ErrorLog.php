<?php
namespace App\Activity\Models;

class ErrorLog extends \App\Common\Models\Activity\ErrorLog
{

    /**
     * 记录
     *
     * @param string $activity_id            
     * @param \Exception $e            
     * @return array
     */
    public function log($activity_id, \Exception $e)
    {
        $data = array();
        $data['activity_id'] = $activity_id;
        $data['error_code'] = intval($e->getCode());
        $data['error_message'] = $e->getMessage();
        $data['happen_time'] = getCurrentTime();
        $result = $this->insert($data);
        return $result;
    }
}