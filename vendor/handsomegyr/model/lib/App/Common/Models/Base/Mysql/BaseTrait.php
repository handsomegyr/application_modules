<?php
namespace App\Common\Models\Base\Mysql;

trait BaseTrait
{

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

    protected function getConditions(array $where, $condition_op = "AND")
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

    protected function getSort(array $sort)
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

    protected function getInsertContents(array $datas)
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

    protected function getUpdateContents(array $object)
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

    protected function changeToBoolean($field)
    {
        if (empty($field)) {
            return false;
        }
        return ($field);
    }

    protected function changeToArray($field)
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

    protected function changeToMongoDate($field)
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

    protected function getSqlAndConditions4Count(array $query)
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $phql = "SELECT COUNT(*) as num FROM {$className} WHERE {$conditions['conditions']}";
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    protected function getSqlAndConditions4FindOne(array $query)
    {
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $phql = "SELECT * FROM {$className} WHERE {$conditions['conditions']}";
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
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
    protected function getSqlAndConditions4Find(array $query, array $sort = null, $skip = 0, $limit = 10, array $fields = array())
    {
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
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    protected function getSqlAndConditions4FindAll(array $query, array $sort = null, array $fields = array())
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
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    protected function getSqlAndConditions4Sum(array $query, array $fields = array(), array $groups = array())
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
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    protected function getSqlAndConditions4Distinct($field, array $query)
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
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }

    /**
     * 执行insert操作
     *
     * @param array $datas            
     */
    protected function getSqlAndConditions4Insert(array $datas)
    {
        $className = $this->getSource();
        $insertFieldValues = $this->getInsertContents($datas);
        $phql = "INSERT INTO {$className}({$insertFieldValues['fields']}) VALUES ({$insertFieldValues['bindFields']})";
        return array(
            'sql' => $phql,
            'insertFieldValues' => $insertFieldValues
        );
    }

    protected function getSqlAndConditions4Update(array $criteria, array $object, array $options = array())
    {
        if (empty($criteria)) {
            throw new \Exception("更新数据的时候请指定条件", - 999);
        }
        
        $className = $this->getSource();
        $conditions = $this->getConditions($criteria);
        $updateFieldValues = $this->getUpdateContents($object);
        $phql = "UPDATE {$className} SET {$updateFieldValues['fields']} WHERE {$conditions['conditions']} ";
        return array(
            'sql' => $phql,
            'updateFieldValues' => $updateFieldValues,
            'conditions' => $conditions
        );
    }

    protected function getSqlAndConditions4Remove(array $query)
    {
        if (empty($query)) {
            // throw new \Exception("删除数据的时候请指定条件", - 999);
            $query = array();
        }
        
        $conditions = $this->getConditions($query);
        if (empty($conditions)) {
            $conditions = array();
            $conditions['conditions'] = '1=1';
            $conditions['bind'] = array();
        }
        $className = $this->getSource();
        $phql = "DELETE FROM {$className} WHERE {$conditions['conditions']}";
        return array(
            'sql' => $phql,
            'conditions' => $conditions
        );
    }
}

?>