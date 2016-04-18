<?php
namespace App\Points\Models;

class Category extends \App\Common\Models\Points\Category
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'code' => 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 获取所有分类
     *
     * @return array
     */
    public function getAll()
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $categoryList = $cache->get($key);
        if (empty($categoryList)) {
            $query = $this->getQuery();
            $sort = $this->getDefaultSort();
            $list = $this->findAll($query, $sort);
            $categoryList = array();
            if (! empty($list)) {
                foreach ($list as $item) {
                    $categoryList[$item['code']] = $item['name'];
                }
            }
            if (! empty($categoryList)) {
                $cache->save($key, $categoryList, 60 * 60 * 24); // 24小时
            }
        }
        return $categoryList;
    }
}