<?php
namespace App\Invitation\Models;

class Rule extends \App\Common\Models\Invitation\Rule
{

    private $_rules = null;

    /**
     * 获取指定活动的全部规则
     *
     * @param string $activity_id            
     */
    public function getRules($activity_id)
    {
        if ($this->_rules == null) {
            $now = getCurrentTime();
            $query = array(
                'activity_id' => $activity_id,
                'start_time' => array(
                    '$lte' => $now
                ),
                'end_time' => array(
                    '$gte' => $now
                )
            );
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
        // 按照probability分组
        array_map(function ($row) use(&$groupList)
        {
            $groupList[$row['probability']][] = $row;
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
     * 获取价值
     */
    public function getWorth($activity_id)
    {
        $worth = 0;
        $rules = $this->getRules($activity_id);
        if (! empty($rules)) {
            foreach ($rules as $rule) {
                if (rand(0, 9999) < $rule['probability']) {
                    $worth = $rule['worth'];
                    return $worth;
                }
            }
        }
        return $worth;
    }
}