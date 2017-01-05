<?php
namespace App\Vote\Models;

class Subject extends \App\Common\Models\Vote\Subject
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