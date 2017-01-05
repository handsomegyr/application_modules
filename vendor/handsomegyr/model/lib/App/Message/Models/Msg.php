<?php
namespace App\Message\Models;

class Msg extends \App\Common\Models\Message\Msg
{

    public function log($from_user_id, $to_user_id, $content)
    {
        $data = array();
        $data['from_user_id'] = $from_user_id;
        $data['to_user_id'] = $to_user_id;
        $data['content'] = $content;
        $data['msg_time'] = getCurrentTime();
        return $this->insert($data);
    }

    /**
     * 分页获取获取消息的列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getListBy2UserId($from_user_id, $to_user_id, $page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $query1 = array(
            'from_user_id' => $from_user_id,
            'to_user_id' => $to_user_id
        );
        $query2 = array(
            'from_user_id' => $to_user_id,
            'to_user_id' => $from_user_id
        );
        $query = array(
            '__QUERY_OR__' => array(
                $query1,
                $query2
            )
        );
        if (empty($sort)) {
            $sort = array();
            $sort['msg_time'] = - 1;
        }
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, array());
        return $list;
    }
}