<?php
namespace App\Freight\Models;

class Express extends \App\Common\Models\Freight\Express
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
        $sort['letter'] = 1;
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
        $query['state'] = true;
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
        $list = $this->findAll($query, $sort, $fields);
        return $list;
    }

    /**
     * 获取所有的信息
     *
     * @return array
     */
    public function getAll()
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            $list = $this->getList();
            if (! empty($list)) {
                $cache->save($key, $list, 60 * 60); // 一个小时
            }
        }
        return $list;
    }
}