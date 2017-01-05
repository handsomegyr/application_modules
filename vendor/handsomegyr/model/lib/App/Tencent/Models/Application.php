<?php
namespace App\Tencent\Models;

class Application extends \App\Common\Models\Tencent\Application
{

    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getInfoByAppId($appid)
    {
        $cacheKey = cacheKey(__FILE__, __CLASS__, $appid);
        $cache = $this->getDI()->get('cache');
        $application = $cache->get($cacheKey);
        if (empty($application)) {
            $application = $this->findOne(array(
                'akey' => $appid
            ));
            if (! empty($application)) {
                $cache->save($cacheKey, $application, 300);
            }
        }
        return $application;
    }

    public function getSignKey($user_id, $secretKey, $timestamp = 0)
    {
        return sha1($user_id . "|" . $secretKey . "|" . $timestamp);
    }
}
