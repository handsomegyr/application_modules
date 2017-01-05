<?php
namespace App\Weixinredpack\Models;

class Limit extends \App\Common\Models\Weixinredpack\Limit
{

    private $_limits = null;

    private $_log = null;

    /**
     * 获取全部限定条件
     *
     * @param string $activity_id            
     * @param string $customer_id            
     * @param string $redpack_id            
     */
    public function getLimits($activity_id, $customer_id, $redpack_id)
    {
        if ($this->_limits == null) {
            $now = getCurrentTime();
            $this->_limits = $this->findAll(array(
                'activity' => $activity_id,
                'customer' => $customer_id,
                'redpack' => $redpack_id,
                'start_time' => array(
                    '$lte' => $now
                ),
                'end_time' => array(
                    '$gte' => $now
                )
            ));
        }
        return $this->_limits;
    }

    public function setLogModel(GotLog $log)
    {
        $this->_log = $log;
    }

    /**
     * 检查指定操作指定规则的限制是否达到
     *
     * @param string $activity_id            
     * @param string $customer_id            
     * @param string $redpack_id            
     * @param string $re_openid            
     */
    public function checkLimit($activity_id, $customer_id, $redpack_id, $re_openid)
    {
        $limits = $this->getLimits($activity_id, $customer_id, $redpack_id);
        if (! empty($limits)) {
            foreach ($limits as $limit) {
                // 增加log日志的查询
                $redpackLogCount = $this->_log->getRedpackCountByOpenId($re_openid, $activity_id, $customer_id, $redpack_id, $limit['start_time']->sec, $limit['end_time']->sec);
                if ($redpackLogCount >= $limit['personal_got_num_limit']) {
                    return false;
                }
            }
            return true;
        } else
            return false;
    }
}