<?php
namespace App\Goods\Models;

class GoodsCommon extends \App\Common\Models\Goods\GoodsCommon
{

    /**
     * 商品收藏次数加1
     *
     * @param string $goods_id
     */
    public function incCollectNum($goods_id)
    {
        $query = array();
        $query['_id'] = $goods_id;
        $data = array();
        $data['collect'] = 1;
        $this->update($query, array(
            '$inc' => $data
        ));
    }
    
    /**
     * 更新当前期数和当期商品ID
     *
     * @param string $id            
     * @param number $period            
     * @param string $period_goods_id            
     */
    public function updateCurrentPeriod($id, $period, $period_goods_id)
    {
        $query = array(
            '_id' => $id,
            'current_period' => array(
                '$lt' => $period
            ),
            'max_period' => array(
                '$gte' => $period
            )
        );
        $data = array();
        $data['current_period'] = $period;
        $data['period_goods_id'] = $period_goods_id;
        
        return $this->update($query, array(
            '$set' => $data
        ));
    }
    

    /**
     * 根据ID列表获取当期商品ID列表信息
     *
     * @param string $ids
     * @return array
     */
    public function getPeriodGoodsIdListByIds(array $ids)
    {        
        $list = $this->getListByIds($ids);
        $ret = array();
        if (! empty($list)) {
            foreach ($list as $item) {
                $ret[] = $item['period_goods_id'];
            }
        }
        return $ret;
    }
}