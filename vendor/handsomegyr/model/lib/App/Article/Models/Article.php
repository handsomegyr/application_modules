<?php
namespace App\Article\Models;

class Article extends \App\Common\Models\Article\Article
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
    public function getPageList($page = 1, $limit = 10, array $query = array(), array $sort = array(), array $fields = array())
    {
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $defaultQuery = $this->getDefaultQuery();
        $query = array_merge($query, $defaultQuery);
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, $fields);
        return $list;
    }

    /**
     * 新闻公告
     */
    public function getNewsList($page = 1, $limit = 3)
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $page, $limit);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            $list = $this->getPageList($page, $limit);
            if (! empty($list['datas'])) {
                $cache->save($key, $list, 60 * 60); // 一个小时
            }
        }
        return $list['datas'];
    }
}