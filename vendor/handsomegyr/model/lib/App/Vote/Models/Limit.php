<?php
namespace App\Vote\Models;

class Limit extends \App\Common\Models\Vote\Limit
{

    private $_log = null;

    /**
     * 获取投票日志实体对象
     *
     * @return Log
     */
    public function getLogModel()
    {
        if (empty($this->_log)) {
            throw new \Exception('没有设定中奖对象');
        }
        return $this->_log;
    }

    public function setLogModel(Log $log)
    {
        $this->_log = $log;
    }

    private $limits = array();

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $now = getCurrentTime();
        $query = array(
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
     * 获取限制列表
     *
     * @param array $activitys            
     * @param array $subjects            
     * @param array $items            
     *
     * @return array
     */
    public function getLimitList($activitys = array(), $subjects = array(), $items = array())
    {
        $query = $this->getQuery();
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
            $query['items'] = array(
                '$in' => $items
            );
        }
        
        $sort = array(
            'category' => - 1
        );
        $list = $this->findAll($query, $sort);
        return $list;
    }

    /**
     * 限制检查
     *
     * @param string $activityId            
     * @param string $subjectId            
     * @param string $itemId            
     * @param string $identity            
     * @param array $activitys            
     * @param array $subjects            
     * @param array $items            
     * @param array $cacheInfo            
     * @return boolean
     */
    public function checkLimit($activityId, $subjectId, $itemId, $identity, $num = 1, array $activitys = array(), array $subjects = array(), array $items = array(), array $cacheInfo = array('isCache'=>false,'cacheKey'=>null,'expire_time'=>null))
    {
        if (empty($this->limits)) {
            // 获取限制列表
            $this->limits = $this->getLimitList($activitys, $subjects, $items);
        }
        // 检查
        if (! empty($this->limits)) {
            $modelLog = $this->getLogModel();
            foreach ($this->limits as $limit) {
                $activity = empty($limit['activity']) ? NULL : $limit['activity'];
                $subject = empty($limit['subject']) ? NULL : $limit['subject'];
                $item = empty($limit['item']) ? NULL : $limit['item'];
                
                if (! empty($item) && $item != $itemId) { // 如果设置到选项这一层次,并且用户所投的选项和限制选项不同的话,跳过检查
                    continue;
                }
                
                if (! empty($subject) && $subject != $subjectId) { // 如果设置到主题这一层次,并且用户所投的主题和限制主题不同的话,跳过检查
                    continue;
                }
                
                if (! empty($activity) && $activity != $activityId) { // 如果设置到活动这一层次,并且用户所投的活动和限制活动不同的话,跳过检查
                    continue;
                }
                
                $activity = empty($activity) ? NULL : array(
                    $activity
                );
                $subject = empty($subject) ? NULL : array(
                    $subject
                );
                $item = empty($item) ? NULL : array(
                    $item
                );
                
                switch (intval($limit['category'])) {
                    case 3:
                        $isVoted = $modelLog->isVoted(array(
                            'identity' => $identity
                        ), $num, $activity, $subject, $item, null, null, $cacheInfo); // 根据身份
                        break;
                    case 2:
                        $isVoted = $modelLog->isVoted(array(
                            'ip' => getIp()
                        ), $num, $activity, $subject, $item, null, null, $cacheInfo); // 根据IP
                        break;
                    case 1:
                        $isVoted = $modelLog->isVoted(array(
                            'session_id' => session_id()
                        ), $num, $activity, $subject, $item, null, null, $cacheInfo); // 根据会话ID
                        break;
                    default:
                        ;
                        break;
                }
                if ($isVoted) {
                    return false;
                }
            }
        }
        
        return true;
    }
}