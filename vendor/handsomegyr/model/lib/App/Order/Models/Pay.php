<?php
namespace App\Order\Models;

class Pay extends \App\Common\Models\Order\Pay
{

    const STATE0 = 0; // 0默认未支付
    const STATE1 = 1; // 1已支付(只有第三方支付接口通知到时才会更改此状态)
    
    /**
     * 默认排序方式
     *
     * @param number $dir            
     * @return array
     */
    public function getDefaultSort()
    {
        $sort = array();
        // 默认的排序方式
        $sort = array(
            'payment_time' => 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getDefaultQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 生成订单支付表信息
     *
     * @param string $pay_sn            
     * @param array $buyerInfo            
     * @throws \Exception
     * @return array
     */
    public function create($pay_sn, array $buyerInfo, $payment_code, $order_amount, $goods_amount, $rcb_amount, $pd_amount, $points_amount, $shipping_fee, $refund_amount, $is_points_used = false, $is_pd_used = false, $process_task = 'goods')
    {
        $data = array();
        $data['pay_sn'] = $pay_sn;
        $data['buyer_id'] = $buyerInfo['buyer_id'];
        $data['buyer_name'] = $buyerInfo['buyer_name'];
        $data['payment_code'] = $payment_code;
        $data['order_amount'] = $order_amount;
        $data['goods_amount'] = $goods_amount;
        $data['rcb_amount'] = $rcb_amount;
        $data['pd_amount'] = $pd_amount;
        $data['is_pd_used'] = $is_pd_used;
        $data['points_amount'] = $points_amount;
        $data['is_points_used'] = $is_points_used;
        
        $data['shipping_fee'] = $shipping_fee;
        $data['refund_amount'] = $refund_amount;
        $pay_amount = $order_amount - $rcb_amount - $points_amount - $pd_amount - $refund_amount;
        $data['pay_amount'] = $pay_amount;
        if ($pay_amount > 0) {
            $data['api_pay_state'] = self::STATE0; // 0默认未支付
        } else {
            $data['api_pay_state'] = self::STATE1; // 1已支付
            $data['payment_time'] = getCurrentTime(); // 支付时间
        }
        $data['process_state'] = false;
        $data['process_task'] = $process_task;
        $orderPayInfo = $this->insert($data);
        return $orderPayInfo;
    }

    public function calculateAmounts(array $orderList, $init_shipping_fee = 0.00, $init_rcb_amount = 0.00, $init_pd_amount = 0.00, $init_points_amount = 0.00, $init_refund_amount = 0.00, $is_points_used = false, $is_pd_used = false)
    {
        $order_amount = 0.00; // 订单总金额
        $goods_amount = 0.00; // 商品总金额
        $shipping_fee = $init_shipping_fee; // 运费
        $rcb_amount = $init_rcb_amount; // 充值卡支付金额
        $pd_amount = $init_pd_amount; // 预存款支付金额
        $points_amount = $init_points_amount; // 福分支付金额
        $refund_amount = $init_refund_amount; // 退款金额
        
        foreach ($orderList as $order) {
            $order_amount += $order['order_amount'];
            $goods_amount += $order['goods_amount'];
            $shipping_fee += $order['shipping_fee'];
            $rcb_amount += $order['rcb_amount'];
            $pd_amount += $order['pd_amount'];
            $points_amount += $order['points_amount'];
            $refund_amount += $order['refund_amount'];
        }
        // 支付金额 = 订单金额 - 退款金额 - 充值卡支付金额
        $pay_amount = $order_amount - $refund_amount - $rcb_amount;
        // 如果使用福分的话
        if ($is_points_used) {
            if ($pay_amount < $points_amount) {
                $points_amount = $pay_amount;
            }
        } else {
            $points_amount = 0;
        }
        $pay_amount -= $points_amount;
        // 如果使用预付款的话
        if ($is_pd_used) {
            if ($pay_amount < $pd_amount) {
                $pd_amount = $pay_amount;
            }
        } else {
            $pd_amount = 0;
        }
        $pay_amount -= $pd_amount;
        return array(
            'order_amount' => $order_amount,
            'goods_amount' => $goods_amount,
            'rcb_amount' => $rcb_amount,
            'pd_amount' => $pd_amount,
            'points_amount' => $points_amount,
            'shipping_fee' => $shipping_fee,
            'refund_amount' => $refund_amount,
            'pay_amount' => $pay_amount
        );
    }

    /**
     * 生成支付单编号
     *
     * @return string
     */
    public function makePaySn()
    {
        return getNewId();
    }

    /**
     * 记录微信统一订单接口返回的结果
     *
     * @param string $id            
     * @param array $unifiedorderInfo            
     */
    public function recordWeixinUnifiedorderInfo($id, array $unifiedorderInfo)
    {
        // 预支付交易会话标识 prepay_id
        // 二维码链接 code_url
        $query = array(
            '_id' => $id
        );
        $data = array(
            'memo' => json_encode($unifiedorderInfo)
        );
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 增加成功次数和失败次数
     *
     * @param string $id            
     * @param number $success_count            
     * @param number $failure_count            
     */
    public function incSuccessAndFailureCount($id, $success_count, $failure_count)
    {
        $query = array(
            '_id' => $id
        );
        $data = array();
        $data['success_count'] = $success_count;
        $data['failure_count'] = $failure_count;
        $data['process_state'] = true;
        $this->update($query, array(
            '$inc' => $data
        ));
    }

    public function changeToPaid($id)
    {
        $query = array(
            '_id' => $id,
            'api_pay_state' => self::STATE0
        );
        $data = array();
        $data['api_pay_state'] = self::STATE1;
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 分页获取已支付,未处理的支付单列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @return array
     */
    public function getProcessingList($page = 1, $limit = 5, array $otherConditions = array())
    {
        // 已支付
        $query = array(
            'api_pay_state' => self::STATE1,
            'process_state' => false
        );
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $sort = array(
            'payment_time' => 1
        );
        $list = $this->getPageList($page, $limit, $query, $sort, array());
        return $list;
    }

    /**
     * 根据某种条件获取分页列表
     *
     * @param array $query            
     * @param array $sort            
     * @param array $fields            
     * @return array
     */
    public function getPageList($page = 1, $limit = 10, array $query = array(), array $sort = array(), array $fields = array())
    {
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $defaultQuery = $this->getDefaultQuery();
        $query = array_merge($query, $defaultQuery);
        $list = false;
        if (empty($list)) {
            $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, $fields);
        }
        return $list;
    }
}