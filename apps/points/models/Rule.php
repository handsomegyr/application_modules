<?php
namespace App\Points\Models;

class Rule extends \App\Common\Models\Points\Rule
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'item_category' => 1,
            'code' => 1,
            'points' => - 1
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
     * 获取所有积分规则
     *
     * @return array
     */
    public function getAll()
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $ruleList = $cache->get($key);
        if (empty($ruleList)) {
            $query = $this->getQuery();
            $sort = $this->getDefaultSort();
            $list = $this->findAll($query, $sort);
            $ruleList = array();
            if (! empty($list)) {
                foreach ($list as $item) {
                    $ruleList[$item['code']][$item['category']] = $item;
                }
            }
            if (! empty($ruleList)) {
                $cache->save($key, $ruleList, 60 * 60 * 24); // 24小时
            }
        }
        return $ruleList;
    }

    /**
     * 根据积分分类和唯一码获取记录信息
     *
     * @param string $category            
     * @param string $code            
     */
    public function getInfoByCategoryAndCode($category, $code)
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $category, $code);
        $cache = $this->getDI()->get("cache");
        $info = $cache->get($key);
        if (empty($info)) {
            $query = array();
            $query['category'] = $category;
            $query['code'] = $code;
            $info = $this->findOne($query);
            if (! empty($info)) {
                $cache->save($key, $info, 60 * 60 * 24); // 24小时
            }
        }
        return $info;
    }
}