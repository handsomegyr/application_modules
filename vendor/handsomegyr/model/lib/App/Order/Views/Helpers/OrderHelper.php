<?php
namespace App\Order\Views\Helpers;

use App\Order\Models\Log;
use App\Order\Models\Goods;

class OrderHelper extends \Phalcon\Tag
{

    /**
     * 获取会员各种不同的订单数量
     *
     * @return number
     */
    static public function getCount($member_id, $order_type)
    {
        // 待确认 待发货 待收货
        $num = 0;
        return $num;
    }

    static public function getLogName(array $logInfo)
    {
        $modelLog = new Log();
        return $modelLog->getLogName($logInfo);
    }

    /**
     * 获取我的云购记录
     *
     * @param string $buyer_id            
     * @param string $goods_id            
     */
    static public function getUserBuyList($buyer_id, $goods_id)
    {
        $modelOrderGoods = new Goods();
        $otherConditions = array(
            'goods_id' => $goods_id
        );
        $orderGoodsList = $modelOrderGoods->getUserBuyList($buyer_id, 1, 1000, 0, 0, 0, $otherConditions);
        $total_buy_num = 0;
        $total_lottery_code_num = 0;
        $lotteryCodeList = array();
        $lastPurchaseTime = 0;
        $buyList = array();
        
        if (! empty($orderGoodsList['datas'])) {
            foreach ($orderGoodsList['datas'] as $orderGoods) {
                $buyList[] = array(
                    'buy_num' => $orderGoods['purchase_num'],
                    'purchase_time' => $orderGoods['purchase_time'],
                    'lottery_code' => $orderGoods['lottery_code']
                );
                if (empty($lastPurchaseTime)) {
                    $lastPurchaseTime = getMilliTime4Show($orderGoods['purchase_time']);
                }
                $lottery_code = explode(',', $orderGoods['lottery_code']);
                $lotteryCodeList = array_merge($lotteryCodeList, $lottery_code);
                $total_buy_num += $orderGoods['purchase_num'];
                $total_lottery_code_num += $orderGoods['purchase_num'];
            }
        }
        return array(
            'buyList' => $buyList,
            'lotteryCodeList' => array_values($lotteryCodeList),
            'lastPurchaseTime' => $lastPurchaseTime,
            'total_buy_num' => $total_buy_num,
            'total_lottery_code_num' => $total_lottery_code_num
        );
    }
}