<?php
namespace App\Member\Models;

class News extends \App\Common\Models\Member\News
{

    /**
     * 分页获取用户动态的列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getNewsList($page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $query = array();
        if (empty($sort)) {
            $sort = array();
            $sort['news_time'] = - 1;
        }
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, array());
        return $list;
    }

    public function log($user_id, $user_name, $user_avatar, $user_register_by, $action, $content_id, array $memo)
    {
        $data = array();
        $data['user_id'] = $user_id;
        $data['user_name'] = $user_name;
        $data['user_avatar'] = $user_avatar;
        $data['user_register_by'] = $user_register_by;
        $data['action'] = $action;
        $data['content_id'] = $content_id;
        $data['news_time'] = getCurrentTime();
        $data['memo'] = json_encode($memo);
        return $this->insert($data);
    }
}