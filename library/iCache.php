<?php

class iCache
{

    private $cacheObj = null;

    public function __construct($cacheObj)
    {
        $this->cacheObj = $cacheObj;
    }

    public function get($key, $defaultVal = '')
    {
        return $this->cacheObj->get($key);
    }

    public function save($key, $val, $expireSec = 0)
    {
        if (method_exists($this->cacheObj, 'save')) {
            return $this->cacheObj->save($key, $val, $expireSec);
        } else {
            return $this->cacheObj->set($key, $val, $expireSec);
        }
    }

    public function delete($key)
    {
        return $this->cacheObj->delete($key);
    }

    public function queryKeys()
    {
        return $this->getKeys();
    }

    public function getKeys()
    {
        return $this->cacheObj->getKeys();
    }

    public function clear()
    {
        return $this->flush();
    }

    public function flush()
    {
        return $this->cacheObj->flush();
    }
}
