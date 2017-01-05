<?php
namespace App\Prize\Models;

class Code extends \App\Common\Models\Prize\Code
{

    /**
     * 获取券码
     *
     * @param string $prize_id            
     * @param string $activity_id            
     * @return array boolean
     */
    public function getCode($prize_id, $activity_id = '')
    {
        $now = getCurrentTime();
        $query = array(
            'prize_id' => $prize_id,
            'is_used' => array(
                '$ne' => true
            ),
            'start_time' => array(
                '$lt' => $now
            ),
            'end_time' => array(
                '$gt' => $now
            )
        );
        
        $loop = 0;
        while (true) {
            $code = $this->findOne($query);
            if (! empty($code)) {
                $options = array();
                $options['query'] = array(
                    '_id' => $code['_id'],
                    'prize_id' => $prize_id,
                    'is_used' => array(
                        '$ne' => true
                    ),
                    'start_time' => array(
                        '$lt' => $now
                    ),
                    'end_time' => array(
                        '$gt' => $now
                    )
                );
                $options['update'] = array(
                    '$set' => array(
                        'is_used' => true,
                        'activity_id' => $activity_id
                    )
                );
                $rst = $this->findAndModify($options);
                if (! empty($rst['value']))
                    return $rst['value'];
                
                if ($loop ++ >= 10) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * 创建一个奖品卡券
     *
     * @param string $prize_id            
     * @param string $code            
     * @param string $pwd            
     * @param boolean $is_used            
     * @param \MongoDate $start_time            
     * @param \MongoDate $end_time            
     */
    public function create($prize_id, $code, $pwd = '', $is_used = false, $start_time = null, $end_time = null)
    {
        if (empty($start_time)) {
            $start_time = getCurrentTime(strtotime('2016-01-01 00:00:00'));
        }
        if (empty($end_time)) {
            $end_time = getCurrentTime(strtotime('2099-12-31 23:59:59'));
        }
        $data = array();
        $data['prize_id'] = $prize_id;
        $data['code'] = $code;
        $data['pwd'] = $pwd;
        $data['is_used'] = $is_used;
        $data['start_time'] = $start_time;
        $data['end_time'] = $end_time;
        return $this->insert($data);
    }
}