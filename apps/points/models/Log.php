<?php
namespace App\Points\Models;

class Log extends \App\Common\Models\Points\Log
{

    /**
     * 默认排序方式
     *
     * @param number $dir            
     * @return array
     */
    public function getDefaultSort($dir = -1)
    {
        $sort = array();
        $sort['add_time'] = $dir;
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
     * 根据唯一编号获取记录信息
     *
     * @param string $uniqueId            
     */
    public function getInfoByUniqueId($uniqueId, $category = 0)
    {
        $query = array(
            'unique_id' => strval($uniqueId),
            'category' => intval($category)
        );
        return $this->findOne($query);
    }

    /**
     * 根据某种条件获取分页列表
     *
     * @param array $query            
     * @param array $sort            
     * @param array $fields            
     * @return array
     */
    public function getPageList($page = 1, $limit = 10, array $query = array(), array $sort = array(), array $fields = array())
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort, $fields, $page, $limit);
        $cache = $this->getDI()->get("cache");
        $list = $cache->get($key);
        if (empty($list)) {
            if (empty($sort)) {
                $sort = $this->getDefaultSort(- 1);
            }
            $defaultQuery = $this->getDefaultQuery();
            $query = array_merge($query, $defaultQuery);
            $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, $fields);
            if (! empty($list['datas'])) {
                $cache->save($key, $list, 60 * 60); // 一个小时
            }
        }
        return $list;
    }

    /**
     * 根据用户ID分页获取对应的积分记录
     * 
     * @param string $user_id            
     * @param number $category            
     * @param number $page            
     * @param number $limit            
     * @param number $beginTime            
     * @param number $endTime            
     * @param array $otherConditions            
     * @return array
     */
    public function getUserPointsDetailList($user_id, $category, $page = 1, $limit = 10, $beginTime = 0, $endTime = 0, array $otherConditions = array())
    {
        $query = array(
            'user_id' => $user_id,
            'category' => $category
        );
        if (! empty($beginTime)) {
            $query['add_time']['$gte'] = getCurrentTime($beginTime);
        }
        if (! empty($endTime)) {
            $query['add_time']['$lte'] = getCurrentTime($endTime);
        }
        if (! empty($otherConditions)) {
            $query = array_merge($query, $otherConditions);
        }
        $list = $this->getPageList($page, $limit, $query, array(), array());
        return $list;
    }

    /**
     * 记录日志
     *
     * @param number $category            
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param string $unique_id            
     * @param boolean $is_consumed            
     * @param \MongoDate $add_time            
     * @param number $points            
     * @param string $stage            
     * @param string $desc            
     */
    public function log($category, $user_id, $user_name, $user_headimgurl, $unique_id, $is_consumed, \MongoDate $add_time, $points, $stage, $desc)
    {
        $data = array();
        $data['category'] = $category;
        $data['user_id'] = $user_id;
        $data['user_name'] = $user_name;
        $data['user_headimgurl'] = $user_headimgurl;
        $data['unique_id'] = $unique_id;
        $data['is_consumed'] = $is_consumed;
        $data['points'] = $points;
        $data['stage'] = $stage;
        $data['desc'] = urldecode($desc);
        $data['add_time'] = $add_time;
        return $this->insert($data);
    }
}