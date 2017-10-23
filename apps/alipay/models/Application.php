<?php
namespace App\Alipay\Models;

class Application extends \App\Common\Models\Alipay\Application
{

    private $_params = array();

    private $_expire = 30;

    /**
     * 获取应用的信息
     *
     * @return array
     */
    public function getApplicationInfoByAppId($appid)
    {
        $cacheKey = $this->getCacheKey($appid);
        $cache = $this->getDI()->get('cache');
        $application = $cache->get($cacheKey);
        if (empty($application)) {
            $application = $this->findOne(array(
                'app_id' => $appid
            ));
            if (! empty($application)) {
                $expire_time = $this->getExpireTime($application);
                $cache->save($cacheKey, $application, $expire_time);
            }
        }
        return $application;
    }

    public function getSignKey($openid, $secretKey, $timestamp = 0)
    {
        return sha1($openid . "|" . $secretKey . "|" . $timestamp);
    }

    private function getExpireTime(array &$info)
    {
        $expire_time = 60;
        return $expire_time;
    }

    private function getCacheKey($appid)
    {
        $cacheKey = cacheKey(__FILE__, __CLASS__, $appid);
        return $cacheKey;
    }
}