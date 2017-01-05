<?php
namespace App\Order\Models;

class Statistics extends \App\Common\Models\Order\Statistics
{

    /**
     * 增加统计信息
     *
     * @param string $id            
     * @param array $orderPayInfo            
     */
    public function incStatisticsInfo($id, array $orderPayInfo)
    {
        $query = array(
            '_id' => $id
        );
        $data = array();
        $data['order_amount'] = $orderPayInfo['order_amount'];
        $data['goods_amount'] = $orderPayInfo['goods_amount'];
        $data['rcb_amount'] = $orderPayInfo['rcb_amount'];
        $data['pd_amount'] = $orderPayInfo['pd_amount'];
        $data['points_amount'] = $orderPayInfo['points_amount'];
        $data['shipping_fee'] = $orderPayInfo['shipping_fee'];
        $data['refund_amount'] = $orderPayInfo['refund_amount'];
        $data['pay_amount'] = $orderPayInfo['pay_amount'];
        $data['success_count'] = $orderPayInfo['success_count'];
        $data['failure_count'] = $orderPayInfo['failure_count'];
        $this->update($query, array(
            '$inc' => $data
        ));
    }
}