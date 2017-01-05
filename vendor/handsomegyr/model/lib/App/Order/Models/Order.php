<?php
namespace App\Order\Models;

class Order extends \App\Common\Models\Order\Order
{

    /**
     * 订单状态
     */
    // 已取消
    const ORDER_STATE_CANCEL = 0;
    // 已产生但未支付
    const ORDER_STATE_NEW = 10;
    // 已支付
    const ORDER_STATE_PAY = 20;
    // 已发货
    const ORDER_STATE_SEND = 30;
    // 已收货，交易成功
    const ORDER_STATE_SUCCESS = 40;
    // 未付款订单，自动取消的天数
    const ORDER_AUTO_CANCEL_DAY = 3;
    // 已发货订单，自动确认收货的天数
    const ORDER_AUTO_RECEIVE_DAY = 7;
    // 兑换码支持过期退款，可退款的期限，默认为7天
    const CODE_INVALID_REFUND = 7;
    // 默认未删除
    const ORDER_DEL_STATE_DEFAULT = 0;
    // 已删除
    const ORDER_DEL_STATE_DELETE = 1;
    // 彻底删除
    const ORDER_DEL_STATE_DROP = 2;
    // 订单结束后可评论时间，15天，60*60*24*15
    const ORDER_EVALUATE_TIME = 1296000;
    // 抢购订单状态
    const OFFLINE_ORDER_CANCEL_TIME = 3; // 单位为天
    
    /**
     * 生成订单
     *
     * @param string $pay_sn            
     * @param string $order_sn            
     * @param array $storeInfo            
     * @param array $buyerInfo            
     * @param string $payment_code            
     * @param number $goods_amount            
     * @param number $shipping_fee            
     * @param number $order_from            
     * @throws \Exception
     * @return array
     */
    public function create($pay_sn, $order_sn, array $storeInfo, array $buyerInfo, $payment_code, $goods_amount, $pd_amount, $rcb_amount, $points_amount, $shipping_fee, $refund_amount, $order_from)
    {
        if ($payment_code == "") {
            $payment_code = "offline";
        }
        $order = array();
        $order['pay_sn'] = $pay_sn;
        $order['order_sn'] = $order_sn;
        
        $order['buyer_id'] = $buyerInfo['buyer_id'];
        $order['buyer_name'] = $buyerInfo['buyer_name'];
        $order['buyer_mobile'] = $buyerInfo['buyer_mobile'];
        $order['buyer_email'] = $buyerInfo['buyer_email'];
        $order['buyer_avatar'] = $buyerInfo['buyer_avatar'];
        $order['buyer_register_by'] = $buyerInfo['buyer_register_by'];
        
        $order['add_time'] = getCurrentTime();
        $order['payment_code'] = $payment_code;
        $order['order_state'] = ($payment_code == 'online' ? self::ORDER_STATE_NEW : self::ORDER_STATE_PAY);
        $order['shipping_fee'] = $shipping_fee;
        $order['goods_amount'] = $goods_amount;
        $order['pd_amount'] = $pd_amount;
        $order['rcb_amount'] = $rcb_amount;
        $order['points_amount'] = $points_amount;
        $order['refund_amount'] = $refund_amount;
        $order['order_amount'] = $order['goods_amount'] + $order['shipping_fee'] - $order['pd_amount'] - $order['rcb_amount'] - $order['points_amount'] - $order['refund_amount'];
        $order['order_from'] = $order_from;
        $orderInfo = $this->insert($order);
        return $orderInfo;
    }

    /**
     * 订单编号生成
     *
     * @return string
     */
    public function makeOrderSn()
    {
        return getNewId();
    }

    /**
     * 根据订单支付单号获取订单列表
     *
     * @param string $pay_sn            
     * @param string $buyer_id            
     * @return array
     */
    public function getListByPaySn($pay_sn, $buyer_id)
    {
        $query = array();
        $query['pay_sn'] = $pay_sn;
        $query['buyer_id'] = $buyer_id;
        $sort = array(
            '_id' => - 1
        );
        $orderList = $this->findAll($query, $sort);
        $list = array();
        if (! empty($orderList)) {
            foreach ($orderList as $order) {
                $list[$order['_id']] = $order;
            }
        }
        return $list;
    }

    /**
     * 更新支付方式
     *
     * @param array $order_ids            
     * @param string $payment_code            
     */
    public function updatePaymentCode(array $order_ids, $payment_code)
    {
        $query = array(
            '_id' => array(
                '$in' => $order_ids
            )
        );
        // $query = array(
        // '_id' => '56757d4c887c22184e8b45b2'
        // );
        $data = array();
        $data['payment_code'] = $payment_code;
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 更新订单状态
     * 订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;
     *
     * @param array $order_ids            
     * @param number $order_state            
     */
    public function updateOrderState(array $order_ids, $order_state)
    {
        $query = array(
            '_id' => array(
                '$in' => $order_ids
            )
        );
        $data = array();
        $data['order_state'] = $order_state;
        if ($order_state == self::ORDER_STATE_PAY) {
            $data['payment_time'] = getCurrentTime();
        }
        $this->update($query, array(
            '$set' => $data
        ));
    }
}