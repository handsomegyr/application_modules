<?php
namespace App\Goods\Models;

class Ad extends \App\Common\Models\Goods\Ad
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
        $sort['show_order'] = 1;
        return $sort;
    }

    /**
     * 默认查询条件
     *
     * @return array
     */
    public function getDefaultQuery()
    {
        $query = array();
        $query['is_show'] = true;
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
    public function getGoodsIdList(array $query = array(), array $sort = array(), array $fields = array())
    {
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $defaultQuery = $this->getDefaultQuery();
        $query = array_merge($query, $defaultQuery);
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort, $fields);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            $ret = $this->findAll($query, $sort, $fields);
            $list = array();
            if (! empty($ret)) {
                foreach ($ret as $item) {
                    $list[] = $item['goods_id'];
                }
                $cache->save($key, $list, 60 * 60); // 一个小时
            }
        }
        return $list;
    }
}