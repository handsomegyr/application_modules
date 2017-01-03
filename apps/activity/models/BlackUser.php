<?php
namespace App\Activity\Models;

class BlackUser extends \App\Common\Models\Activity\BlackUser
{

    public function getInfoByUser($user_id, $activity_id)
    {
        $cache = $this->getDI()->get("cache");
        $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__, $user_id, $activity_id, 2);
        $info = $cache->get($cacheKey);
        if (empty($info)) {
            $query = array(
                'user_id' => $user_id,
                'activity_id' => $activity_id
            );
            $info = $this->findOne($query);
            if (! empty($info)) {
                $cache->save($cacheKey, $info, 5 * 60);
            }
        }
        return $info;
    }
}