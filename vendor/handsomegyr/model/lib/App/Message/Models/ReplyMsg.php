<?php
namespace App\Message\Models;

class ReplyMsg extends \App\Common\Models\Message\ReplyMsg
{

    public function getDefaultSort()
    {
        $sort = array();
        $sort['msg_time'] = - 1;
        return $sort;
    }

    /**
     * 分页获取某人的列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getReplyMsgPageByUserID($reply_user_id, $page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $query = $this->getQuery4UserId($reply_user_id);
        
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        if (empty($sort)) {
            $sort = $this->getDefaultSort();
        }
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, array());
        return $list;
    }

    public function log($relate_id, $reply_user_id, $reply_user_name, $reply_user_avatar, $reply_user_register_by, $reply_content, $to_user_id, $to_user_name, $to_user_avatar, $to_user_register_by, $to_user_content)
    {
        $data = array();
        $data['relate_id'] = $relate_id;
        
        $data['reply_user_id'] = $reply_user_id;
        $data['reply_user_name'] = $reply_user_name;
        $data['reply_user_avatar'] = $reply_user_avatar;
        $data['reply_user_register_by'] = $reply_user_register_by;
        $data['reply_content'] = $reply_content;
        
        $data['to_user_id'] = $to_user_id;
        $data['to_user_name'] = $to_user_name;
        $data['to_user_avatar'] = $to_user_avatar;
        $data['to_user_register_by'] = $to_user_register_by;
        $data['to_user_content'] = $to_user_content;
        
        $data['msg_time'] = getCurrentTime();
        $data['is_read'] = false;
        return $this->insert($data);
    }

    public function removeByUserId($reply_user_id)
    {
        $query = $this->getQuery4UserId($reply_user_id);
        $this->remove($query);
    }

    private function getQuery4UserId($reply_user_id)
    {
        $query = array();
        $query1 = array(
            'reply_user_id' => $reply_user_id
        );
        $query2 = array(
            'to_user_id' => $reply_user_id
        );
        $query = array(
            '__QUERY_OR__' => array(
                $query1,
                $query2
            )
        );
        return $query;
    }
}