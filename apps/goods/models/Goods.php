<?php
namespace App\Goods\Models;

class Goods extends \App\Common\Models\Goods\Goods
{

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
            'salenum' => 1,
            'click' => 1,
            '_id' => - 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getDefaultQuery()
    {
        $query = array(
            'state' => self::STATE1,
            'verify' => self::VERIFY1,
            'storage' => array(
                '$gt' => 0
            )
        );
        return $query;
    }

    /**
     * 根据某种条件获取分页列表
     *
     * @param array $query            
     * @param array $sort            
     * @param array $fields            
     * @return array
     */
    public function getList(array $query = array(), array $sort = array(), array $fields = array())
    {
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $defaultQuery = $this->getDefaultQuery();
        $query = array_merge($query, $defaultQuery);
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort, $fields);
        $cache = $this->getDI()->get("cache");
        $list = false; // $cache->get($key);
        if (empty($list)) {
            $list = $this->findAll($query, $sort, $fields);
            $cache->save($key, $list, 60 * 60); // 一个小时
        }
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
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort, $fields, $page, $limit);
        $cache = $this->getDI()->get("cache");
        $list = false; // $cache->get($key);
        if (empty($list)) {
            $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, $fields);
            $cache->save($key, $list, 60 * 60); // 一个小时
        }
        return $list;
    }

    /**
     * 商品收藏次数加1
     *
     * @param string $goods_commonid            
     */
    public function incCollectNum($goods_commonid)
    {
        $query = array();
        $query['goods_commonid'] = $goods_commonid;
        $data = array();
        $data['collect'] = 1;
        $this->update($query, array(
            '$inc' => $data
        ));
    }

    /**
     * 根据公共商品生成新一期的商品
     *
     * @param number $period            
     * @param array $goodsCommonInfo            
     */
    public function createNewPeriodGoodsByGoodsCommon($period, array $goodsCommonInfo)
    {
        $datas = array();
        $datas['goods_commonid'] = $goodsCommonInfo['_id'];
        $datas['name'] = $goodsCommonInfo['name'];
        $datas['jingle'] = $goodsCommonInfo['jingle'];
        $datas['price'] = $goodsCommonInfo['price'];
        $datas['image'] = $goodsCommonInfo['image'];
        $datas['state'] = $goodsCommonInfo['state'];
        $datas['verify'] = $goodsCommonInfo['verify'];
        $datas['is_hot'] = $goodsCommonInfo['is_hot'];
        $datas['is_new'] = $goodsCommonInfo['is_new'];
        
        $datas['period'] = $period;
        $datas['lottery_code'] = $goodsCommonInfo['lottery_code'];
        $datas['total_person_time'] = intval($goodsCommonInfo['price'] / 100);
        $datas['purchase_person_time'] = 0;
        $datas['remain_person_time'] = $datas['total_person_time'] - $datas['purchase_person_time'];
        $datas['complete_percent'] = round($datas['purchase_person_time'] * 100.00 / $datas['total_person_time'], 2);
        $datas['restrict_person_time'] = $goodsCommonInfo['restrict_person_time'];
        $datas['sale_state'] = self::SALE_STATE1; // 进行中
        
        $datas['gc_id'] = $goodsCommonInfo['gc_id'];
        $datas['gc_id_1'] = $goodsCommonInfo['gc_id_1'];
        $datas['gc_id_2'] = $goodsCommonInfo['gc_id_2'];
        $datas['gc_id_3'] = $goodsCommonInfo['gc_id_3'];
        $datas['brand_id'] = $goodsCommonInfo['brand_id'];
        $datas['collect'] = $goodsCommonInfo['collect'];
        $datas['commend'] = $goodsCommonInfo['commend'];
        $datas['click'] = 0;
        $datas['salenum'] = 0;
        $datas['storage'] = 1;
        return $this->insert($datas);
    }

    /**
     * 分页获取人气推荐商品列表
     *
     * @param number $page            
     * @param number $limit            
     * @return array
     */
    public function getCommendList($page = 1, $limit = 4)
    {
        $query = $this->getDefaultQuery();
        $query['sale_state'] = self::SALE_STATE1;
        $query['commend'] = true;
        $sort = $this->getDefaultSort();
        $list = $this->getPageList($page, $limit, $query, $sort, array());
        return $list;
    }

    /**
     * 分页获取热门商品列表
     *
     * @param number $page            
     * @param number $limit            
     * @return array
     */
    public function getHotList($page = 1, $limit = 8)
    {
        $query = $this->getDefaultQuery();
        $query['sale_state'] = self::SALE_STATE1;
        $query['is_hot'] = true;
        $sort = $this->getDefaultSort();
        $list = $this->getPageList($page, $limit, $query, $sort, array());
        return $list;
    }

    /**
     * 分页获取即将揭晓商品列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @return array
     */
    public function getAnnouncedSoonList($page = 1, $limit = 24, array $otherConditions = array())
    {
        $query = $this->getDefaultQuery();
        $query['sale_state'] = self::SALE_STATE1;
        $query['is_hot'] = false;
        $query['is_new'] = false;
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $sort = array(
            'complete_percent' => - 1
        );
        $list = $this->getPageList($page, $limit, $query, $sort, array());
        return $list;
    }

    /**
     * 分页获取新品上架商品列表
     *
     * @param number $page            
     * @param number $limit            
     * @return array
     */
    public function getNewList($page = 1, $limit = 4)
    {
        $query = $this->getDefaultQuery();
        $query['sale_state'] = self::SALE_STATE1;
        $query['is_new'] = true;
        $sort = $this->getDefaultSort();
        $list = $this->getPageList($page, $limit, $query, $sort, array());
        return $list;
    }

    /**
     * 获取云列表
     *
     * @param string $goods_common_id            
     * @return array
     */
    public function getPeriodList($goods_common_id, $page = 1, $limit = 100)
    {
        $query = $this->getDefaultQuery();
        $query['goods_commonid'] = $goods_common_id;
        $sort = array();
        $sort['period'] = - 1;
        $list = $this->getPageList($page, $limit, $query, $sort, array(
            '_id' => 1,
            'period' => 1
        ));
        return $list;
    }

    /**
     * 获取云信息
     *
     * @param string $goods_common_id            
     * @param number $period            
     * @return array
     */
    public function getPeriodInfo($goods_common_id, $period)
    {
        $query = $this->getDefaultQuery();
        $query['goods_commonid'] = $goods_common_id;
        $query['period'] = $period;
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 检查商品是否有效
     *
     * @param string $goods_id            
     * @param number $quantity            
     * @return array
     */
    public function checkGoods($goods_id, $quantity)
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        // 检查商品是否存在
        $goodsInfo = $this->getInfoById($goods_id);
        if (empty($goodsInfo)) {
            $ret['error_code'] = - 1;
            $ret['error_msg'] = '商品不存在';
            return $ret;
        }
        
        // 检查该商品的库存
        if (empty($goodsInfo['storage']) || $goodsInfo['storage'] < $quantity) {
            $ret['error_code'] = - 2;
            $ret['error_msg'] = '商品库存不足';
            return $ret;
        }
        
        // 检查该商品的剩余人次
        if (empty($goodsInfo['remain_person_time']) || $goodsInfo['remain_person_time'] < $quantity) {
            $ret['error_code'] = - 3;
            $ret['error_msg'] = '商品剩余人次不足';
            return $ret;
        }
        $ret['result'] = $goodsInfo;
        return $ret;
    }

    /**
     * 检查是否已满
     *
     * @param array $goodsInfo            
     * @return boolean
     */
    public function checkIsFull(array $goodsInfo)
    {
        // 检查该商品的剩余人次
        if (! empty($goodsInfo['remain_person_time'])) {
            return false;
        }
        return true;
    }

    /**
     * 更新云购奖品
     *
     * @param string $id            
     * @param string $lottery_prize_id            
     */
    public function updateLotteryPrizeId($id, $lottery_prize_id)
    {
        // 商品受关注次数加1
        $query = array();
        $query['_id'] = $id;
        $data = array();
        $data['lottery_prize_id'] = $lottery_prize_id;
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 商品云购参与人次加1
     *
     * @param string $goods_id            
     * @param number $times            
     */
    public function incPurchasePersonTime($goods_id, $times = 1)
    {
        // 商品参与人次加1
        $query = array();
        $query['_id'] = $goods_id;
        $query['sale_state'] = self::SALE_STATE1;
        $query['purchase_person_time'] = array(
            '$gte' => 0
        );
        $query['remain_person_time'] = array(
            '$gte' => $times
        );
        $data = array();
        $data['purchase_person_time'] = $times;
        $data['remain_person_time'] = - $times;
        $data['last_purchase_time'] = round(microtime(true), 3);
        $ret = $this->update($query, array(
            '$inc' => $data,
            '$exp' => array(
                'complete_percent' => 'round(purchase_person_time*100/total_person_time,2)',
                'sale_state' => sprintf('IF(remain_person_time>0,%s,%s)', self::SALE_STATE1, self::SALE_STATE2)
            )
        ));
    }

    /**
     * 更新中奖码信息
     *
     * @param string $goods_id            
     * @param string $prize_code            
     * @param array $orderGoodsList            
     * @param array $prizeOrderGoodsInfo            
     */
    public function updatePrizeCode($goods_id, $prize_code, $prize_time, array $orderGoodsList, array $prizeOrderGoodsInfo, $last_purchase_time, $total_time)
    {
        $query = array();
        $query['_id'] = $goods_id;
        $data = array();
        $data['sale_state'] = self::SALE_STATE3; // 已揭晓
        $data['prize_code'] = $prize_code;
        $data['prize_time'] = $prize_time;
        $data['order_goods_list'] = json_encode($orderGoodsList);
        $data['prize_buyer_id'] = $prizeOrderGoodsInfo['buyer_id'];
        $data['prize_buyer_name'] = $prizeOrderGoodsInfo['buyer_name'];
        $data['prize_buyer_avatar'] = $prizeOrderGoodsInfo['buyer_avatar'];
        $data['prize_buyer_register_by'] = $prizeOrderGoodsInfo['buyer_register_by'];
        $data['prize_buyer_purchase_num'] = $prizeOrderGoodsInfo['purchase_num'];
        $data['prize_buyer_purchase_time'] = $prizeOrderGoodsInfo['purchase_time'];
        $data['prize_order_goods_id'] = $prizeOrderGoodsInfo['_id'];
        $data['prize_order_goods_order_no'] = $prizeOrderGoodsInfo['order_no'];
        $data['prize_buyer_ip'] = $prizeOrderGoodsInfo['buyer_ip'];
        $data['prize_buyer_lottery_code'] = $prizeOrderGoodsInfo['lottery_code'];
        // $data['last_purchase_time'] = $last_purchase_time;
        $data['prize_total_time'] = $total_time;
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 分页获取最新揭晓的商品列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @return array
     */
    public function getStartRaffleList($page = 1, $limit = 5, array $otherConditions = array())
    {
        // 已揭晓
        $query = array(
            'sale_state' => self::SALE_STATE3
        );
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $sort = array(
            'prize_time' => - 1
        );
        $list = $this->getPageList($page, $limit, $query, $sort, array());
        return $list;
    }

    /**
     * 获取最新揭晓的商品总数列表
     *
     * @param array $otherConditions            
     * @return number
     */
    public function getRaffleCount(array $otherConditions = array())
    {
        // 已揭晓
        $query = array(
            'sale_state' => self::SALE_STATE3
        );
        $query['prize_time'] = array(
            '$lte' => round(microtime(true), 3)
        );
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $num = $this->count($query);
        return $num;
    }

    /**
     * 分页获取需要揭晓中的商品列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @return array
     */
    public function getProcessingList($page = 1, $limit = 5, array $otherConditions = array())
    {
        // 揭晓中
        $query = array(
            'sale_state' => self::SALE_STATE2
        );
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $sort = array(
            'last_purchase_time' => 1
        );
        $list = $this->getPageList($page, $limit, $query, $sort, array());
        return $list;
    }

    public function getSaleState($sale_state)
    {
        $saleStateDatas = self::SALESTATEDATAS;
        return $saleStateDatas[$sale_state]['name'];
    }
}