<?php
namespace App\Goods\Models;

class Brand extends \App\Common\Models\Goods\Brand
{

    /**
     * 默认排序方式
     *
     * @param number $dir            
     * @return array
     */
    public function getDefaultSort($dir = 1)
    {
        $sort = array();
        $sort['sort'] = $dir;
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
    public function getList(array $query = array(), array $sort = array(), array $fields = array())
    {
        if (empty($sort)) {
            $sort = $this->getDefaultSort(- 1);
        }
        $defaultQuery = $this->getDefaultQuery();
        $query = array_merge($query, $defaultQuery);
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort, $fields);
        $cache = $this->getDI()->get("cache");
        $list = false; // $cache->get($key);
        if (empty($list)) {
            $list = $this->findAll($query, $sort, $fields);
            if (! empty($list)) {
                $cache->save($key, $list, 60 * 60); // 一个小时
            }
        }
        return $list;
    }

    /**
     * 获取所有的品牌
     *
     * @return array
     */
    public function getAll()
    {
        $list = $this->getList();
        return $list;
    }
}