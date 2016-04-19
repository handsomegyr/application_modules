<?php
namespace App\Common\Models\Base;

class Base
{
    use BaseTrait;

    public function setPhql($isPhql)
    {
        $this->getModel()->setPhql($isPhql);
    }

    /**
     * 设置是否测试
     *
     * @param boolean $isDebug            
     */
    public function setDebug($isDebug)
    {
        $this->getModel()->setDebug($isDebug);
    }

    private $_model = null;

    protected function setModel($model)
    {
        if (empty($model)) {
            throw new \Exception('Model设置错误');
        }
        $this->_model = $model;
    }

    protected function getModel()
    {
        if (empty($this->_model)) {
            throw new \Exception('Model没有设置');
        }
        return $this->_model;
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

    public function count($query)
    {
        return $this->getModel()->getCount($query);
    }

    public function findOne(array $query)
    {
        return $this->getModel()->getOne($query);
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
        return $this->getModel()->get($query, $sort, $skip, $limit, $fields);
    }

    public function findAll(array $query, array $sort = array(), array $fields = array())
    {
        return $this->getModel()->getAll($query, $sort, $fields);
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
        return $this->getModel()->getSum($query, $fields, $groups);
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
        return $this->insert($datas);
    }

    public function update(array $criteria, array $object, array $options = array())
    {
        return $this->getModel()->modify($criteria, $object, $options);
    }

    /**
     * findAndModify
     */
    public function findAndModify(array $options)
    {
        return $this->getModel()->insertAndModify($options);
    }

    public function remove(array $query)
    {
        return $this->getModel()->remove($query);
    }
}
