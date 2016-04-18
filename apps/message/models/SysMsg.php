<?php
namespace App\Message\Models;

class SysMsg extends \App\Common\Models\Message\SysMsg
{

    public function log($to_user_id, $content)
    {
        $data = array();
        $data['to_user_id'] = $to_user_id;
        $data['content'] = $content;
        $data['msg_time'] = getCurrentTime();
        $data['is_read'] = false;
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
    public function getListByUserId($to_user_id, $page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $query = array(
            'to_user_id' => $to_user_id
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

    public function removeByUserId($to_user_id)
    {
        $query = array(
            'to_user_id' => $to_user_id
        );
        $this->remove($query);
    }
}