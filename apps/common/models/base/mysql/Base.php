<?php
namespace App\Common\Models\Mysql;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Base extends Model
{

    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

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

    public function getCount(array $query)
    {
        $conditions = $this->getConditions($query);
        // print_r($conditions);
        $num = self::count($conditions);
        // die('num:' . $num);
        return $num;
    }

    public function getOne(array $query)
    {
        $conditions = $this->getConditions($query);
        // print_r($conditions);
        $info = self::findFirst($conditions);
        // die('info:' . var_dump($info));
        if (! empty($info)) {
            return $this->reorganize($info->toArray());
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
    public function get(array $query, array $sort = null, $skip = 0, $limit = 10, array $fields = array())
    {
        $total = $this->getCount($query);
        
        $conditions = $this->getConditions($query);
        $order = $this->getSort($sort);
        $conditions = array_merge($conditions, $order, array(
            'limit' => $limit
        ), array(
            'offset' => $skip
        ));
        if ($this->isDebug) {
            echo "<pre><br/>";
            var_dump($conditions);
            die('OK');
        }
        $ret = self::find($conditions);
        $list = array();
        if (! empty($ret)) {
            foreach ($ret as $key => $item) {
                $list[$key] = $this->reorganize($item->toArray());
            }
        }
        return array(
            'total' => $total,
            'datas' => $list
        );
    }

    public function getAll(array $query, array $sort = null, array $fields = array())
    {
        $conditions = $this->getConditions($query);
        $order = $this->getSort($sort);
        $conditions = array_merge($conditions, $order);
        if ($this->isDebug) {
            echo "<pre><br/>";
            var_dump($conditions);
            die('OK');
        }
        $ret = self::find($conditions);
        $list = array();
        if (! empty($ret)) {
            foreach ($ret as $key => $item) {
                $list[$key] = $this->reorganize($item->toArray());
            }
        }
        return $list;
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
        if ($this->isPhql) {
            $className = get_class($this);
        } else {
            $className = $this->getSource();
        }
        $phql = "select DISTINCT {$field} FROM {$className} WHERE {$conditions['conditions']}";
        $ret = $this->executeQuery($phql, $conditions['bind'], true);
        $list = array();
        if (! empty($ret)) {
            foreach ($ret as $key => $item) {
                $data = $this->reorganize($item->toArray());
                $list[] = $data[$field];
            }
        }
        return $list;
    }

    public function getSum(array $query, array $fields = array(), array $groups = array())
    {
        $conditions = $this->getConditions($query);
        $columns = $this->getColumns($fields);
        $groups = $this->getGroups($groups);
        $params = array_merge($columns, $conditions, $groups);
        $ret = self::sum($params);
        return $ret;
    }

    protected function getMongoId4Query($_id)
    {
        if (is_array($_id)) {
            $list = array();
            foreach ($_id as $item) {
                if ($item instanceof \MongoId) {
                    $list[] = $item->__toString();
                } else {
                    $list[] = $item;
                }
            }
            return $list;
        } else {
            if ($_id instanceof \MongoId) {
                return $_id->__toString();
            } else {
                return $_id;
            }
        }
    }

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    public function insert(array $datas)
    {
        if ($this->isPhql) {
            $className = get_class($this);
        } else {
            $className = $this->getSource();
        }
        $insertFieldValues = $this->getInsertContents($datas);
        $phql = "INSERT INTO {$className}({$insertFieldValues['fields']}) VALUES ({$insertFieldValues['bindFields']})";
        $data = $insertFieldValues['values'];
        $result = $this->executeQuery($phql, $data);
        if ($this->isPhql) {
            return $this->reorganize($result->getModel()
                ->toArray());
        } else {
            $_id = $insertFieldValues['_id'];
            return $this->getOne(array(
                '_id' => $_id
            ));
        }
    }

    /**
     * findAndModify
     */
    public function insertAndModify(array $options)
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
            $info = $this->getOne($criteria);
            
            // 如果没有找到的话
            if (empty($info)) {
                // 如果需要插入的话
                if ($upsert) {
                    array_walk_recursive($criteria, function (&$value, $key)
                    {
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
                $this->modify($criteria, $object);
                if ($new) {
                    // 获取单条记录
                    $newInfo = $this->getOne(array(
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

    public function modify(array $criteria, array $object, array $options = array())
    {
        if (empty($criteria)) {
            throw new \Exception("更新数据的时候请指定条件", - 999);
        }
        if ($this->isPhql) {
            $className = get_class($this);
        } else {
            $className = $this->getSource();
        }
        $conditions = $this->getConditions($criteria);
        $updateFieldValues = $this->getUpdateContents($object);
        $phql = "UPDATE {$className} SET {$updateFieldValues['fields']} WHERE {$conditions['conditions']} ";
        $data = array_merge($updateFieldValues['values'], $conditions['bind']);
        $result = $this->executeQuery($phql, $data);
    }

    public function remove(array $query)
    {
        if (empty($query)) {
            throw new \Exception("删除数据的时候请指定条件", - 999);
        }
        
        $conditions = $this->getConditions($query);
        if ($this->isPhql) {
            $className = get_class($this);
        } else {
            $className = $this->getSource();
        }
        $phql = "DELETE FROM {$className} WHERE {$conditions['conditions']}";
        $result = $this->executeQuery($phql, $conditions['bind']);
    }

    public function getConditions(array $where, $condition_op = "AND")
    {
        $unique = uniqid();
        $conditions = array();
        $bind = array();
        $forUpdate = array();
        // 如果__FOR_UPDATE__ 存在的话
        if (array_key_exists("__FOR_UPDATE__", $where)) {
            $forUpdate["for_update"] = $where["__FOR_UPDATE__"];
            unset($where["__FOR_UPDATE__"]);
        }
        
        // 如果__QUERY_OR__ 存在的话
        if (array_key_exists("__QUERY_OR__", $where)) {
            $condition_op = "OR";
            $orConditions = $where["__QUERY_OR__"];
            unset($where["__QUERY_OR__"]);
            $bind = array();
            $conditions = array();
            foreach ($orConditions as $condition) {
                $query = $this->getConditions($condition);
                $bind = array_merge($bind, $query['bind']);
                $conditions[] = $query['conditions'];
            }
            $where = array();
        }
        
        foreach ($where as $key => $item) {
            if ($key == '__OR__') {
                // 解决OR查询
                $orConditions = $this->getConditions($item, "OR");
                if (! empty($orConditions)) {
                    $conditions[] = $orConditions['conditions'];
                    $bind = array_merge($bind, $orConditions['bind']);
                }
            } else {
                $fieldKey = "[{$key}]";
                $bindKey = "__{$key}{$unique}__";
                if (is_array($item)) {
                    foreach ($item as $op => $value) {
                        $value = $this->changeValue4Conditions($value, $key);
                        if ($op == '$in') {
                            if (! empty($value)) {
                                // $conditions[] = "{$fieldKey} IN ({{$bindKey}:array})";
                                // $bind[$bindKey] = array_values($value);
                                $bindKey4In = array();
                                foreach (array_values($value) as $idex => $item) {
                                    $bindKey4In[] = $bindKey . '_' . $idex;
                                    $bind[$bindKey . '_' . $idex] = $item;
                                }
                                $bindKey4In = implode(':,:', $bindKey4In);
                                $conditions[] = "{$fieldKey} IN (:{$bindKey4In}:)";
                            } else {
                                $conditions[] = "{$fieldKey}=:{$bindKey}:";
                                $bind[$bindKey] = "";
                            }
                        }
                        if ($op == '$nin') {
                            if (! empty($value)) {
                                // $conditions[] = "{$fieldKey} NOT IN ({{$bindKey}:array})";
                                // $bind[$bindKey] = array_values($value);
                                $bindKey4In = array();
                                foreach (array_values($value) as $idex => $item) {
                                    $bindKey4In[] = $bindKey . '_' . $idex;
                                    $bind[$bindKey . '_' . $idex] = $item;
                                }
                                $bindKey4In = implode(':,:', $bindKey4In);
                                $conditions[] = "{$fieldKey} NOT IN (:{$bindKey4In}:)";
                            } else {
                                $conditions[] = "{$fieldKey}!=:{$bindKey}:";
                                $bind[$bindKey] = "";
                            }
                        }
                        
                        if ($op == '$ne') {
                            $conditions[] = "{$fieldKey}!=:{$bindKey}:";
                            $bind[$bindKey] = $value;
                        }
                        if ($op == '$lt') {
                            $conditions[] = "{$fieldKey}<:lt_{$bindKey}:";
                            $bind['lt_' . $bindKey] = $value;
                        }
                        if ($op == '$lte') {
                            $conditions[] = "{$fieldKey}<=:lte_{$bindKey}:";
                            $bind['lte_' . $bindKey] = $value;
                        }
                        
                        if ($op == '$gt') {
                            $conditions[] = "{$fieldKey}>:gt_{$bindKey}:";
                            $bind['gt_' . $bindKey] = $value;
                        }
                        if ($op == '$gte') {
                            $conditions[] = "{$fieldKey}>=:gte_{$bindKey}:";
                            $bind['gte_' . $bindKey] = $value;
                        }
                        
                        if ($op == '$like') {
                            // 解决like查询
                            $conditions[] = "{$fieldKey} LIKE :like_{$bindKey}:";
                            $bind['like_' . $bindKey] = $value;
                        }
                    }
                } else {
                    if ($item instanceof \MongoRegex) {
                        $conditions[] = "{$fieldKey} LIKE :{$bindKey}:";
                    } else {
                        $conditions[] = "{$fieldKey}=:{$bindKey}:";
                    }
                    $value = $this->changeValue4Conditions($item, $key);
                    $bind[$bindKey] = $value;
                }
            }
        }
        if (empty($bind)) {
            return array();
        } else {
            return array_merge(array(
                'conditions' => '(' . implode(" {$condition_op} ", $conditions) . ')',
                'bind' => $bind
            ), $forUpdate);
        }
    }

    public function getSort(array $sort)
    {
        $order = array();
        foreach ($sort as $key => $value) {
            if ($key == '__RANDOM__') {
                // 解决随机查询
                $order[] = "rand()";
            } else {
                $fieldKey = "[{$key}]";
                // $fieldKey = "{$key}";
                if (intval($value) > 0) {
                    $order[] = "{$fieldKey} ASC";
                } else {
                    $order[] = "{$fieldKey} DESC";
                }
            }
        }
        $order = implode(',', $order);
        if (empty($order)) {
            return array();
        } else {
            return array(
                'order' => $order
            );
        }
    }

    protected function changeValue4Conditions($value, $field)
    {
        if ($field == '_id') {
            $value = $this->getMongoId4Query($value);
            // die("_id's value:" . $value);
            return $value;
        } else {
            if (is_bool($value)) {
                $value = intval($value);
                return $value;
            }
            if ($value instanceof \MongoDate) {
                $value = date('Y-m-d H:i:s', $value->sec);
                return $value;
            }
            if ($value instanceof \MongoRegex) {
                // /系统管理员/i->'%Art%'
                $value = $value->__toString();
                $value = str_ireplace('/i', '%', $value);
                $value = str_ireplace('/^$', '', $value);
                $value = str_ireplace('/', '%', $value);
                return $value;
            }
        }
        return $value;
    }

    protected function changeValue4Save($value)
    {
        if ($value instanceof \MongoDate) {
            $value = date('Y-m-d H:i:s', $value->sec);
        } elseif (is_bool($value)) {
            $value = intval($value);
        } elseif (is_array($value)) {
            if (! empty($value)) {
                $value = json_encode($value);
            } else {
                $value = "";
            }
        } elseif (is_object($value)) {
            $value = json_encode($value);
        }
        
        return $value;
    }

    public function getInsertContents(array $datas)
    {
        $fields = array();
        $bindFields = array();
        $values = array();
        if (empty($datas)) {
            throw new \Exception("字段没有定义", - 999);
        }
        if (! isset($datas['_id'])) {
            $_id = new \MongoId();
            $datas['_id'] = $_id->__toString();
        }
        $datas['__CREATE_TIME__'] = $datas['__MODIFY_TIME__'] = getCurrentTime();
        $datas['__REMOVED__'] = false;
        
        foreach ($datas as $field => $value) {
            $fieldKey = "[{$field}]";
            $fields[] = "{$fieldKey}";
            $fieldBindKey = "{$field}_1";
            $bindFields[] = ":{$fieldBindKey}:";
            $values[$fieldBindKey] = $this->changeValue4Save($value);
        }
        if (empty($fields)) {
            throw new \Exception("字段没有定义", - 999);
        } else {
            return array(
                'fields' => implode(",", $fields),
                'bindFields' => implode(",", $bindFields),
                'values' => $values,
                '_id' => $datas['_id']
            );
        }
    }

    public function getUpdateContents(array $object)
    {
        $fields = array();
        $values = array();
        if (empty($object)) {
            throw new \Exception("更新字段没有定义", - 999);
        }
        
        foreach ($object as $key => $items) {
            switch ($key) {
                case '$exp':
                    if (! empty($items)) {
                        foreach ($items as $field => $value) {
                            $fieldKey = "[{$field}]";
                            $fields[] = "{$fieldKey}={$value}";
                        }
                    }
                    break;
                case '$set':
                    if (! empty($items)) {
                        foreach ($items as $field => $value) {
                            $fieldKey = "[{$field}]";
                            $fields[] = "{$fieldKey}=:{$field}:";
                            $values[$field] = $this->changeValue4Save($value);
                        }
                    }
                    break;
                case '$inc':
                    if (! empty($items)) {
                        foreach ($items as $field => $value) {
                            $fieldKey = "[{$field}]";
                            $value = $this->changeValue4Save($value);
                            $fields[] = "{$fieldKey}={$fieldKey}+{$value}";
                        }
                    }
                    break;
                default:
                    throw new \Exception("更新类别没有定义", - 999);
            }
        }
        
        if (empty($fields)) {
            throw new \Exception("更新字段没有定义", - 999);
        } else {
            $field = '__MODIFY_TIME__';
            $value = getCurrentTime();
            $fieldKey = "[{$field}]";
            $fields[] = "{$fieldKey}=:{$field}:";
            $values[$field] = $this->changeValue4Save($value);
            
            return array(
                'fields' => implode(",", $fields),
                'values' => $values
            );
        }
    }

    protected function executeQuery($phql, array $data, $isQuery = false)
    {
        if (! $this->isPhql) {
            try {
                $phql = preg_replace('/:(.*?):/i', ':$1', $phql);
                $phql = preg_replace('/\[(.*?)\]/i', '`$1`', $phql);
                if ($this->isDebug) {
                    echo "<pre><br/>";
                    echo $phql . "<br/>";
                    var_dump($data);
                    die('OK');
                }
                $di = $this->getDI();
                $db = $di['db'];
                if (empty($isQuery)) {
                    $result = $db->execute($phql, $data);
                } else {
                    $result = new Resultset(null, $this, $db->query($phql));
                }
                return $result;
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            if ($this->isDebug) {
                echo "<pre><br/>";
                echo $phql . "<br/>";
                var_dump($data);
                die('OK');
            }
            $result = $this->modelsManager->executeQuery($phql, $data);
            if (empty($isQuery)) {
                if ($result->success() == false) {
                    $msgList = array();
                    foreach ($result->getMessages() as $message) {
                        $msgList[] = $message->getMessage();
                    }
                    throw new \Exception(implode(",", $msgList), - 999);
                }
            }
            return $result;
        }
    }

    public function changeToBoolean($field)
    {
        if (empty($field)) {
            return false;
        }
        return ($field);
    }

    public function changeToArray($field)
    {
        if (empty($field)) {
            return array();
        }
        if (is_array($field)) {
            return $field;
        } else {
            return json_decode($field, true);
        }
    }

    public function changeToMongoDate($field)
    {
        if (empty($field)) {
            return $field;
        }
        return getCurrentTime(strtotime($field));
        // if (is_date($field)) {
        // } else {
        // return json_decode($field, true);
        // }
    }

    protected function getColumns(array $fields = array())
    {
        $ret = array();
        if (! empty($fields)) {
            $ret['column'] = implode(',', $fields);
        }
        return $ret;
    }

    protected function getGroups(array $groups = array())
    {
        $ret = array();
        if (! empty($groups)) {
            $ret['group'] = implode(',', $groups);
        }
        return $ret;
    }
}
