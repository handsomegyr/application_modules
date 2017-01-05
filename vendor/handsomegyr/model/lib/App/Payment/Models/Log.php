<?php
namespace App\Payment\Models;

class Log extends \App\Common\Models\Payment\Log
{

    /**
     * 默认排序方式
     *
     * @param number $dir            
     * @return array
     */
    public function getDefaultSort()
    {
        $sort = array();
        $sort['log_time'] = - 1;
        return $sort;
    }

    /**
     * 默认查询条件
     *
     * @return array
     */
    public function getDefaultQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 根据某种条件获取列表
     *
     * @param array $query            
     * @param array $sort            
     * @param array $fields            
     * @return array
     */
    public function getPageList($page = 1, $limit = 10, array $query = array(), array $sort = array(), array $fields = array())
    {
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $defaultQuery = $this->getDefaultQuery();
        $query = array_merge($query, $defaultQuery);
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, $fields);
        return $list;
    }

    public function getuserbcrecord($user_id, $page = 1, $limit = 10, $type = 0, $beginTime = 0, $endTime = 0)
    {
        $otherConditions = array();
        $otherConditions['user_id'] = $_SESSION['member_id'];
        if (! empty($type)) {
            $otherConditions['type'] = $type;
        }
        if (! empty($beginTime)) {
            $otherConditions['log_time']['$gte'] = getCurrentTime($beginTime);
        }
        if (! empty($endTime)) {
            $otherConditions['log_time']['$lte'] = getCurrentTime($endTime);
        }
        $list = $this->getPageList($page, $limit, $otherConditions);
        return $list;
    }

    public function recordLog($user_id, $type, $money, $desc, $memo)
    {
        $data = array();
        $data['user_id'] = $user_id;
        $data['type'] = $type;
        $data['money'] = $money;
        $data['desc'] = $desc;
        $data['log_time'] = getCurrentTime();
        $data['memo'] = json_encode($memo);
        return $this->insert($data);
    }

    public function getSummaryMoney($user_id, $type)
    {
        $query = array(
            'user_id' => $user_id,
            'type' => $type
        );
        $fields = array(
            'money'
        );
        $groups = array(
            'user_id'
        );
        $summary = 0;
        $ret = $this->sum($query, $fields, $groups);
        foreach ($ret as $row) {
            $summary += $row['sumatory'];
        }
        return $summary;
    }
}