<?php
namespace App\Common\Models\Base\Mysql;

use App\Common\Models\Base\Mysql\Phalcon\Impl2;

class Base
{
    use BaseTrait;

    protected $impl = NULL;

    public function __construct()
    {
        $this->impl = new Impl2($this);
    }

    protected $isPhql = false;

    public function setPhql($isPhql)
    {
        $this->isPhql = $isPhql;
    }

    protected $isDebug = false;

    public function setDebug($isDebug)
    {
        $this->impl->setDebug($isDebug);
    }

    public function getSource()
    {
        new \Exception('getSource is not implement');
    }

    public function reorganize(array $data)
    {
        if (isset($data['_id'])) {
            $data['_id'] = $this->getMongoId4Query($data['_id']);
        }
        if (isset($data['__CREATE_TIME__'])) {
            $data['__CREATE_TIME__'] = $this->changeToMongoDate($data['__CREATE_TIME__']);
        }
        if (isset($data['__MODIFY_TIME__'])) {
            $data['__MODIFY_TIME__'] = $this->changeToMongoDate($data['__MODIFY_TIME__']);
        }
        if (isset($data['__REMOVED__'])) {
            $data['__REMOVED__'] = $this->changeToBoolean($data['__REMOVED__']);
        }
        if (isset($data['memo'])) {
            $data['memo'] = $this->changeToArray($data['memo']);
        }
        return $data;
    }

    public function getDI()
    {
        $di = \Phalcon\DI::getDefault();
        return $di;
    }

    public function begin()
    {
        return $this->impl->begin();
    }

    public function commit()
    {
        return $this->impl->commit();
    }

    public function rollback()
    {
        return $this->impl->rollback();
    }

    public function count(array $query)
    {
        return $this->impl->count($query);
    }

    public function findOne(array $query)
    {
        return $this->impl->findOne($query);
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
        return $this->impl->find($query, $sort, $skip, $limit, $fields);
    }

    public function findAll(array $query, array $sort = null, array $fields = array())
    {
        return $this->impl->findAll($query, $sort, $fields);
    }

    public function sum(array $query, array $fields = array(), array $groups = array())
    {
        return $this->impl->sum($query, $fields, $groups);
    }

    public function distinct($field, array $query)
    {
        return $this->impl->distinct($field, $query);
    }

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    public function insert(array $datas)
    {
        return $this->impl->insert($datas);
    }

    /**
     * findAndModify
     */
    public function findAndModify(array $options)
    {
        return $this->impl->findAndModify($options);
    }

    public function modify(array $criteria, array $object, array $options = array())
    {
        return $this->impl->modify($criteria, $object, $options);
    }

    public function remove(array $query)
    {
        return $this->impl->remove($query);
    }
}
