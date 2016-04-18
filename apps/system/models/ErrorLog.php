<?php
namespace App\System\Models;

class ErrorLog extends \App\Common\Models\System\ErrorLog
{

    /**
     * 记录
     *
     * @param Exception $e            
     * @return array
     */
    public function log(\Exception $e)
    {
        $data = array();
        $data['error_code'] = $e->getCode();
        $data['error_message'] = $e->getMessage();
        $data['happen_time'] = getCurrentTime();
        $result = $this->insert($data);
        
        return $result;
    }
}