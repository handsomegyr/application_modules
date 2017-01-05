<?php
namespace App\System\Models;

class Enum extends \App\Common\Models\System\Enum
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'show_order' => 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array(
            'is_show' => true
        );
        return $query;
    }

    /**
     * 获取性别列表
     *
     * @return array
     */
    public function getSexList()
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            $query = array(
                'code' => 1,
                'pid' => ''
            );
            $info = $this->findOne($query);
            
            // 获取该分类下的所有信息
            $query = $this->getQuery();
            $query['pid'] = $info['_id'];
            $sort = $this->getDefaultSort();
            $datas = $this->findAll($query, $sort);
            $list = array();
            if (! empty($datas)) {
                foreach ($datas as $item) {
                    $list[$item['code']] = $item['name'];
                }
            }
            if (! empty($list)) {
                $cache->save($key, $list, 60 * 60 * 24); // 24小时
            }
        }
        return $list;
    }

    /**
     * 获取星座列表
     *
     * @return array
     */
    public function getConstellationList()
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            $query = array(
                'code' => 2,
                'pid' => ''
            );
            $info = $this->findOne($query);
            // 获取该分类下的所有信息
            $query = $this->getQuery();
            $query['pid'] = $info['_id'];
            $sort = $this->getDefaultSort();
            $datas = $this->findAll($query, $sort);
            $list = array();
            if (! empty($datas)) {
                foreach ($datas as $item) {
                    $list[$item['code']] = $item['name'];
                }
            }
            if (! empty($list)) {
                $cache->save($key, $list, 60 * 60 * 24); // 24小时
            }
        }
        return $list;
    }

    /**
     * 获取月收入列表
     *
     * @return array
     */
    public function getMonthlyIncomeList()
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            $query = array(
                'code' => 3,
                'pid' => ''
            );
            $info = $this->findOne($query);
            
            // 获取该分类下的所有信息
            $query = $this->getQuery();
            $query['pid'] = $info['_id'];
            $sort = $this->getDefaultSort();
            $datas = $this->findAll($query, $sort);
            $list = array();
            if (! empty($datas)) {
                foreach ($datas as $item) {
                    $list[$item['code']] = $item['name'];
                }
            }
            if (! empty($list)) {
                $cache->save($key, $list, 60 * 60 * 24); // 24小时
            }
        }
        return $list;
    }
}