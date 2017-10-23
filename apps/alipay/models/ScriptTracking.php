<?php
namespace App\Alipay\Models;

class ScriptTracking extends \App\Common\Models\Alipay\ScriptTracking
{

    /**
     * 记录执行时间
     *
     * @param string $app_id            
     * @param string $type            
     * @param float $start_time            
     * @param float $end_time            
     * @param string $who            
     */
    public function record($app_id, $type, $start_time, $end_time, $who)
    {
        $datas = array(
            'app_id' => $app_id,
            'who' => $who,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'execute_time' => abs($end_time - $start_time)
        );
        
        return $this->insert($datas);
    }
}