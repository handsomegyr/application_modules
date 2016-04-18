<?php
namespace App\Common\Models\Mongodb;

class Base extends \iWebsitePluginMongo
{

    protected $isPhql = false;

    public function setPhql($isPhql)
    {
        $this->isPhql = $isPhql;
    }

    protected $isDebug = false;

    public function setDebug($isDebug)
    {
        $this->isDebug = $isDebug;
    }

    public function begin()
    {
        return;
    }

    public function commit()
    {
        return;
    }

    public function rollback()
    {
        return;
    }

    public function reorganize(array $data)
    {
        if (isset($data["_id"])) {
            $data["_id"] = myMongoId($data["_id"]);
        }
        return $data;
    }

    public function getCount(array $query)
    {
        $query = $this->getConditions($query);
        return $this->count($query);
    }

    public function getOne(array $query)
    {
        $query = $this->getConditions($query);
        $info = $this->findOne($query);
        if (empty($info)) {
            return $this->reorganize($info);
        } else {
            return array();
        }
    }

    public function get(array $query, array $sort = null, $skip = 0, $limit = 10, array $fields = array())
    {
        $query = $this->getConditions($query);
        $ret = $this->find($query, $sort, $skip, $limit, $fields);
        $list = array(
            'total' => $ret['total'],
            'datas' => array()
        );
        if (empty($ret['datas'])) {
            foreach ($ret['datas'] as $item) {
                $list['datas'][] = $this->reorganize($item);
            }
        }
        return $list;
    }

    public function getAll(array $query, array $sort = null, array $fields = array())
    {
        $query = $this->getConditions($query);
        $ret = $this->findAll($query, $sort, $fields);
        $list = array();
        if (empty($ret)) {
            foreach ($ret as $item) {
                $list[] = $this->reorganize($item);
            }
        }
        return $list;
    }

    protected function getMongoId4Query($_id)
    {
        return myMongoId($_id);
    }

    /**
     * findAndModify
     */
    public function insertAndModify(array $options)
    {
        return $this->findAndModify($options);
    }

    public function modify(array $criteria, array $object, array $options = array())
    {
        $query = $this->getConditions($query);
        return $this->update($criteria, $object, $options);
    }

    protected function getConditions(array $where)
    {
        if (isset($where['_id'])) {
            $where['_id'] = $this->getMongoId4Query($where['_id']);
        }
        return $where;
    }
}
