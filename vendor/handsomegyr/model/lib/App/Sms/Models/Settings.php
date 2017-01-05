<?php
namespace App\Sms\Models;

class Settings extends \App\Common\Models\Sms\Settings
{

    /**
     * 获取短信设置
     *
     * @return array
     */
    public function getSettings()
    {
        $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get("cache");
        $settings = $cache->get($key);
        if (empty($settings)) {
            $settings = array();
            $list = $this->find(array(), array(
                '_id' => - 1
            ), 0, 1);
            
            if (! empty($list['datas'])) {
                $settings = $list['datas'][0];
                $cache->save($key, $settings, 60 * 60 * 24); // 24小时
            }
        }
        return $settings;
    }

    public function sendSms($mobile, $subject, $body)
    {
        $smsSettings = $this->getSettings();
        $objClient = new \Sms\Yunpian\Sms($smsSettings['apikey']);
        $ret = $objClient->send($mobile, $body);
        return $ret;
    }
}