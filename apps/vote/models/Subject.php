<?php
namespace Webcms\Vote\Models;

class Subject extends \Webcms\Common\Models\Vote\Subject
{

    /**
     * 默认排序
     *
     * @param number $sort            
     * @return array
     */
    public function getDefaultSort($sort = -1)
    {
        $sort = array(
            'show_order' => - 1,
            '_id' => $sort
        );
        return $sort;
    }

    /**
     * 根据投票数排序
     *
     * @param number $sort            
     * @return array
     */
    public function getRankSort($sort = -1)
    {
        $sort = array(
            'vote_count' => $sort
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $now = getCurrentTime();
        $query = array(
            "is_closed" => false,
            'start_time' => array(
                '$lte' => $now
            ),
            'end_time' => array(
                '$gte' => $now
            )
        ); // 显示
        return $query;
    }

    /**
     * 根据活动ID获取场主题列表
     *
     * @param string $activityId            
     * @return array
     */
    public function getListByActivityId($activityId)
    {
        $query = $this->getQuery();
        $query['activity_id'] = $activityId;
        $sort = $this->getDefaultSort();
        $ret = $this->findAll($query, $sort);
        return $ret;
    }

    /**
     * 增加投票数
     *
     * @param string $id            
     * @param number $vote_count            
     */
    public function incVoteCount($id, $vote_count = 1)
    {
        $query = array(
            '_id' => ($id)
        );
        $this->update($query, array(
            '$inc' => array(
                'vote_count' => $vote_count
            )
        ));
    }

    /**
     * 获取列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditon            
     * @param array $sort            
     * @param array $cacheInfo            
     * @return array
     */
    public function getList($page = 1, $limit = 10, array $otherConditon = array(), array $sort = null, array $cacheInfo = array('isCache'=>false,'cacheKey'=>null,'expire_time'=>null))
    {
        if (empty($sort)) {
            $sort = $this->getDefaultSort(- 1);
        }
        $condition = array();
        if (! empty($otherConditon)) {
            $condition = array_merge($condition, $otherConditon);
        }
        $list = array();
        
        if (! empty($cacheInfo) && ! empty($cacheInfo['isCache']) && ! empty($cacheInfo['cacheKey'])) {
            $cache = Zend_Registry::get('cache');
            $cacheKey = md5($cacheInfo['cacheKey'] . 'page' . $page . 'limit' . $limit . "_condition_" . md5(serialize($condition)) . "_sort_" . md5(serialize($sort)));
            $list = $cache->load($cacheKey);
        }
        
        if (empty($list)) {
            $list = $this->find($condition, $sort, ($page - 1) * $limit, $limit);
        }
        
        if (! empty($cacheInfo) && ! empty($cacheInfo['isCache']) && ! empty($cacheInfo['cacheKey'])) {
            $cache->save($list, $cacheKey, array(), empty($cacheInfo['expire_time']) ? null : $cacheInfo['expire_time']);
        }
        
        return array(
            'condition' => $condition,
            'list' => $list
        );
    }

    /**
     * 我的排名
     *
     * @param array $myInfo            
     * @param array $otherConditions            
     * @return number
     */
    public function getRank($myInfo, array $otherConditions = array())
    {
        $query = $this->getQuery();
        $query['_id'] = array(
            '$ne' => $myInfo['_id']
        );
        $query['vote_count'] = array(
            '$gt' => $myInfo['vote_count']
        ); // 按投票次数
        if (! empty($otherConditions)) {
            foreach ($otherConditions as $key => $value) {
                $query[$key] = $value;
            }
        }
        $num = $this->count($query);
        return $num + 1;
    }
}