<?php

namespace App\Campaign\Controllers;

/**
 * cache事例
 *
 * @author Administrator
 *        
 */
class CacheController extends ControllerBase
{

    private $cache = null;

    public function initialize()
    {
        try {
            parent::initialize();
            $this->view->disable();
            $di = \Phalcon\DI::getDefault();
            $this->cache = $di->get("cache");
        } catch (\Exception $e) {
            $this->modelErrorLog->log($e);
        }
    }

    /**
     * 获取某个key的信息
     */
    public function getkeyAction()
    {
        // http://www.applicationmodule.com/campaign/cache/getkey?key=xxx
        try {
            // 获取所有的key
            $key = $this->get('key', '');
            var_dump($this->cache->get($key));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取某个key的信息
     */
    public function setkeyAction()
    {
        // http://www.applicationmodule.com/campaign/cache/setkey
        try {
            // 获取所有的key
            $key1 = 'arr1';
            $value1 = array('id' => 1, 'name' => 'test');
            $this->cache->save($key1, $value1);
            var_dump($this->cache->get($key1));

            $key2 = 'str1';
            $value2 = uniqid();
            $this->cache->save($key2, $value2);
            var_dump($this->cache->get($key2));


            $modelSite = new \App\Site\Models\Site();
            // // 热门搜索
            // $siteSettingInfo = $modelSite->getSettings(YUNGOU_SITE_ID);
            // var_dump($siteSettingInfo);

            $key3 = 'setttings';
            $info = $modelSite->getInfoById(YUNGOU_SITE_ID);
            if (!empty($info)) {
                $this->cache->save($key3, $info, 60 * 60); // 一个小时
            }
            var_dump($this->cache->get($key3));

            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}
