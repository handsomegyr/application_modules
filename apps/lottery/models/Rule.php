<?php
namespace App\Lottery\Models;

class Rule extends \App\Common\Models\Lottery\Rule
{

    private $_rules = null;

    private $_limit = null;

    private $_exchange = null;

    public function setExchangeModel(Exchange $exchange)
    {
        $this->_exchange = $exchange;
    }

    public function getExchangeModel()
    {
        if ($this->exchange == null) {
            $this->exchange = new Exchange();
        }
        return $this->exchange;
    }

    public function setLimitModel(Limit $limit)
    {
        $this->_limit = $limit;
    }

    public function getLimitModel()
    {
        if ($this->_limit == null) {
            $this->_limit = new Limit();
        }
        return $this->_limit;
    }

    /**
     * 获取指定活动的全部抽奖规则
     *
     * @param string $activity_id            
     * @param array $prize_ids            
     */
    public function getRules($activity_id, array $prize_ids = array())
    {
        if ($this->_rules == null) {
            $now = getCurrentTime();
            $query = array(
                'activity_id' => $activity_id,
                'allow_start_time' => array(
                    '$lte' => $now
                ),
                'allow_end_time' => array(
                    '$gte' => $now
                )
            );
            if (! empty($prize_ids)) {
                $query['prize_id'] = array(
                    '$in' => $prize_ids
                );
            }
            $this->_rules = $this->findAll($query);
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
            $groupList[$row['allow_probability']][] = $row;
        }, $list);
        
        // 按照概率从高到底的次序排序
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
     * 计算抽奖概率判断用户是否中奖
     */
    public function lottery($activity_id, $identity_id, array $prize_ids = array())
    {
        $rules = $this->getRules($activity_id, $prize_ids);
        if (! empty($rules)) {
            foreach ($rules as $rule) {
                if (rand(0, 9999) < $rule['allow_probability'] && $rule['allow_number'] > 0) {
                    $allow = $this->getLimitModel()->checkLimit($activity_id, $identity_id, $rule['prize_id']);
                    if ($allow)
                        return $rule;
                }
            }
        }
        return false;
    }

    /**
     * 更新奖品的剩余数量
     *
     * @param array $rule            
     * @return bool false表示错误 true表示正确
     */
    public function updateRemain($rule)
    {
        $options = array();
        $options['query'] = array(
            '_id' => $rule['_id'],
            'prize_id' => $rule['prize_id'],
            'allow_number' => array(
                '$gt' => 0
            )
        );
        $options['update'] = array(
            '$inc' => array(
                'allow_number' => - 1
            )
        );
        $rst = $this->findAndModify($options);
        if ($rst['ok'] == 0) {
            throw new \Exception("findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        if ($rst['value'] == null) {
            return false;
        }
        return true;
    }

    /**
     * 生成抽奖概率
     *
     * @param string $activity_id            
     * @param string $prize_id            
     * @param number $allow_number            
     * @param number $allow_probability            
     * @param \Mongodate $allow_start_time            
     * @param \Mongodate $allow_end_time            
     */
    public function create($activity_id, $prize_id, $allow_number = 0, $allow_probability = 0, $allow_start_time = null, $allow_end_time = null)
    {
        if (empty($allow_start_time)) {
            $allow_start_time = getCurrentTime(strtotime('2016-01-01 00:00:00'));
        }
        if (empty($allow_end_time)) {
            $allow_end_time = getCurrentTime(strtotime('2099-12-31 23:59:59'));
        }
        $data = array();
        $data['activity_id'] = $activity_id;
        $data['prize_id'] = $prize_id;
        $data['allow_start_time'] = $allow_start_time;
        $data['allow_end_time'] = $allow_end_time;
        $data['allow_number'] = $allow_number;
        $data['allow_probability'] = $allow_probability;
        return $this->insert($data);
    }
}