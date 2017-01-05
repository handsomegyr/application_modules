<?php
namespace App\Weixinredpack\Models;

class Rule extends \App\Common\Models\Weixinredpack\Rule
{
    private $_rules = null;

    private $_limit = null;

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            '_id' => - 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 检查指定操作指定规则的限制是否达到
     *
     * @param array $rule            
     */
    public function checkValid($rule)
    {
        // 目前只能设置为1,这个是微信红包接口的限制,以后可能放开此限制
        if ($rule['personal_can_get_num'] != 1) {
            return false;
        }
        // 如果数量<个人能获取的红包数的时候
        if ($rule['quantity'] < $rule['personal_can_get_num']) {
            return false;
        }
        
        // 如果金额小于最小现金的时候
        if ($rule['amount'] < $rule['min_cash'] * $rule['personal_can_get_num']) {
            return false;
        }
        
        return true;
    }

    /**
     * 获取指定活动的全部抽奖规则
     *
     * @param string $activity_id            
     * @param string $customer_id            
     * @param string $redpack_id            
     */
    public function getRules($activity_id, $customer_id, $redpack_id)
    {
        if ($this->_rules == null) {
            $now = getCurrentTime();
            $this->_rules = $this->findAll(array(
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
        return $this->doShuffle($this->_rules);
    }

    /**
     * 对于概率进行随机分组处理
     *
     * @param array $list            
     * @return array
     */
    private function doShuffle($list)
    {
        $groupList = array();
        // 按照allow_probability分组
        array_map(function ($row) use(&$groupList)
        {
            //$key = "a{$row['activity']}_c{$row['customer']}_rp{$row['redpack']}_ap{$row['allow_probability']}";
            $key = $row['allow_probability'];
            $groupList[$key][] = $row;
        }, $list);
        
        // 按照概率从底到的高次序排序
        ksort($groupList, SORT_NUMERIC);
        
        // 按分组随机排序
        $resultList = array();
        foreach ($groupList as $key => $rows) {
            shuffle($rows);
            $resultList = array_merge($resultList, $rows);
        }
        return $resultList;
    }

    /**
     * 获取一个有效的发放规则
     */
    public function getValidRule($activity_id, $customer_id, $redpack_id)
    {
        $rules = $this->getRules($activity_id, $customer_id, $redpack_id);
        if (! empty($rules)) {
            foreach ($rules as $rule) {
                $allow = $this->checkValid($rule);                
                if (rand(0, 9999) < $rule['allow_probability'] && $allow)
                    return $rule;
            }
        }
        return false;
    }

    /**
     * 更新剩余数量和金额
     *
     * @param array $rule            
     * @return bool false表示错误 true表示正确
     */
    public function updateRemain($rule, $amount = 100, $quantity = 1)
    {
        $options = array();
        $options['query'] = array(
            '_id' => $rule['_id'],
            'quantity' => array(
                '$gt' => 0
            ),
            'amount' => array(
                '$gte' => $rule['min_cash']
            )
        );
        $options['update'] = array(
            '$inc' => array(
                'quantity' => - intval($quantity),
                'amount' => - intval($amount)
            )
        );
        $options['new'] = true; // 返回更新之后的值
        $rst = $this->findAndModify($options);
        if (empty($rst['ok'])) {
            throw new \Exception("更新剩余数量和金额的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        if (empty($rst['value'])) {
            throw new \Exception("更新剩余数量和金额的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        return $rst['value'];
    }
    
    
}