<?php
namespace App\Goods\Services;

class Goods
{

    private $modelPost = null;

    private $modelMember = null;

    private $modelGoods = null;

    private $modelGoodsCommon = null;

    private $modelPrize = null;

    private $modelPrizeCode = null;

    private $modelLotteryRule = null;

    private $modelOrder = null;

    private $modelOrderLog = null;

    private $modelOrderGoods = null;

    private $modelTaskLog = null;

    public function __construct()
    {
        $this->modelPost = new \App\Post\Models\Post();
        $this->modelMember = new \App\Member\Models\Member();
        $this->modelGoods = new \App\Goods\Models\Goods();
        $this->modelGoodsCommon = new \App\Goods\Models\GoodsCommon();
        $this->modelPrize = new \App\Prize\Models\Prize();
        $this->modelPrizeCode = new \App\Prize\Models\Code();
        $this->modelLotteryRule = new \App\Lottery\Models\Rule();
        $this->modelOrder = new \App\Order\Models\Order();
        $this->modelOrderLog = new \App\Order\Models\Log();
        $this->modelOrderGoods = new \App\Order\Models\Goods();
        $this->modelGoodsCollect = new \App\Goods\Models\Collect();
        $this->modelTaskLog = new \App\Task\Models\Log();
    }

    /**
     * 生成新一期的商品信息
     */
    public function createNewPeriodGoods($goods_commonid)
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        
        $request = array(
            'goods_commonid' => $goods_commonid
        );
        
        try {
            // 生成新的一期
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $goods_commonid);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                throw new \Exception('根据公共商品生成新一期的商品在处理中，请等待', - 99);
            }
            try {
                $this->modelGoodsCommon->begin();
                
                // lock
                $goodsCommonInfo = $this->modelGoodsCommon->findOne(array(
                    '_id' => $goods_commonid,
                    '__FOR_UPDATE__' => true
                ));
                
                // 当期数 超过了 最大期数的时候 报错
                if ($goodsCommonInfo['current_period'] >= $goodsCommonInfo['max_period']) {
                    throw new \Exception('已到达最大期数', - 4);
                }
                
                if ($goodsCommonInfo['current_period'] > 0 && empty($goodsCommonInfo['period_goods_id'])) {
                    throw new \Exception('现期的商品信息为空', - 5);
                }
                
                // 检查现期的商品是否存在
                if (! empty($goodsCommonInfo['period_goods_id'])) {
                    $period_goods_id = $goodsCommonInfo['period_goods_id'];
                    
                    // 检查是否存在
                    $periodGoodsInfo = $this->modelGoods->getInfoById($period_goods_id);
                    if (empty($periodGoodsInfo)) {
                        throw new \Exception('现期的商品不存在', - 6);
                    }
                    
                    // 检查是否满
                    $isFull = $this->modelGoods->checkIsFull($periodGoodsInfo);
                    if (empty($isFull)) {
                        throw new \Exception('现期的商品还未满员', - 7);
                    }
                }
                // 现有期数+1
                $period = $goodsCommonInfo['current_period'] + 1;
                
                // 根据公用商品生成新一期的商品
                $newPeriodGoodsInfo = $this->modelGoods->createNewPeriodGoodsByGoodsCommon($period, $goodsCommonInfo);
                if (empty($newPeriodGoodsInfo)) {
                    throw new \Exception('商品生成失败', - 8);
                }
                
                // 更新当期次数
                $retUpdate = $this->modelGoodsCommon->updateCurrentPeriod($goodsCommonInfo['_id'], $period, $newPeriodGoodsInfo['_id']);
                
                // 检查是否已经生成
                $prizeInfo = $this->modelPrize->getInfoByCode($newPeriodGoodsInfo['_id']);
                if (! empty($prizeInfo)) {
                    throw new \Exception('新一期商品对应的奖品已生成', - 9);
                }
                // 生成新一期的商品对应的奖品
                $prizeInfo = $this->modelPrize->create($newPeriodGoodsInfo['_id'], $newPeriodGoodsInfo['name'], true, true, 0, true);
                if (empty($prizeInfo)) {
                    throw new \Exception('新一期商品对应的奖品生成失败', - 10);
                }
                
                // 更新云购奖品
                $this->modelGoods->updateLotteryPrizeId($newPeriodGoodsInfo['_id'], $prizeInfo['_id']);
                
                // 根据总云购人次生成云购码
                for ($i = 0; $i < $newPeriodGoodsInfo['total_person_time']; $i ++) {
                    // 生成新一期的商品对应的云购码
                    $newPrizeCodeInfo = $this->modelPrizeCode->create($prizeInfo['_id'], $newPeriodGoodsInfo['lottery_code'] + $i);
                    if (empty($newPrizeCodeInfo)) {
                        throw new \Exception('云购码生成失败', - 11);
                    }
                }
                // 生成抽奖规则
                $lotterRuleInfo = $this->modelLotteryRule->create(YUNGOU_ACTIVITY_ID, $prizeInfo['_id'], $newPeriodGoodsInfo['total_person_time'], 10000);
                if (empty($lotterRuleInfo)) {
                    throw new \Exception('云购码抽奖规则生成失败', - 12);
                }
                $this->modelGoodsCommon->commit();
                
                $result = array(
                    'new_period' => $newPeriodGoodsInfo['period'],
                    'new_period_goods_id' => $newPeriodGoodsInfo['_id']
                );
                $this->modelTaskLog->log('新一期商品生成', true, $request, $result);
            } catch (\Exception $e) {
                $this->modelGoodsCommon->rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            $ret['error_code'] = $e->getCode();
            $ret['error_msg'] = $e->getMessage();
            $this->modelTaskLog->log('新一期商品生成', false, $request, $ret);
        }
        return $ret;
    }

    /**
     * 满员商品抽奖
     */
    public function lottery($goods_id)
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        $request = array(
            'goods_id' => $goods_id
        );
        try {
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $goods_id);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                throw new \Exception('满员的商品在处理中，请等待', - 99);
            }
            try {
                $this->modelGoods->begin();
                
                // lock
                $goodsInfo = $this->modelGoods->findOne(array(
                    '_id' => $goods_id,
                    'remain_person_time' => 0,
                    'sale_state' => array(
                        '$lt' => \App\Goods\Models\Goods::SALE_STATE3
                    ),
                    '__FOR_UPDATE__' => true
                ));
                
                if (empty($goodsInfo)) {
                    throw new \Exception('揭晓中的满员商品不存在', - 1);
                }
                
                // 检查是否满员
                $isFull = $this->modelGoods->checkIsFull($goodsInfo);
                if (empty($isFull)) {
                    throw new \Exception('商品还未满员', - 4);
                }
                
                if ($goodsInfo['sale_state'] == \App\Goods\Models\Goods::SALE_STATE3) {
                    throw new \Exception('商品已揭晓', - 5);
                }
                // 如何计算？
                // 1、取该商品最后购买时间前网站所有商品的最后100条购买时间记录；
                // 2、按时、分、秒、毫秒排列取值之和，除以该商品总参与人次后取余数；
                // 3、余数加上10000001 即为“幸运云购码”；
                // 4、余数是指整数除法中被除数未被除尽部分， 如7÷3 = 2 ......1，1就是余数 。
                // $lastPurchaseInfo = $this->modelOrderGoods->getLastPurchaseInfo($goods_id);
                // if (empty($lastPurchaseInfo)) {
                // throw new \Exception('该商品最后购买记录未找到', - 6);
                // }
                // $last_purchase_time = $lastPurchaseInfo['purchase_time'];
                $last_purchase_time = $goodsInfo['last_purchase_time'];
                $orderGoodsList = $this->modelOrderGoods->getLastPurchaseList($last_purchase_time, 100);
                $total_time = 0;
                foreach ($orderGoodsList as $orderGoods) {
                    list ($sec, $msec) = explode(".", $orderGoods['purchase_time']);
                    $mill = date('His', $sec) . $msec;
                    $total_time += $mill;
                }
                $prize_code = $goodsInfo['lottery_code'] + $total_time % $goodsInfo['total_person_time'];
                
                // 获取中奖的订单商品记录
                $orderGoodsInfo = $this->modelOrderGoods->getInfoByGoodsIdAndLotteryCode($goodsInfo['_id'], $prize_code);
                $order_no = getNewId();
                $order_state = \App\Order\Models\Goods::ORDER_STATE1;
                $orderGoodsInfo['order_no'] = $order_no;
                $orderGoodsInfo['order_state'] = $order_state;
                
                // 更新该订单商品记录的订单信息
                $this->modelOrderGoods->updateOrderNoById($orderGoodsInfo['_id'], $order_no, $order_state);
                
                // 更新购买用户的获取商品的数量
                $this->modelMember->incPrizedNum($orderGoodsInfo['buyer_id']);
                // 记录订单日志记录
                $this->modelOrderLog->log($order_no, \App\Order\Models\Goods::ORDER_STATE1, "恭喜您云购成功，请尽快填写收货地址，以便我们为您配送！", \App\Order\Models\Log::ROLE_SYSTEM, YUNGOU_SITE_ID, '云购系统');
                
                // 更新该商品的中奖码信息
                $prize_time = getMilliTime() + 3 * 60; // 3分钟后揭晓
                $this->modelGoods->updatePrizeCode($goods_id, $prize_code, $prize_time, $orderGoodsList, $orderGoodsInfo, $last_purchase_time, $total_time);
                
                // 更新订单商品的中奖码信息
                $this->modelOrderGoods->updatePrizeCodeByGoodsId($goods_id, $prize_code, $prize_time, $orderGoodsInfo);
                
                $this->modelGoods->commit();
                
                $result = array(
                    'order_goods_id' => $orderGoodsInfo['_id'],
                    'prize_code' => $prize_code,
                    'order_no' => $order_no
                );
                $this->modelTaskLog->log('满员商品揭晓', true, $request, $result);
            } catch (\Exception $e) {
                $this->modelGoods->rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            $ret['error_code'] = $e->getCode();
            $ret['error_msg'] = $e->getMessage();
            $this->modelTaskLog->log('满员商品揭晓', false, $request, $ret);
        }
        return $ret;
    }
}