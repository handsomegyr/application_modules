<?php
namespace App\Exchange\Models;

class Limit extends \App\Common\Models\Exchange\Limit
{

    private $_limits = null;

    private $_success = null;

    public function setSuccessModel(Success $success)
    {
        $this->_success = $success;
    }

    public function getSuccessModel()
    {
        if (empty($this->_success)) {
            throw new \Exception("没有指定兑换成功对象");
        }
        return $this->_success;
    }

    /**
     * 获取全部限定条件
     *
     * @param string $prize_id            
     */
    public function getLimits($prize_id)
    {
        if ($this->_limits == null) {
            
            $now = getCurrentTime();
            $query = array(
                'prize_id' => $prize_id,
                'limit' => array(
                    '$gt' => 0
                ),
                'start_time' => array(
                    '$lte' => $now
                ),
                'end_time' => array(
                    '$gt' => $now
                )
            );
            $this->_limits = $this->findAll($query, array(
                '_id' => - 1
            ));
        }
        return $this->_limits;
    }

    /**
     * 是否可以兑换 没有限制则默认可以
     *
     * @param string $prize_id            
     * @param string $user_id            
     * @param number $quantity            
     * @return boolean
     */
    public function checkLimit($prize_id, $user_id, $quantity)
    {
        $limits = $this->getLimits($prize_id);
        
        if (! empty($limits)) {
            foreach ($limits as $limit) {
                // 从成功兑换表中获取该商品的数量
                $successNum = $this->getSuccessModel()->getExchangeNum($user_id, $prize_id, $limit['start_time'], $limit['end_time']);
                // 兑换数量>=限制时，无法兑换
                if (($successNum + $quantity) > $limit['limit'])
                    return false;
            }
        }
        return true;
    }
}