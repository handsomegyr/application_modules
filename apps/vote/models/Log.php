<?php
namespace Webcms\Vote\Models;

class Log extends \Webcms\Common\Models\Vote\Log
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
            '_id' => $sort
        );
        return $sort;
    }

    /**
     * 记录日志
     *
     * @param string $activity            
     * @param string $subject            
     * @param string $item            
     * @param string $identity            
     * @param number $vote_num            
     * @param array $memo            
     * @return array
     */
    public function log($activity, $subject, $item, $identity, $vote_num = 1, array $memo = array())
    {
        $data = array();
        $data['activity'] = $activity;
        $data['subject'] = $subject;
        $data['item'] = $item;
        $data['vote_time'] = getCurrentTime();
        $data['identity'] = $identity;
        $data['ip'] = getIp();
        $data['session_id'] = session_id();
        $data['vote_num'] = intval($vote_num);
        $data['memo'] = $memo;
        
        $info = $this->insert($data);
        return $info;
    }

    /**
     * 根据投票人获取他是否投票了$num次
     *
     * @param array $judgeBy            
     * @param number $num            
     * @param array $activitys            
     * @param array $subjects            
     * @param array $items            
     * @param MongoDate $startTime            
     * @param MongoDate $endTime            
     * @param array $cacheInfo            
     * @throws Exception
     * @return boolean
     */
    public function isVoted(array $judgeBy = array('identity'=>NULL,'ip'=>NULL,'session_id'=>NULL), $num = 1, array $activitys = NULL, array $subjects = NULL, array $items = NULL, $startTime = NULL, $endTime = NULL, array $cacheInfo = array('isCache'=>false,'cacheKey'=>null,'expire_time'=>null))
    {
        $query = array();
        
        if (! empty($judgeBy) && ! empty($judgeBy['identity'])) {
            $query['identity'] = array(
                '$in' => $judgeBy['identity']
            );
        }
        if (! empty($judgeBy) && ! empty($judgeBy['ip'])) {
            $query['ip'] = array(
                '$in' => $judgeBy['ip']
            );
        }
        if (! empty($judgeBy) && ! empty($judgeBy['session_id'])) {
            $query['session_id'] = array(
                '$in' => $judgeBy['session_id']
            );
        }
        if (! empty($activitys)) {
            $query['activity'] = array(
                '$in' => $activitys
            );
        }
        if (! empty($subjects)) {
            $query['subject'] = array(
                '$in' => $subjects
            );
        }
        if (! empty($items)) {
            $query['item'] = array(
                '$in' => $items
            );
        }
        
        if (! empty($startTime)) {
            $query['vote_time']['$gte'] = $startTime;
        }
        if (! empty($endTime)) {
            $query['vote_time']['$lte'] = $endTime;
        }
        
        if (intval($num) < 1) {
            throw new \Exception("参数num的值不正确");
        }
        
        $isVoted = false;
        if (! empty($cacheInfo) && ! empty($cacheInfo['isCache']) && ! empty($cacheInfo['cacheKey'])) {
            $cache = Zend_Registry::get('cache');
            $cacheKey = md5($cacheInfo['cacheKey'] . 'num' . $num . "_condition_" . md5(serialize($query)));
            $isVoted = $cache->load($cacheKey);
        }
        if (empty($isVoted)) {
            $count = $this->count($query);
            $isVoted = ($count > ($num - 1));
            if ($isVoted) {
                if (! empty($cacheInfo) && ! empty($cacheInfo['isCache']) && ! empty($cacheInfo['cacheKey'])) {
                    $cache->save($isVoted, $cacheKey, array(), empty($cacheInfo['expire_time']) ? null : $cacheInfo['expire_time']);
                }
            }
        }
        return $isVoted;
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
}