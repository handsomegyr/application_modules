<?php
namespace App\Message\Models;

class MsgStatistics extends \App\Common\Models\Message\MsgStatistics
{

    public function log($user1_id, $user2_id, $content)
    {
        $info = $this->getInfoBy2UserId($user1_id, $user2_id);
        if (empty($info)) {
            $data = array();
            $data['user1_id'] = $user1_id;
            $data['user2_id'] = $user2_id;
            $data['content'] = $content;
            $data['msg_user_id'] = $user1_id;
            $data['msg_time'] = getCurrentTime();
            $data['msg_num'] = 1;
            $data['user1_unread_num'] = 0;
            $data['user2_unread_num'] = 1;
            return $this->insert($data);
        } else {
            $this->incMsgNum($info, $user1_id, $content);
            return $info;
        }
    }

    public function getInfoBy2UserId($user1_id, $user2_id)
    {
        $query = $this->getQueryBy2UserId($user1_id, $user2_id);
        return $this->findOne($query);
    }

    /**
     * 增加回复和未读数量
     */
    public function incMsgNum($info, $msg_user_id, $content)
    {
        $query = array();
        $query['_id'] = $info['_id'];
        $incArr = array(
            'msg_num' => 1
        );
        if ($msg_user_id == $info['user1_id']) {
            $incArr['user2_unread_num'] = 1;
        } else {
            $incArr['user1_unread_num'] = 1;
        }
        $this->update($query, array(
            '$inc' => $incArr,
            '$set' => array(
                'msg_user_id' => $msg_user_id,
                'content' => $content,
                'msg_time' => getCurrentTime()
            )
        ));
    }

    /**
     * 分页获取获取消息统计的列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getListByUserId($user_id, $page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $query1 = array(
            'user1_id' => $user_id
        );
        $query2 = array(
            'user2_id' => $user_id
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

    /**
     * 增加回复和未读数量
     */
    public function setToRead($info, $msg_user_id)
    {
        $query = array();
        $query['_id'] = $info['_id'];
        $data = array();
        if ($msg_user_id == $info['user1_id']) {
            $data['user2_unread_num'] = 0;
        } else {
            $data['user1_unread_num'] = 0;
        }
        $this->update($query, array(
            '$set' => $data
        ));
    }

    protected function getQueryBy2UserId($user1_id, $user2_id)
    {
        $query1 = array();
        $query1['user1_id'] = $user1_id;
        $query1['user2_id'] = $user2_id;
        
        $query2 = array();
        $query2['user1_id'] = $user2_id;
        $query2['user2_id'] = $user1_id;
        
        $query = array(
            '__QUERY_OR__' => array(
                $query1,
                $query2
            )
        );
        return $query;
    }
}