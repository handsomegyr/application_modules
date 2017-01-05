<?php
namespace App\Member\Models;

class Visitor extends \App\Common\Models\Member\Visitor
{

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array();
        return $query;
    }

    /**
     * 分页获取获取申请好友的列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getList($visited_user_id, $page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $query = $this->getQuery();
        $query['visited_user_id'] = $visited_user_id;
        if (empty($sort)) {
            $sort = array();
            $sort['browser_time'] = - 1;
        }
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, array());
        return $list;
    }

    public function visit($visited_user_id, $visit_user_id)
    {
        // 检查有没有访问过
        $info = $this->getVisitedInfo($visited_user_id, $visit_user_id);
        if (empty($info)) {
            $data = array();
            $data['visited_user_id'] = $visited_user_id;
            $data['visit_user_id'] = $visit_user_id;
            $data['browser_time'] = getCurrentTime();
            $info = $this->insert($data);
        }
        return $info;
    }

    public function getVisitedInfo($visited_user_id, $visit_user_id)
    {
        $query = array();
        $query['visited_user_id'] = $visited_user_id;
        $query['visit_user_id'] = $visit_user_id;
        $info = $this->findOne($query);
        return $info;
    }
}