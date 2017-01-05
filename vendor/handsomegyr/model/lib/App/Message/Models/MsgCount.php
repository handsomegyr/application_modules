<?php
namespace App\Message\Models;

class MsgCount extends \App\Common\Models\Message\MsgCount
{

    public function log($msg_user_id)
    {
        $data = array();
        $data['user_id'] = $msg_user_id;
        return $this->insert($data);
    }

    /**
     * 增加系统消息数量
     */
    public function incSysMsgCount($msg_user_id)
    {
        $query = array();
        $query['user_id'] = $msg_user_id;
        $incArr = array(
            'sysMsgCount' => 1
        );
        $this->update($query, array(
            '$inc' => $incArr
        ));
    }

    public function clearSysMsgCount($msg_user_id)
    {
        $query = array();
        $query['user_id'] = $msg_user_id;
        $data = array(
            'sysMsgCount' => 0
        );
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 增加好友消息数量
     */
    public function incFriendMsgCount($msg_user_id)
    {
        $query = array();
        $query['user_id'] = $msg_user_id;
        $incArr = array(
            'friendMsgCount' => 1
        );
        $this->update($query, array(
            '$inc' => $incArr
        ));
    }

    public function clearFriendMsgCount($msg_user_id)
    {
        $query = array();
        $query['user_id'] = $msg_user_id;
        $data = array(
            'friendMsgCount' => 0
        );
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 增加回复消息数量
     */
    public function incReplyMsgCount($msg_user_id)
    {
        $query = array();
        $query['user_id'] = $msg_user_id;
        $incArr = array(
            'replyMsgCount' => 1
        );
        $this->update($query, array(
            '$inc' => $incArr
        ));
    }

    public function clearReplyMsgCount($msg_user_id)
    {
        $query = array();
        $query['user_id'] = $msg_user_id;
        $data = array(
            'replyMsgCount' => 0
        );
        $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 增加私信数量
     */
    public function incPrivateMsgCount($msg_user_id)
    {
        $query = array();
        $query['user_id'] = $msg_user_id;
        $incArr = array(
            'privMsgCount' => 1
        );
        $this->update($query, array(
            '$inc' => $incArr
        ));
    }

    public function clearPrivateMsgCount($msg_user_id)
    {
        $query = array();
        $query['user_id'] = $msg_user_id;
        $data = array(
            'privMsgCount' => 0
        );
        $this->update($query, array(
            '$set' => $data
        ));
    }
    
    public function getInfoByUserId($msg_user_id)
    {
        $query = array();
        $query['user_id'] = $msg_user_id;        
        return $this->findOne($query);
    }
    
    
}