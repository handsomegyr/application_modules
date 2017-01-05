<?php
namespace App\Common\Models\Base;

class Base implements IBase
{

    protected $isDebug = false;

    protected $isPhql = false;

    protected $name = null;

    protected $dbName = 'db';

    protected $secondary = false;
    
    use BaseTrait;

    /**
     * model
     *
     * @var \App\Common\Models\Base\IBase
     */
    private $_model = null;

    protected function setModel(\App\Common\Models\Base\IBase $model)
    {
        if (empty($model)) {
            throw new \Exception('Model设置错误');
        }
        $this->_model = $model;
        $this->setPhql($this->isPhql);
        $this->setDebug($this->isDebug);
        $this->setDb($this->dbName);
        $this->setSource($this->name);
    }

    protected function getModel()
    {
        if (empty($this->_model)) {
            throw new \Exception('Model没有设置');
        }
        return $this->_model;
    }

    /**
     * 设置是否phql
     *
     * @param boolean $isPhql            
     */
    public function setPhql($isPhql)
    {
        return $this->getModel()->setPhql($isPhql);
    }

    public function getPhql()
    {
        return $this->getModel()->getPhql();
    }

    /**
     * 设置是否测试
     *
     * @param boolean $isDebug            
     */
    public function setDebug($isDebug)
    {
        return $this->getModel()->setDebug($isDebug);
    }

    public function getDebug()
    {
        return $this->getModel()->getDebug();
    }

    /**
     * 设置数据源表
     *
     * @param string $source            
     */
    public function setSource($source)
    {
        return $this->getModel()->setSource($source);
    }

    /**
     * 获取数据源表
     */
    public function getSource()
    {
        return $this->getModel()->getSource();
    }

    /**
     * 设置数据源库
     *
     * @param string $dbName            
     */
    public function setDb($dbName)
    {
        return $this->getModel()->setDb($dbName);
    }

    /**
     * 获取数据源库
     */
    public function getDb()
    {
        return $this->getModel()->getDb();
    }

    public function setSecondary($secondary)
    {
        return $this->getModel()->setSecondary($secondary);
    }

    public function getSecondary()
    {
        return $this->getModel()->getSecondary();
    }

    public function begin()
    {
        return $this->getModel()->begin();
    }

    public function commit()
    {
        return $this->getModel()->commit();
    }

    public function rollback()
    {
        return $this->getModel()->rollback();
    }

    public function getDI()
    {
        return $this->getModel()->getDI();
    }

    public function count(array $query)
    {
        return $this->getModel()->count($query);
    }

    public function findOne(array $query)
    {
        return $this->getModel()->findOne($query);
    }

    /**
     * 查询某个表中的数据
     *
     * @param array $query            
     * @param array $sort            
     * @param int $skip            
     * @param int $limit            
     * @param array $fields            
     */
    public function find(array $query, array $sort = null, $skip = 0, $limit = 10, array $fields = array())
    {
        return $this->getModel()->find($query, $sort, $skip, $limit, $fields);
    }

    public function findAll(array $query, array $sort = array(), array $fields = array())
    {
        return $this->getModel()->findAll($query, $sort, $fields);
    }

    public function distinct($field, array $query)
    {
        return $this->getModel()->distinct($field, $query);
    }

    /**
     * 查询某个表合计信息的数据
     *
     * @param array $query            
     * @param array $fields            
     * @param array $groups            
     */
    public function sum(array $query, array $fields = array(), array $groups = array())
    {
        return $this->getModel()->sum($query, $fields, $groups);
    }

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    public function insert(array $datas)
    {
        return $this->getModel()->insert($datas);
    }

    /**
     * 执行save操作
     *
     * @param array $datas            
     */
    public function save(array $datas)
    {
        return $this->getModel()->save($datas);
    }

    public function update(array $criteria, array $object, array $options = array())
    {
        return $this->getModel()->update($criteria, $object, $options);
    }

    /**
     * findAndModify
     */
    public function findAndModify(array $options)
    {
        return $this->getModel()->findAndModify($options);
    }

    public function remove(array $query)
    {
        return $this->getModel()->remove($query);
    }
}
