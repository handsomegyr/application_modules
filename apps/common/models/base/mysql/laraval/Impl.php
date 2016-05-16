<?php
namespace App\Common\Models\Base\Mysql\Laravel;

use App\Common\Models\Base\Mysql\Base;
use App\Common\Models\Base\Mysql\BaseTrait;
use PDO;
use DB;

class Impl2 extends Base
{
    
    use BaseTrait;

    protected $model = NULL;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function setDebug($isDebug)
    {
        $this->isDebug = $isDebug;
    }

    public function reorganize(array $data)
    {
        return $this->model->reorganize($data);
    }

    public function getSource()
    {
        return $this->model->getSource();
    }

    public function begin()
    {
        return DB::beginTransaction();
    }

    public function commit()
    {
        return DB::commit();
    }

    public function rollback()
    {
        return DB::rollBack();
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
		$result = $this->executeQuery($phql, $conditions['bind'], 'select');
        if (count($result) > 0) {
            $num = $result[0]['num'];
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
		$info = $this->executeQuery($phql, $conditions['bind'], 'selectOne');
        // die('info:' . var_dump($info));
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
        $ret = $this->executeQuery($phql, $conditions['bind'], 'select');
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
        $ret = $this->executeQuery($phql, $conditions['bind'], 'select');
        
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
        $ret = $this->executeQuery($phql, $conditions['bind'], 'select');
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
        $ret = $this->executeQuery($phql, $conditions['bind'], 'select');
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
        $result = $this->executeQuery($phql, $data, 'insert');
        $_id = $insertFieldValues['_id'];
        return $this->findOne(array(
            '_id' => $_id
        ));
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
		$result = $this->executeQuery($phql, $data, 'update');
    }

    public function remove(array $query)
    {
        if (empty($query)) {
            throw new \Exception("删除数据的时候请指定条件", - 999);
        }
        
        $conditions = $this->getConditions($query);
        $className = $this->getSource();
        $phql = "DELETE FROM {$className} WHERE {$conditions['conditions']}";
		$result = $this->executeQuery($phql, $conditions['bind'], 'delete');
    }

	protected function executeQuery($phql, array $data, $method = 'select')
    {
        try {
            $phql = preg_replace('/:(.*?):/i', ':$1', $phql);
            $phql = preg_replace('/\[(.*?)\]/i', '`$1`', $phql);
            if ($this->isDebug) {
                echo "<pre><br/>";
                echo $phql . "<br/>";
                var_dump($data);
                die('OK');
            }
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $result = DB::$method($phql, $data);            
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
