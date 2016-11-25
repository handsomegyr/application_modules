<?php
namespace App\Common\Models\Base\Mysql\Phalcon;

use App\Common\Models\Base\Mysql\Base;
use App\Common\Models\Base\Mysql\BaseTrait;

class Impl2 extends Base
{

    public function __construct()
    {}

    public function getDI()
    {
        $di = \Phalcon\DI::getDefault();
        return $di;
    }

    public function begin()
    {
        return $this->getDI()
            ->getDb()
            ->begin();
    }

    public function commit()
    {
        return $this->getDI()
            ->getDb()
            ->commit();
    }

    public function rollback()
    {
        return $this->getDI()
            ->getDb()
            ->rollback();
    }

    public function count(array $query)
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $phql = "SELECT COUNT(*) as num FROM {$className} WHERE {$conditions['conditions']}";
        $result = $this->executeQuery($phql, $conditions['bind']);
        $result = $result->fetch();
        if (! empty($result)) {
            $num = $result['num'];
        } else {
            $num = 0;
        }
        return $num;
    }

    public function findOne(array $query)
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $phql = "SELECT * FROM {$className} WHERE {$conditions['conditions']}";
        $result = $this->executeQuery($phql, $conditions['bind']);
        $info = $result->fetch();
        if (! empty($info)) {
            return $this->reorganize($info);
        } else {
            return array();
        }
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
        $total = $this->count($query);
        
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $order = $this->getSort($sort);
        $conditions = array_merge($conditions, $order, array(
            'limit' => $limit
        ), array(
            'offset' => $skip
        ));
        $orderBy = "";
        if (! empty($order['order'])) {
            $orderBy = "ORDER BY {$order['order']}";
        }
        $phql = "SELECT * FROM {$className} WHERE {$conditions['conditions']} {$orderBy} LIMIT {$conditions['limit']} OFFSET {$conditions['offset']} ";
        $result = $this->executeQuery($phql, $conditions['bind']);
        $ret = $result->fetchAll();
        $list = array();
        if (! empty($ret)) {
            foreach ($ret as $key => $item) {
                $list[$key] = $this->reorganize($item);
            }
        }
        
        return array(
            'total' => $total,
            'datas' => $list
        );
    }

    public function findAll(array $query, array $sort = null, array $fields = array())
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $order = $this->getSort($sort);
        $conditions = array_merge($conditions, $order);
        $orderBy = "";
        if (! empty($order['order'])) {
            $orderBy = "ORDER BY {$order['order']}";
        }
        $phql = "SELECT * FROM {$className} WHERE {$conditions['conditions']} {$orderBy} ";
        $result = $this->executeQuery($phql, $conditions['bind']);
        $ret = $result->fetchAll();
        $list = array();
        if (! empty($ret)) {
            foreach ($ret as $key => $item) {
                $list[$key] = $this->reorganize($item);
            }
        }
        return $list;
    }

    public function sum(array $query, array $fields = array(), array $groups = array())
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $columns = $this->getColumns($fields);
        $groups = $this->getGroups($groups);
        $params = array_merge($columns, $conditions, $groups);
        
        $groupBy = "";
        $groupFields = "";
        if (! empty($groups) && ! empty($groups['group'])) {
            $groupBy = "GROUP BY {$groups['group']}";
            $groupFields = "{$groups['group']},";
        }
        
        $phql = "select {$groupFields} SUM({$columns['column']}) AS sumatory FROM {$className} WHERE {$conditions['conditions']} {$groupBy}";
        $result = $this->executeQuery($phql, $conditions['bind']);
        $ret = $result->fetchAll();
        return $ret;
    }

    public function distinct($field, array $query)
    {
        if (empty($field)) {
            throw new \Exception('请指定字段$field', - 999);
        }
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $phql = "select DISTINCT {$field} FROM {$className} WHERE {$conditions['conditions']}";
        $result = $this->executeQuery($phql, $conditions['bind']);
        $ret = $result->fetchAll();
        $list = array();
        if (! empty($ret)) {
            foreach ($ret as $key => $item) {
                $data = $this->reorganize($item);
                $list[] = $data[$field];
            }
        }
        return $list;
    }

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    public function insert(array $datas)
    {
        $className = $this->getSource();
        $insertFieldValues = $this->getInsertContents($datas);
        $phql = "INSERT INTO {$className}({$insertFieldValues['fields']}) VALUES ({$insertFieldValues['bindFields']})";
        $data = $insertFieldValues['values'];
        $result = $this->executeQuery($phql, $data, 'execute');
        $_id = $insertFieldValues['_id'];
        return $this->findOne(array(
            '_id' => $_id
        ));
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

    /**
     * findAndModify
     */
    public function findAndModify(array $options)
    {
        $criteria = array();
        if (isset($options['query'])) {
            $criteria = $options['query'];
        }
        if (empty($criteria)) {
            throw new \Exception("query condition is empty in findAndModify", - 999);
        }
        $object = array();
        if (isset($options['update'])) {
            $object = $options['update'];
        }
        if (empty($object)) {
            throw new \Exception("update content is empty in findAndModify", - 999);
        }
        
        $new = false;
        if (isset($options['new'])) {
            $new = $options['new'];
        }
        $upsert = false;
        if (isset($options['upsert'])) {
            $upsert = $options['upsert'];
        }
        
        try {
            $this->begin();
            // 获取单条记录
            $info = $this->findOne($criteria);
            
            // 如果没有找到的话
            if (empty($info)) {
                // 如果需要插入的话
                if ($upsert) {
                    array_walk_recursive($criteria, function (&$value, $key) {
                        if (is_array($value)) {
                            unset($criteria[$key]);
                        }
                    });
                    $datas = array();
                    $datas = array_merge($criteria, $object['$set']);
                    $newInfo = $this->insert($datas);
                } else {
                    throw new \Exception("no record match query condition", - 999);
                }
            } else {
                // 进行更新操作
                $criteria['_id'] = $info['_id'];
                $this->update($criteria, $object);
                if ($new) {
                    // 获取单条记录
                    $newInfo = $this->findOne(array(
                        '_id' => $info['_id']
                    ));
                }
            }
            $this->commit();
            // 这里要确认一些mongodb的findAndModify操作的返回值
            
            $rst = array();
            $rst['ok'] = 1;
            if (empty($new)) {
                $rst['value'] = $info;
            } else {
                $rst['value'] = $newInfo;
            }
        } catch (\Exception $e) {
            $this->rollback();
            $rst = array();
            $rst['ok'] = 0;
            $rst['error'] = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        return $rst;
    }

    public function update(array $criteria, array $object, array $options = array())
    {
        if (empty($criteria)) {
            throw new \Exception("更新数据的时候请指定条件", - 999);
        }
        
        $className = $this->getSource();
        $conditions = $this->getConditions($criteria);
        $updateFieldValues = $this->getUpdateContents($object);
        $phql = "UPDATE {$className} SET {$updateFieldValues['fields']} WHERE {$conditions['conditions']} ";
        $data = array_merge($updateFieldValues['values'], $conditions['bind']);
        $result = $this->executeQuery($phql, $data, 'execute');
    }

    public function remove(array $query)
    {
        if (empty($query)) {
            throw new \Exception("删除数据的时候请指定条件", - 999);
        }
        
        $conditions = $this->getConditions($query);
        $className = $this->getSource();
        $phql = "DELETE FROM {$className} WHERE {$conditions['conditions']}";
        $result = $this->executeQuery($phql, $conditions['bind'], 'execute');
    }

    protected function executeQuery($phql, array $data, $method = 'query')
    {
        try {
            $phql = preg_replace('/:(.*?):/i', ':$1', $phql);
            $phql = preg_replace('/\[(.*?)\]/i', '`$1`', $phql);
            if ($this->getDebug()) {
                echo "<pre><br/>";
                echo $phql . "<br/>";
                var_dump($data);
                die('OK');
            }
            $di = $this->getDI();
            $db = $di['db'];
            // 只有在读取数据的时候，如果设置了secondary的话
            if ($method == 'query' && $this->getSecondary()) {
                $db = $di['secondarydb'];
            }
            $result = $db->$method($phql, $data);
            if ($method == 'query') {
                $result->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
                return $result;
            } else {
                return $db->affectedRows();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
