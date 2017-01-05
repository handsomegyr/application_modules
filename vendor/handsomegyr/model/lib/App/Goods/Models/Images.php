<?php
namespace App\Goods\Models;

class Images extends \App\Common\Models\Goods\Images
{

    /**
     * 默认排序方式
     *
     * @return array
     */
    public function getDefaultSort()
    {
        $sort = array();
        // 默认的排序方式
        $sort = array(
            'is_default' => - 1,
            'sort' => 1
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
     * 根据某种条件获取列表
     *
     * @param array $query            
     * @param array $sort            
     * @param array $fields            
     * @return array
     */
    public function getList(array $query = array(), array $sort = array('is_default'=>-1 ,'sort'=>1 ), array $fields = array())
    {
        if (empty($sort)) {
            $sort = $this->getDefaultSort(- 1);
        }
        $defaultQuery = $this->getDefaultQuery();
        $query = array_merge($query, $defaultQuery);
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort, $fields);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            $list = $this->findAll($query, $sort, $fields);
            $cache->save($key, $list, 60 * 60); // 一个小时
        }
        return $list;
    }

    /**
     * 根据商品公共ID列表以及颜色ID列表以及门店ID列表获取列表信息
     *
     * @param array $goods_commonids            
     * @param array $color_ids            
     * @param array $store_ids            
     * @return array
     */
    public function getListByGoodsCommonColorAndStoreIds(array $goods_commonids, array $color_ids, array $store_ids)
    {
        $list = array();
        $query = array();
        if (! empty($goods_commonids)) {
            $goods_commonids = array_values($goods_commonids);
            $query['goods_commonid'] = array(
                '$in' => $goods_commonids
            );
        }
        if (! empty($color_ids)) {
            $color_ids = array_values($color_ids);
            $query['color_id'] = array(
                '$in' => $color_ids
            );
        }
        if (! empty($store_ids)) {
            $store_ids = array_values($store_ids);
            $query['store_id'] = array(
                '$in' => $store_ids
            );
        }
        if (! empty($query)) {
            $list = $this->getList($query);
        }
        return $list;
    }
}