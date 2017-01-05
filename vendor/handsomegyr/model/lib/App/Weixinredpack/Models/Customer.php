<?php
namespace App\Weixinredpack\Models;

class Customer extends \App\Common\Models\Weixinredpack\Customer
{    
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
     * 更新客户的总使用金额
     *
     * @param string $customer_id            
     * @param number $total_amount            
     */
    public function incUsedAmount($customer_id, $total_amount)
    {
        $total_amount = intval($total_amount);
        $options = array();
        $options['query'] = array(
            '_id' => ($customer_id),
            'remain_amount' => array(
                '$gte' => $total_amount
            )
        );
        $options['update'] = array(
            '$inc' => array(
                'used_amount' => $total_amount,
                'remain_amount' => - $total_amount
            )
        );
        $options['new'] = true; // 返回更新之后的值
        $rst = $this->findAndModify($options);
        if (empty($rst['ok'])) {
            throw new \Exception("更新客户总红包金额的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        if (empty($rst['value'])) {
            throw new \Exception("更新客户总红包金额的findAndModify执行错误，返回结果为:" . json_encode($rst));
        }
        return $rst['value'];
    }

    /**
     * 获取客户余额
     *
     * @return number
     */
    public function getRemainAmount(array $customerInfo)
    {
        return intval($customerInfo['remain_amount']);
    }
}