<?php
namespace App\Site\Models;

class Site extends \App\Common\Models\Site\Site
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

    public function getSettings($id)
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $id);
        $cache = $this->getDI()->get("cache");
        $info = $cache->get($key);
        if (empty($info)) {
            $info = $this->getInfoById($id);
            if (! empty($info)) {
                $cache->save($key, $info, 60 * 60); // 一个小时
            }
        }
        return $info;
    }
}