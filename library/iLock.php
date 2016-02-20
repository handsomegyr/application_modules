<?php

/**
 * 添加锁定
 * 
 * @author Young
 * 
 * 用法：
 * $key = array(x=>x);
 * $key = '123123123';
 * $key mixed
 * 
 * $objLock = new iLock($key);
 * $objLock->lock(); //true表示被锁定 false表示未被锁定
 * 
 * //设定过期时间30秒，意味着对于这个key锁定30秒，除非手动解锁，否则将持续锁定30秒
 * $objLock = new iLock($key);
 * $objLock->setExpire(30);
 * $objLock->lock(); 
 * 
 * 
 */
class iLock
{

    private $_cache = null;

    private $_key = '';

    private $_expire = 30;

    private $_isInstance = false;

    private $_isLocked = false;

    private $_autoUnLock = true;

    public function __construct($key)
    {
        if (empty($key)) {
            throw new \Exception('new iLock($key) $key is not empty');
        }
        
        if (! class_exists('Memcached')) {
            throw new \Exception('请安装Memcached扩展');
        }
        
        $di = \Phalcon\DI::getDefault();
        if (! $di->has('memcached')) { // Zend_Registry::isRegistered('memcached')
            throw new \Exception('memcached未正确初始化');
        }
        
        $this->_cache = $di->get('memcached'); // Zend_Registry::get('memcached');
        
        $this->_key = cacheKey(__FILE__, $key);
        $this->_expire = ini_get('max_execution_time');
    }

    /**
     * 设定缓存的强制锁定时间,必须锁定足够时间才能解锁
     *
     * @param number $sec            
     */
    public function setExpire($sec = 5)
    {
        $this->_autoUnLock = false;
        $this->_expire = $sec;
    }

    /**
     * 采用CAS乐观锁
     *
     * @throws Exception
     * @return boolean
     */
    public function lock()
    {
        if ($this->_isInstance) {
            throw new \Exception('每个iLock实例只能lock一次，当一个请求中需要多次锁定时，请分别实例化iLock类');
        }
        
        try {
            $this->_isInstance = true;
            do {
                $value = $this->_cache->get($this->_key, null, $casToken);
                if ($this->_cache->getResultCode() == Memcached::RES_NOTFOUND) {
                    $expireTime = $this->_expire;
                    // $expireTime = $this->_expire;
                    $this->_cache->add($this->_key, $expireTime, $expireTime);
                    $this->_isLocked = false;
                } else {
                    $this->_cache->cas($casToken, $this->_key, $value, $value);
                    $this->_isLocked = true;
                }
            } while ($this->_cache->getResultCode() != Memcached::RES_SUCCESS);
            return $this->_isLocked;
        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * 释放锁，在特定情况下，手动释放
     *
     * @return boolean
     */
    public function release()
    {
        return $this->_cache->delete($this->_key);
    }

    /**
     * 自动释放锁，注意点：只有加锁的人自己才可以解锁
     */
    public function __destruct()
    {
        if (! $this->_isLocked && $this->_autoUnLock)
            $this->_cache->delete($this->_key);
    }

    public function getKey()
    {
        return $this->_key;
    }
}