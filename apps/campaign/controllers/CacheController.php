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
}
