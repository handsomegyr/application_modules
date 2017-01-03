<?php
namespace App\Activity\Models;

class Activity extends \App\Common\Models\Activity\Activity
{

    /**
     * 根据ID获取信息
     *
     * @param string $id            
     * @return array
     */
    public function getInfoById($id)
    {
        $cache = $this->getDI()->get("cache");
        $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__, $id);
        $info = $cache->get($cacheKey);
        if (empty($info)) {
            $query = array(
                '_id' => $id,
                'is_actived' => true
            );
            $info = $this->findOne($query);
            $cache->save($cacheKey, $info, 5 * 60);
        }
        return $info;
    }

    public function getActivityInfo2($activity_id, $now, $is_return_info = false)
    {
        $ret = array();
        // 获取活动信息
        $activityInfo = $this->getInfoById($activity_id);
        if (empty($activityInfo)) {
            $is_activity_started = false;
            $is_activity_over = false;
            $is_actvity_paused = false;
        } else {
            $is_activity_started = $this->isActivityStarted($activityInfo, $now);
            $is_activity_over = $this->isActivityOver($activityInfo, $now);
            $is_actvity_paused = empty($activityInfo['is_paused']) ? false : true;
        }
        // 活动是否开始了
        $ret['is_activity_started'] = $is_activity_started;
        // 活动是否暂停
        $ret['is_actvity_paused'] = $is_actvity_paused;
        // 活动是否结束了
        $ret['is_activity_over'] = $is_activity_over;
        
        if (! empty($is_return_info)) {
            // 活动信息
            $ret['activityInfo'] = $activityInfo;
        }
        return $ret;
    }

    /**
     * 检查活动是否开始了
     *
     * @param array $activityInfo            
     */
    protected function isActivityStarted($activityInfo, $now)
    {
        if (empty($activityInfo)) {
            return false;
        } else {
            if ($activityInfo['start_time']->sec <= $now) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 检查活动是否发完了
     *
     * @param array $activityInfo            
     */
    protected function isActivityOver($activityInfo, $now)
    {
        if (empty($activityInfo)) {
            return false;
        } else {
            if ($activityInfo['end_time']->sec < $now) {
                return true;
            } else {
                return false;
            }
        }
    }

    private $_activityInfo = null;

    /**
     * 获取活动信息
     *
     * @param string $activity_id            
     */
    public function getActivityInfo($activity_id)
    {
        if ($this->_activityInfo == null) {
            $this->_activityInfo = $this->findOne(array(
                '_id' => $activity_id
            ));
        }
        return $this->_activityInfo;
    }

    /**
     * 检测活动是否开始
     *
     * @param string $activity_id            
     */
    public function checkActivityActive($activity_id)
    {
        $activityInfo = $this->getActivityInfo($activity_id);
        if (! empty($activityInfo['is_actived'])) {
            $now = time();
            if (! empty($activityInfo['start_time']) && ! empty($activityInfo['end_time'])) {
                if ($activityInfo['start_time']->sec <= $now && $now <= $activityInfo['end_time']->sec) {
                    return true;
                } else {
                    return false;
                }
            } else {
                throw new \Exception("请设定完整的活动起止时间");
            }
        } else {
            return false;
        }
    }
}