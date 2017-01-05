<?php
namespace App\Site\Models;

class Banner extends \App\Common\Models\Site\Banner
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
            if (! empty($list)) {
                $cache->save($key, $list, 60 * 60); // 一个小时
            }
        }
        return $list;
    }

    /**
     * 获取所有的信息
     *
     * @return array
     */
    public function getAll()
    {
        $list = $this->getList();
        return $list;
    }
}