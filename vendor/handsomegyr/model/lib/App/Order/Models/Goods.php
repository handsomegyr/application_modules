<?php
namespace App\Order\Models;

class Goods extends \App\Common\Models\Order\Goods
{

    /**
     * 生成订单商品表信息
     *
     * @throws \Exception
     * @return array
     */
    public function create($buyerInfo, $orderInfo, $storeInfo, $cartGoodsInfo, $commis_rate = 0.00, $promotion_rate = 0.00, $refund_num = 0)
    {
        $data = array();
        $data['buyer_id'] = $buyerInfo['buyer_id'];
        $data['buyer_name'] = $buyerInfo['buyer_name'];
        $data['buyer_mobile'] = $buyerInfo['buyer_mobile'];
        $data['buyer_email'] = $buyerInfo['buyer_email'];
        $data['buyer_avatar'] = $buyerInfo['buyer_avatar'];
        $data['buyer_register_by'] = $buyerInfo['buyer_register_by'];
        $data['buyer_ip'] = $buyerInfo['buyer_ip'];
        
        $data['order_id'] = $orderInfo['order_id'];
        
        $data['goods_id'] = $cartGoodsInfo['goods_id'];
        $data['goods_name'] = $cartGoodsInfo['goods_name'];
        $data['goods_price'] = $cartGoodsInfo['goods_price'];
        $data['goods_value'] = $cartGoodsInfo['goods_value'];
        $data['goods_num'] = $cartGoodsInfo['goods_num'];
        $data['goods_image'] = $cartGoodsInfo['goods_image'];
        $data['gc_id'] = $cartGoodsInfo['gc_id'];
        $data['goods_type'] = $cartGoodsInfo['goods_type'];
        $data['goods_commonid'] = $cartGoodsInfo['goods_commonid'];
        $data['goods_period'] = $cartGoodsInfo['goods_period'];
        $data['goods_total_person_time'] = $cartGoodsInfo['goods_total_person_time'];
        $data['goods_remain_person_time'] = $cartGoodsInfo['goods_remain_person_time'];
        $data['lottery_prize_id'] = $cartGoodsInfo['lottery_prize_id'];
        
        $data['promotions_id'] = empty($cartGoodsInfo['promotions_id']) ? '' : $cartGoodsInfo['promotions_id'];
        
        $data['commis_rate'] = $commis_rate;
        $data['state'] = self::STATE1; // 进行中
        $data['refund_num'] = $refund_num; // 退购次数
                                           
        // 计算商品金额
        $goods_total = $data['goods_price'] * $data['goods_num'];
        // 计算本件商品优惠金额
        $promotion_value = floor($goods_total * $promotion_rate);
        $data['goods_pay_price'] = $goods_total - $promotion_value;
        
        $orderGoodsInfo = $this->insert($data);
        return $orderGoodsInfo;
    }

    /**
     * 根据订单ID列表获取商品列表信息
     *
     * @param array $order_ids            
     * @return array
     */
    public function getListByOrderIds(array $order_ids)
    {
        $list = array();
        if (! empty($order_ids)) {
            $query = array();
            $query['order_id'] = array(
                '$in' => $order_ids
            );
            $sort = array();
            $sort['goods_num'] = - 1;
            $orderGoodsList = $this->findAll($query, $sort);
            if (! empty($orderGoodsList)) {
                foreach ($orderGoodsList as $orderGoods) {
                    $list[$orderGoods['order_id']][] = $orderGoods;
                }
            }
            return $list;
        }
        return $list;
    }

    /**
     * 更新是否成功购买
     *
     * @param string $id            
     * @param boolean $is_success            
     * @param string $lottery_code            
     * @param number $failure_num            
     */
    public function updateIsSuccess($id, $is_success, $lottery_code, $failure_num)
    {
        $query = array(
            '_id' => $id
        );
        $data = array();
        $data['is_success'] = $is_success; // 成功支付
        $data['lottery_code'] = $lottery_code; // 云购码
        $data['purchase_num'] = count(explode(',', $lottery_code)); // 云购次数
        $data['purchase_time'] = getMilliTime(); // 云购时间
        $data['failure_num'] = $failure_num; // 云购失败次数
        
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 根据ID更新订单信息
     *
     * @param string $id            
     * @param string $order_no            
     * @param number $order_state            
     */
    public function updateOrderNoById($id, $order_no, $order_state)
    {
        $query = array();
        $query['_id'] = $id;
        $data = array();
        // 生成新订单的信息
        $data['order_no'] = $order_no;
        $data['order_state'] = $order_state;
        $data['is_post_single'] = false;
        $data['orderActDesc'] = '';
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 根据商品ID更新中奖码信息
     *
     * @param string $goods_id            
     * @param string $prize_code            
     * @param float $prize_time            
     * @param array $prizeOrderGoodsInfo            
     */
    public function updatePrizeCodeByGoodsId($goods_id, $prize_code, $prize_time, array $prizeOrderGoodsInfo)
    {
        $query = array();
        $query['goods_id'] = $goods_id;
        $data = array();
        $data['prize_code'] = $prize_code;
        $data['prize_time'] = $prize_time; // 揭晓时间
        $data['state'] = self::STATE3; // 已揭晓
        $data['prize_buyer_id'] = $prizeOrderGoodsInfo['buyer_id'];
        $data['prize_buyer_name'] = $prizeOrderGoodsInfo['buyer_name'];
        $data['prize_buyer_register_by'] = $prizeOrderGoodsInfo['buyer_register_by'];
        $data['prize_order_goods_id'] = $prizeOrderGoodsInfo['_id'];
        
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 根据商品ID和云购码获取订单商品信息
     *
     * @param string $goods_id            
     * @param string $lottery_code            
     * @return array
     */
    public function getInfoByGoodsIdAndLotteryCode($goods_id, $lottery_code)
    {
        $query = array();
        $query['goods_id'] = $goods_id;
        $query['lottery_code'] = array(
            '$like' => '%' . $lottery_code . '%'
        );
        $info = $this->findOne($query, $query);
        return $info;
    }

    /**
     * 取该商品最后购买记录
     *
     * @param string $goods_id            
     * @param number $limit            
     * @return array
     */
    public function getLastPurchaseInfo($goods_id)
    {
        $list = array();
        $query = array();
        $query['goods_id'] = $goods_id;
        $query['lottery_code'] = array(
            '$ne' => ''
        );
        $sort = array();
        $sort['purchase_time'] = - 1;
        $ret = $this->find($query, $sort, 0, 1);
        if (! empty($ret['datas'])) {
            return $ret['datas'][0];
        } else {
            return null;
        }
    }

    /**
     * 取该商品最后购买时间前网站所有商品的最后100条购买时间记录
     *
     * @param string $goods_id            
     * @param number $limit            
     * @return array
     */
    public function getLastPurchaseList($last_purchase_time, $limit = 100)
    {
        // 获取比它购买之前的100条记录
        $query = array();
        $query['purchase_time'] = array(
            '$lt' => $last_purchase_time
        );
        $query['lottery_code'] = array(
            '$ne' => ''
        );
        $sort = array(
            'purchase_time' => - 1
        );
        $orderGoodsList = $this->find($query, $sort, 0, $limit);
        $list = array();
        if (! empty($orderGoodsList['datas'])) {
            foreach ($orderGoodsList['datas'] as $orderGoods) {
                $list[$orderGoods['_id']] = $orderGoods;
            }
        }
        return $list;
    }

    /**
     * 分页获取最新购买的商品列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @return array
     */
    public function getUserBuyNewlist($page = 1, $limit = 9, array $otherConditions = array())
    {
        $query = array(
            'lottery_code' => array(
                '$ne' => ''
            )
        );
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $sort = array(
            'purchase_time' => - 1
        );
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit);
        return $list;
    }

    /**
     * 根据订单NO获取信息
     *
     * @param string $order_no            
     * @return array
     */
    public function getInfoByOrderNo($order_no)
    {
        $query = array(
            'order_no' => $order_no
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 确认收货地址
     *
     * @param string $order_no            
     * @param string $message            
     * @param array $consigneeInfo            
     */
    public function confirmOrderConsignee($order_no, $message, array $consigneeInfo)
    {
        $query = array(
            'order_no' => $order_no,
            'order_state' => self::ORDER_STATE1
        );
        $data = array();
        $data['consignee_info'] = json_encode($consigneeInfo);
        $data['order_message'] = $message;
        $data['order_state'] = self::ORDER_STATE2; // 待发货
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 发货
     *
     * @param string $order_no            
     * @param array $deliveryInfo            
     */
    public function deliveryOrder($order_no, array $deliveryInfo)
    {
        $query = array(
            'order_no' => $order_no,
            'order_state' => self::ORDER_STATE2
        );
        $data = array();
        $data['delivery_info'] = json_encode($deliveryInfo);
        $data['order_state'] = self::ORDER_STATE3; // 待确认收货
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 确认收货
     *
     * @param string $order_no            
     * @param string $consignee_id            
     * @param string $message            
     */
    public function confirmOrderReceive($order_no)
    {
        $query = array(
            'order_no' => $order_no,
            'order_state' => self::ORDER_STATE3
        );
        $data = array();
        $data['order_state'] = self::ORDER_STATE4; // 已确认收货
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 记录晒单ID
     *
     * @param string $order_no            
     * @param string $consignee_id            
     * @param string $message            
     */
    public function recordPostId($order_no, $post_id)
    {
        $query = array(
            'order_no' => $order_no,
            'order_state' => self::ORDER_STATE4
        );
        $data = array();
        $data['post_id'] = $post_id;
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 完成订单
     *
     * @param string $order_no            
     */
    public function finishOrder($order_no)
    {
        $query = array(
            'order_no' => $order_no,
            'order_state' => self::ORDER_STATE4
        );
        $data = array();
        $data['order_state'] = self::ORDER_STATE10; // 已完成
        $this->update($query, array(
            '$set' => $data
        ));
    }

    public function getOrderCountByBuyerId($buyer_id, array $otherConditions = array())
    {
        $query = array(
            'buyer_id' => $buyer_id,
            'lottery_code' => array(
                '$ne' => ''
            )
        );
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $num = $this->count($query);
        return $num;
    }

    /**
     * 根据购买者ID，获取云购列表
     *
     * @param string $buyer_id            
     * @param number $page            
     * @param number $limit            
     * @param number $state            
     * @param number $beginTime            
     * @param number $endTime            
     * @param array $otherConditions            
     * @return array
     */
    public function getUserBuyList($buyer_id, $page = 1, $limit = 10, $state = 0, $beginTime = 0, $endTime = 0, array $otherConditions = array())
    {
        $query = array();
        if (! empty($buyer_id)) {
            $query['buyer_id'] = $buyer_id;
        }
        $query['purchase_num'] = array(
            '$gt' => 0
        );
        // 0全部 1 进行中 3 已揭晓 4已退购
        if ($state == 1) {
            $query['state'] = \App\Order\Models\Goods::STATE1;
        } elseif ($state == 3) {
            $query['state'] = \App\Order\Models\Goods::STATE3;
        } elseif ($state == 4) {
            $query['state'] = \App\Order\Models\Goods::STATE4;
        }
        if (! empty($beginTime)) {
            $query['purchase_time']['$gte'] = $beginTime;
        }
        if (! empty($endTime)) {
            $query['purchase_time']['$lte'] = $endTime;
        }
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        
        $sort = array(
            'purchase_time' => - 1
        );
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit);
        return $list;
    }

    public function getUserWinList($buyer_id, $page = 1, $limit = 10, $orderState = 0, $beginTime = 0, $endTime = 0, array $otherConditions = array())
    {
        $query = array(
            'buyer_id' => $buyer_id,
            'prize_buyer_id' => $buyer_id,
            'purchase_num' => array(
                '$gt' => 0
            )
        );
        $query['order_state'] = array(
            '$gt' => \App\Order\Models\Goods::ORDER_STATE0
        );
        // 0全部 1待确认地址 2待发货 3待收货 4待晒单
        if ($orderState == 1) {
            $query['order_state'] = \App\Order\Models\Goods::ORDER_STATE1;
        } elseif ($orderState == 2) {
            $query['order_state'] = \App\Order\Models\Goods::ORDER_STATE2;
        } elseif ($orderState == 3) {
            $query['order_state'] = \App\Order\Models\Goods::ORDER_STATE3;
        } elseif ($orderState == 4) {
            $query['order_state'] = \App\Order\Models\Goods::ORDER_STATE4;
        }
        if (! empty($beginTime)) {
            $query['purchase_time']['$gte'] = $beginTime;
        }
        if (! empty($endTime)) {
            $query['purchase_time']['$lte'] = $endTime;
        }
        
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        
        $sort = array(
            'purchase_time' => - 1
        );
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit);
        return $list;
    }

    public function getList4MsgSend($page = 1, $limit = 10, array $otherConditions = array())
    {
        $query = array();
        $query['is_send_msg'] = false;
        $query['purchase_num'] = array(
            '$gt' => 0
        );
        $query['prize_buyer_id'] = array(
            '$ne' => ''
        );
        $query['prize_time']['$lte'] = getMilliTime();
        // 0全部 1 进行中 3 已揭晓 4已退购
        $query['state'] = \App\Order\Models\Goods::STATE3;
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        
        $sort = array(
            'prize_time' => 1
        );
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit);
        return $list;
    }

    public function isMsgSent($id)
    {
        $query = array(
            '_id' => $id
        );
        $data = array();
        $data['is_send_msg'] = true; // 已发送
        $this->update($query, array(
            '$set' => $data
        ));
    }
}