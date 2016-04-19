<?php
namespace App\Common\Models\Order;

use App\Common\Models\Base\Base;

class Goods extends Base
{
    
    // 销售状态 1 进行中 2 揭晓中 3 已揭晓
    const SALESTATEDATAS = array(
        '1' => array(
            'name' => '进行中',
            'value' => '1'
        ),
        '2' => array(
            'name' => '揭晓中',
            'value' => '2'
        ),
        '3' => array(
            'name' => '已揭晓',
            'value' => '3'
        )
    );
    
    // 订单状态 1 完善收货地址 2 待发货 3 确认收货 4 已确认收货
    const ORDERSTATEDATAS = array(
        '1' => array(
            'name' => '完善收货地址',
            'value' => '1'
        ),
        '2' => array(
            'name' => '待发货',
            'value' => '2'
        ),
        '3' => array(
            'name' => '待确认收货',
            'value' => '3'
        ),
        '4' => array(
            'name' => '已确认收货',
            'value' => '4'
        ),
        '5' => array(
            'name' => '已晒单',
            'value' => '5'
        ),
        '10' => array(
            'name' => '已完成',
            'value' => '10'
        ),
        '11' => array(
            'name' => '已取消',
            'value' => '11'
        )
    );

    const STATE1 = 1; // 进行中
    const STATE2 = 2; // 揭晓中
    const STATE3 = 3; // 已揭晓
    const STATE4 = 4; // 已退购
    const ORDER_STATE0 = 0; // 非订单
    const ORDER_STATE1 = 1; // 完善收货地址
    const ORDER_STATE2 = 2; // 待发货
    const ORDER_STATE3 = 3; // 待确认收货
    const ORDER_STATE4 = 4; // 已确认收货
    const ORDER_STATE5 = 5; // 已晒单
    const ORDER_STATE10 = 10; // 已完成/晒单赢福分
    const ORDER_STATE11 = 11; // 已取消
    function __construct()
    {
        $this->setModel(new \App\Common\Models\Order\Mysql\Goods());
    }
}