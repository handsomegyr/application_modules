<?php
namespace App\Bargain\Models;

class Log extends \App\Common\Models\Bargain\Log
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            '_id' => - 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getDefaultQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 根据用户ID和砍价物ID获取信息
     *
     * @param string $user_id            
     * @param string $bargain_id            
     * @return array
     */
    public function getInfoByUserIdAndBargainId($user_id, $bargain_id)
    {
        $query = array(
            'user_id' => $user_id,
            'bargain_id' => $bargain_id
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 记录数据
     *
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param string $client_ip            
     * @param string $bargain_id            
     * @param number $bargain_num            
     * @param number $bargain_amount            
     * @param boolean $is_system_bargain            
     * @param string $memo            
     */
    public function record($user_id, $user_name, $user_headimgurl, $client_ip, $bargain_id, $bargain_num, $bargain_amount, $is_system_bargain, array $memo = array())
    {
        if (empty($memo)) {
            $memo = array(
                'random' => mt_rand(0, 10000)
            );
        }
        return $this->insert(array(
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_headimgurl' => $user_headimgurl,
            'client_ip' => $client_ip,
            'bargain_id' => $bargain_id,
            'bargain_num' => intval($bargain_num),
            'bargain_amount' => intval($bargain_amount),
            'bargain_time' => new \MongoDate(),
            'is_system_bargain' => $is_system_bargain,
            'memo' => $memo
        ));
    }

    /**
     * 获取日志列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditon            
     * @param array $sort            
     * @param array $cacheInfo            
     * @return array
     */
    public function getList($page = 1, $limit = 10, array $otherConditon = array(), array $sort = array())
    {
        $list = $this->getPageList($page, $limit, $otherConditon, $sort, array());
        
        return array(
            'condition' => $condition,
            'list' => $list
        );
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
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $defaultQuery = $this->getDefaultQuery();
        $query = array_merge($query, $defaultQuery);
        
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $query, $sort, $fields, $page, $limit);
        
        $cache = $this->getDI()->get("cache");
        
        $list = $cache->get($key);
        if (empty($list)) {
            $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, $fields);
            $cache->save($key, $list, 60 * 60); // 一个小时
        }
        return $list;
    }
}
