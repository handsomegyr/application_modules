<?php
namespace App\Tencent\Models;

class ScriptTracking extends \App\Common\Models\Tencent\ScriptTracking
{

    /**
     * 记录执行时间
     *
     * @param string $type            
     * @param float $start_time            
     * @param float $end_time            
     * @param string $who            
     * @return array
     */
    public function record($type, $start_time, $end_time, $who)
    {
        $datas = array(
            'who' => $who,
            'type' => $type,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'execute_time' => abs($end_time - $start_time)
        );
        
        return $this->insert($datas);
    }
}
