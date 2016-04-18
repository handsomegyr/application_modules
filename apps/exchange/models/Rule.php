<?php
namespace App\Exchange\Models;

class Rule extends \App\Common\Models\Exchange\Rule
{

    public function getDefaultSort()
    {
        return $sort = array(
            'sort' => 1
        );
    }
    
    // 减少规则数量
    public function exchange($rule_id, $quantity)
    {
        $option = array();
        $option['query'] = array(
            '_id' => $rule_id,
            'quantity' => array(
                '$gte' => $quantity
            )
        );
        $option['update'] = array(
            '$inc' => array(
                'quantity' => - $quantity,
                'exchange_quantity' => $quantity
            )
        );
        $rst = $this->findAndModify($option);
        if (empty($rst['ok'])) {
            throw new \Exception("减少规则数量的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        if (empty($rst['value'])) {
            throw new \Exception("减少规则数量的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        return $rst['value'];
    }

    /**
     * 获得可兑换奖品
     *
     * @param number $date            
     * @param number $score            
     * @param number $score_category            
     * @return array
     */
    public function getList($date = 0, $score = 0, $score_category = 0)
    {
        if (! $date) {
            $date = time();
        }
        $query = array();
        $query['start_time'] = array(
            '$lte' => getCurrentTime($date)
        );
        $query['end_time'] = array(
            '$gt' => getCurrentTime($date)
        );
        if ($score)
            $query['score'] = array(
                '$gte' => $score
            );
        if ($score_category)
            $query['score_category'] = $score_category;
        
        $sort = $this->getDefaultSort();
        $list = $this->findAll($query, $sort);
        return $list;
    }
}