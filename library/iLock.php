<?php

/**
 * 添加锁定
 *
 * @author Young
 *        
 *         用法：
 *         $key = array(x=>x);
 *         $key = '123123123';
 *         $key mixed
 *        
 *         $objLock = new iLock($key);
 *         $objLock->lock(); //true表示被锁定 false表示未被锁定
 *        
 *         //设定过期时间30秒，意味着对于这个key锁定30秒，除非手动解锁，否则将持续锁定30秒
 *         $objLock = new iLock($key);
 *         $objLock->setExpire(30);
 *         $objLock->lock();
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

    private $_debug = false;

    public function __construct($key)
    {
        if (empty($key)) {
            throw new \Exception('new iLock($key) $key is not empty');
        }
        
        // 由于乐观锁只能用于单实例操作，所以从ilock自身去连接redis单实例数据库
        
        $redis = new Redis();
        $host = '127.0.0.1';
        $port = 6379;
        
        $di = \Phalcon\DI::getDefault();
        $config = $di->get('config');
        // $_SERVER['ICC_REDIS_MASTERS'] = "{$config['redis']['host']}:{$config['redis']['port']}";
        $host = $config['redis']['host'];
        $port = $config['redis']['port'];
        
        if (! $redis->connect($host, $port)) {
            throw new \Exception('redis connect refruse,please check $_SERVER[\'ICC_REDIS_SINGLE_MASTER\']');
        }
        
        // if (defined('REDIS_PASSWORD') && REDIS_PASSWORD != '') {
        // if (! $redis->auth(REDIS_PASSWORD)) {
        // throw \new Exception('redis connect auth error,please check password');
        // }
        // }
        $redis->select(2); // 指定库
        $this->_cache = $redis;
        $this->_key = $key; // cacheKey(__FILE__, $key);
        $this->_expire = ini_get('max_execution_time') > 0 ? ini_get('max_execution_time') : 30;
        
        //die(uniqid() . $host . $port);
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
        if ($sec > 86400)
            throw new Exception('$sec is too large,more than 1 day');
    }

    /**
     * 设置是否开启debug
     *
     * @param bool $boolean            
     * @return void
     */
    public function setDebug($boolean)
    {
        ini_set("opcache.enable", 0);
        ini_set("opcache.consistency_checks", 1);
        ini_set("opcache.force_restart_timeout", 3);
        $this->_debug = $boolean;
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
            throw new Exception('每个iLock实例只能lock一次，当一个请求中需要多次锁定时，请分别实例化iLock类');
        }
        
        try {
            $this->_isLocked = true;
            $this->_isInstance = true;
            
            if ($this->_cache->exists($this->_key)) {
                return $this->_isLocked;
            }
            
            $this->_cache->watch($this->_key);
            $val = $this->_cache->get($this->_key);
            if ($val !== false) {
                return $this->_isLocked;
            }
            
            $rst = $this->_cache->multi()
                ->incr($this->_key)
                ->expireAt($this->_key, time() + $this->_expire)
                ->exec();
            
            if ($rst !== false) {
                $this->_isLocked = false;
            }
            return $this->_isLocked;
        } catch (Exception $e) {
            var_dump($e);
            return true;
        }
    }

    /**
     * 返回ttl信息
     */
    public function ttl()
    {
        return $this->_cache->ttl($this->_key);
    }

    /**
     * 释放锁，在特定情况下，手动释放
     *
     * @return boolean
     */
    public function release()
    {
        if (! $this->_cache instanceof redis) {
            throw new Exception('$this->_cache is not a redis instance');
        }
        $this->_cache->delete($this->_key);
        return true;
    }

    /**
     * 自动释放锁，注意点：只有加锁的人自己才可以解锁
     */
    public function __destruct()
    {
        if (! $this->_isLocked && $this->_autoUnLock) {
            $this->_cache->delete($this->_key);
            $this->_cache->close();
        }
    }
}