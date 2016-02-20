<?php

abstract class iWebsitePluginMongo
{

    protected $name = null;

    protected $dbName = 'default';

    protected $secondary = false;

    private $_db;

    private $_di;

    /**
     * Sets the dependency injection container
     *
     * @param mixed $dependencyInjector            
     */
    public function setDI(\Phalcon\DiInterface $dependencyInjector)
    {
        $this->_di = $dependencyInjector;
    }

    /**
     * Returns the dependency injection container
     *
     * @return \Phalcon\DiInterface
     */
    public function getDI()
    {
        return $this->_di;
    }

    /**
     * 建立默认的数据库连接
     */
    public function __construct()
    {
        try {
            // global $di;
            $di = \Phalcon\DI::getDefault();
            $this->setDI($di);
            $db = $di->get("databases");
            if ($db) {
                if (count($db) == 0)
                    exit('Please set db config');
                
                if (isset($db[$this->dbName])) {
                    $this->_db = clone $db[$this->dbName];
                } else {
                    $db = array_values($db);
                    $this->_db = clone $db[0];
                }
            } else {
                exit('isRegistered(\'db\') is undefined');
            }
            
            $this->_db->setCollection($this->name, $this->secondary);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     * 获取当前的数据库连接
     *
     * @return mixed
     */
    public function getDB()
    {
        return $this->_db;
    }

    public function getSchema()
    {
        return $this->_db->getSchema();
    }

    public function insertRef(&$datas)
    {
        return $this->_db->insert($datas);
    }

    public function save(&$datas)
    {
        return $this->_db->save($datas);
    }

    /**
     * 是否开启调试模式
     *
     * @param bool $bool            
     */
    public function setDebug($bool)
    {
        $bool = is_bool($bool) ? $bool : false;
        $this->_db->setDebug($bool);
    }

    /**
     * 过载处理
     *
     * @param string $funcname            
     * @param array $arguments            
     * @return mixed
     */
    public function __call($funcname, $arguments)
    {
        if (! is_array($arguments)) {
            $arguments = array();
        }
        return call_user_func_array(array(
            $this->_db,
            $funcname
        ), $arguments);
    }
}