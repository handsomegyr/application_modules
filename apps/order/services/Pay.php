<?php
namespace App\Order\Services;

class Pay
{

    private $modelMemberNews = null;

    private $modelGoods = null;

    private $modelMember = null;

    private $modelOrder = null;

    private $modelOrderPay = null;

    private $modelOrderGoods = null;

    private $modelOrderStatistics = null;

    private $modelPointsUser = null;

    private $modelInvitation = null;

    private $modelInvitationGotDetail = null;

    private $serviceLottery = null;

    private $modelPointsRule = null;

    private $modelPayLog = null;

    private $modelTaskLog = null;

    function __construct()
    {
        $this->modelMemberNews = new \App\Member\Models\News();
        $this->modelGoods = new \App\Goods\Models\Goods();
        $this->modelMember = new \App\Member\Models\Member();
        $this->modelOrder = new \App\Order\Models\Order();
        $this->modelOrderPay = new \App\Order\Models\Pay();
        $this->modelOrderStatistics = new \App\Order\Models\Statistics();
        $this->modelOrderGoods = new \App\Order\Models\Goods();
        $this->modelPointsUser = new \App\Points\Models\User();
        $this->modelInvitation = new \App\Invitation\Models\Invitation();
        $this->modelInvitation->setIsExclusive(false);
        $this->modelInvitationGotDetail = new \App\Invitation\Models\InvitationGotDetail();
        $this->serviceLottery = new \App\Lottery\Services\Api();
        $this->modelPointsRule = new \App\Points\Models\Rule();
        $this->modelPayLog = new \App\Payment\Models\Log();
        $this->modelTaskLog = new \App\Task\Models\Log();
    }

    /**
     * 支付完成处理
     */
    public function finishPay($out_trade_no)
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        $request = array(
            'out_trade_no' => $out_trade_no
        );
        try {
            try {
                $this->modelOrderPay->begin();
                
                // lock
                $orderPayInfo = $this->modelOrderPay->findOne(array(
                    '_id' => $out_trade_no,
                    'api_pay_state' => \App\Order\Models\Pay::STATE1,
                    'process_state' => false,
                    '__FOR_UPDATE__' => true
                ));
                if (empty($orderPayInfo)) {
                    throw new \Exception('支付订单不存在', - 1);
                }
                $pay_amount = $orderPayInfo['pay_amount'];
                $pay_sn = $orderPayInfo['pay_sn'];
                $payment_code = $orderPayInfo['payment_code'];
                $process_task = $orderPayInfo['process_task'];
                
                // 获取会员信息
                $buyerInfo = $this->modelMember->getInfoById($orderPayInfo['buyer_id']);
                $buyerInfo['buyer_id'] = $buyerInfo['_id'];
                $buyerInfo['buyer_name'] = $this->modelMember->getRegisterName($buyerInfo);
                $buyerInfo['buyer_email'] = $buyerInfo['email'];
                $buyerInfo['buyer_mobile'] = $buyerInfo['mobile'];
                $buyerInfo['buyer_avatar'] = $buyerInfo['avatar'];
                $buyerInfo['buyer_register_by'] = $buyerInfo['register_by'];
                
                switch ($process_task) {
                    case 'predeposit':
                        // 预付款充值
                        // 增加预付款
                        $pay_amount_yuan = $pay_amount / 100;
                        $this->modelPointsUser->addOrReduce(POINTS_CATEGORY3, $buyerInfo['buyer_id'], $buyerInfo['buyer_name'], $buyerInfo['buyer_avatar'], $out_trade_no, $orderPayInfo['__CREATE_TIME__'], $pay_amount, "预付款充值", "支付金额￥{$pay_amount_yuan}已充值到您的云购账户");
                        
                        // 增加支付日志记录
                        $this->modelPayLog->recordLog($buyerInfo['buyer_id'], \App\Payment\Models\Log::TYPE1, $pay_amount, '预付款充值', $orderPayInfo);
                        
                        // 更新支付订单的信息
                        $this->modelOrderPay->incSuccessAndFailureCount($orderPayInfo['_id'], 1, 0);
                        
                        break;
                    
                    default:
                        // 商品购买支付
                        // 福分账户锁定
                        $pointInfo = $this->modelPointsUser->getInfoByUserId($orderPayInfo['buyer_id'], POINTS_CATEGORY1); // 福分
                                                                                                                           
                        // 预付款账户锁定
                        $predepositInfo = $this->modelPointsUser->getInfoByUserId($orderPayInfo['buyer_id'], POINTS_CATEGORY3); // 预付款
                                                                                                                                
                        // 获取订单列表信息
                        $orderList = $this->modelOrder->getListByPaySn($pay_sn, $orderPayInfo['buyer_id']);
                        if (empty($orderList)) {
                            throw new \Exception('订单不存在', - 4);
                        }
                        
                        // 更新订单的支付方式
                        $order_ids = array_keys($orderList);
                        $this->modelOrder->updatePaymentCode($order_ids, $payment_code);
                        // 更新订单的支付状态等信息
                        $this->modelOrder->updateOrderState($order_ids, \App\Order\Models\Order::ORDER_STATE_PAY);
                        
                        // 如果支付完成的话，计算成功和失败个数
                        $failure_count = 0; // 失败个数
                        $success_count = 0; // 成功个数
                        $pay_state = true;
                        $total_amount = 0.00; // 总花费金额
                                              
                        // 获取订单商品列表
                        $goods_ids = array();
                        $orderGoodsList = $this->modelOrderGoods->getListByOrderIds($order_ids);
                        foreach ($orderGoodsList as $order_id => $goodsList) {
                            foreach ($goodsList as $goodsInfo) {
                                
                                // 云购码列表
                                $lottery_code_list = array();
                                // 为每个订单商品获取一个云购码
                                for ($i = 0; $i < $goodsInfo['goods_num']; $i ++) {
                                    // 是否能够购买
                                    $isCanBuy = false;
                                    // 检查支付金额够不够
                                    $goods_amount = 1 * $goodsInfo['goods_price'];
                                    // 如果支付金额足的时候
                                    if ($pay_amount >= $goods_amount) {
                                        $pay_amount -= $goods_amount; // 支付金额
                                        $total_amount += $goods_amount; // 总花费金额
                                        $isCanBuy = true;
                                    } else {
                                        // 如果支付金额已不足的时候
                                        $isCanBuy = false;
                                        // 如果使用福分的话,扣除福分的处理
                                        if (! empty($orderPayInfo['is_points_used'])) {
                                            try {
                                                // $pointInfo = $this->modelPointsUser->addOrReduce(POINTS_CATEGORY1, $buyer_id, $buyerInfo['buyer_name'], $buyerInfo['buyer_avatar'], $orderPayInfo['_id'], $orderPayInfo['__CREATE_TIME__'], - $integral, "支付", "云购商品编码(2542873)消耗{$integral}福分");
                                            } catch (\Exception $e) {
                                                ;
                                            }
                                        }
                                        // 如果使用预付款的话,扣除预付款的处理
                                        if (! empty($orderPayInfo['is_pd_used'])) {
                                            if ($predepositInfo['current'] >= $goods_amount) {
                                                try {
                                                    $predepositInfo = $this->modelPointsUser->addOrReduce(POINTS_CATEGORY3, $orderPayInfo['buyer_id'], $buyerInfo['buyer_name'], $buyerInfo['buyer_avatar'], $orderPayInfo['_id'], $orderPayInfo['__CREATE_TIME__'], - $goods_amount, "支付", "云购商品");
                                                    $total_amount += $goods_amount; // 总花费金额
                                                    $isCanBuy = true;
                                                } catch (\Exception $e) {
                                                    ;
                                                }
                                            }
                                        }
                                    }
                                    // 如果能买的话
                                    if ($isCanBuy) {
                                        // 通过抽奖处理，获取云购码
                                        $prize_id = $goodsInfo['lottery_prize_id'];
                                        $this->serviceLottery->_isInstance = false;
                                        $lotteryResult = $this->serviceLottery->doLottery(YUNGOU_ACTIVITY_ID, $orderPayInfo['buyer_id'], array(
                                            $prize_id
                                        ));
                                        // 如果成功获取的话
                                        if (empty($lotteryResult['error_code'])) {
                                            $exchangeInfo = $lotteryResult['result'];
                                            $lottery_code_list[] = $exchangeInfo['prize_virtual_code'];
                                            // 该期的商品参与人次增加一
                                            $goods_ids[] = $goodsInfo['goods_id'];
                                            $this->modelGoods->incPurchasePersonTime($goodsInfo['goods_id'], 1);
                                        }
                                    }
                                }
                                // 云购码的数量
                                $lottery_code_num = count($lottery_code_list);
                                if ($lottery_code_num > 0) {
                                    $lottery_code = implode(',', $lottery_code_list);
                                } else {
                                    $lottery_code = '';
                                }
                                // 购买失败次数 = 商品购买次数-云购码的数量
                                $failure_num = $goodsInfo['goods_num'] - $lottery_code_num;
                                
                                // 购买失败次数
                                if ($failure_num > 0) {
                                    // 失败个数加1
                                    $failure_count += $failure_num;
                                } else {
                                    // 成功个数加1
                                    $success_count += $lottery_code_num;
                                }
                                // 更新成功购买的标志为成功
                                $this->modelOrderGoods->updateIsSuccess($goodsInfo['_id'], true, $lottery_code, $failure_num);
                                
                                // 记录会员动态
                                $this->modelMemberNews->log($buyerInfo['buyer_id'], $buyerInfo['buyer_name'], $buyerInfo['buyer_avatar'], $buyerInfo['buyer_register_by'], \App\Member\Models\News::ACTION1, $goodsInfo['_id'], $goodsInfo);
                                // 如果成功购买次数大于0
                                if ($lottery_code_num > 0) {
                                    // 增加会员的积分
                                    // 参与云购每消费1元 1 10 生日当月享双倍福分
                                    $currentTime = getCurrentTime();
                                    $double = 1;
                                    if (! empty($buyerInfo['birthday']) && substr($buyerInfo['birthday'], 5) == date('m-d', $currentTime->sec)) {
                                        $double = 2;
                                    }
                                    $unique_id = getNewId();
                                    $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'buy');
                                    $this->modelPointsUser->addOrReduce(POINTS_CATEGORY1, $buyerInfo['buyer_id'], $buyerInfo['buyer_name'], $buyerInfo['buyer_avatar'], $unique_id, null, $pointsRuleInfo['points'] * $double * $lottery_code_num, $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                                    
                                    $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY2, 'buy');
                                    $this->modelPointsUser->addOrReduce(POINTS_CATEGORY2, $buyerInfo['buyer_id'], $buyerInfo['buyer_name'], $buyerInfo['buyer_avatar'], $unique_id, null, $pointsRuleInfo['points'] * $double * $lottery_code_num, $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                                    
                                    // 增加支付日志记录
                                    $this->modelPayLog->recordLog($buyerInfo['buyer_id'], \App\Payment\Models\Log::TYPE2, $lottery_code_num, '云购商品', $goodsInfo);
                                }
                            }
                        }
                        
                        // 退回的金额
                        $pay_amount = $success_count * 100;
                        if ($total_amount - $pay_amount > 0) {
                            $failure_amount = $total_amount - $pay_amount;
                            $failure_amount_yuan = $failure_amount_yuan / 100;
                            // 退回支付金额至预付款中
                            $this->modelPointsUser->addOrReduce(POINTS_CATEGORY3, $orderPayInfo['buyer_id'], $buyerInfo['buyer_name'], $buyerInfo['buyer_avatar'], getNewId(), $orderPayInfo['__CREATE_TIME__'], $failure_amount, "支付", "云购失败，支付金额￥{$failure_amount_yuan}已退回到您的云购账户");
                        }
                        
                        // 更新支付订单的信息
                        $this->modelOrderPay->incSuccessAndFailureCount($orderPayInfo['_id'], $success_count, $failure_count);
                        
                        // 更新购买用户的获取商品的数量
                        $this->modelMember->incBuyNum($buyerInfo['buyer_id'], $success_count);
                        
                        // 如果该购买用户是从邀请过来的话，需要给邀请人加积分
                        if (! empty($buyerInfo['inviter_id'])) {
                            $invitationInfo = $this->modelInvitation->getInfoById($buyerInfo['inviter_id']);
                            if (! empty($invitationInfo)) {
                                // 每成功邀请1位好友并消费 50 50
                                $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'invitation');
                                $worth = $pointsRuleInfo['points'];
                                $this->modelPointsUser->addOrReduce(POINTS_CATEGORY1, $invitationInfo['user_id'], $invitationInfo['user_name'], $invitationInfo['user_headimgurl'], $orderPayInfo['_id'], null, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                                $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY2, 'invitation');
                                $this->modelPointsUser->addOrReduce(POINTS_CATEGORY2, $invitationInfo['user_id'], $invitationInfo['user_name'], $invitationInfo['user_headimgurl'], $orderPayInfo['_id'], null, $pointsRuleInfo['points'], $pointsRuleInfo['item_category'], $pointsRuleInfo['item']);
                                
                                // 增加该邀请函的价值
                                $this->modelInvitation->incWorth($invitationInfo['_id'], $worth, 1);
                                // 增加领取记录的价值
                                $this->modelInvitationGotDetail->incWorth($invitationInfo['_id'], $orderPayInfo['buyer_id'], $worth, 1);
                            }
                        }
                        
                        // 更新订单统计的信息
                        $orderPayInfo = $this->modelOrderPay->getInfoById($orderPayInfo['_id']);
                        $this->modelOrderStatistics->incStatisticsInfo(YUNGOU_ORDER_STATISTICS_ID, $orderPayInfo);
                        
                        break;
                }
                
                $this->modelOrderPay->commit();
                
                if (! empty($goods_ids)) {
                    $goods_ids = array_unique($goods_ids);
                    foreach ($goods_ids as $goods_id) {
                        // 后续的操作由队列处理
                        // 入新期的商品处理队列
                        \iQueue::enqueue4Newperiodgoods(array(
                            'goods_id' => $goods_id
                        ));
                        
                        // 入抽奖处理队列
                        \iQueue::enqueue4Lotterygoods(array(
                            'goods_id' => $goods_id
                        ));
                    }
                }
                $result = array(
                    'buyer_id' => $buyerInfo['buyer_id'],
                    'pay_sn' => $pay_sn,
                    'success_count' => $success_count,
                    'failure_count' => $failure_count
                );
                $this->modelTaskLog->log('支付完成', true, $request, $result);
            } catch (\Exception $e) {
                $this->modelOrderPay->rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            $ret['error_code'] = $e->getCode();
            $ret['error_msg'] = $e->getMessage();
            $this->modelTaskLog->log('支付完成', false, $request, $ret);
        }
        return $ret;
    }
}