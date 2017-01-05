<?php
namespace App\Member\Models;

class Friend extends \App\Common\Models\Member\Friend
{

    const STATE0 = 0; // 申请中
    const STATE1 = 1; // 同意
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
    public function getApplyList($page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $query = array(
            'state' => self::STATE0
        );
        if (empty($sort)) {
            $sort = array();
            $sort['apply_time'] = - 1;
        }
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, array());
        return $list;
    }

    public function getApplyCount(array $otherConditions = array())
    {
        $query = array(
            'state' => self::STATE0
        );
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $num = $this->count($query);
        return $num;
    }

    /**
     * 分页获取获取好友的列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getAgreeList($page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $query = array();
        if (empty($sort)) {
            $sort = array();
            $sort['agree_time'] = - 1;
        }
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        // $this->setDebug(true);
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, array());
        return $list;
    }

    public function apply($from_user_id, $from_user_nickname, $from_user_email, $from_user_mobile, $from_user_register_by, $to_user_id, $to_user_nickname, $to_user_email, $to_user_mobile, $to_user_register_by)
    {
        $data = array();
        $data['from_user_id'] = $from_user_id;
        $data['from_user_nickname'] = $from_user_nickname;
        $data['from_user_email'] = $from_user_email;
        $data['from_user_mobile'] = $from_user_mobile;
        $data['from_user_register_by'] = $from_user_register_by;
        $data['to_user_id'] = $to_user_id;
        $data['to_user_nickname'] = $to_user_nickname;
        $data['to_user_email'] = $to_user_email;
        $data['to_user_mobile'] = $to_user_mobile;
        $data['to_user_register_by'] = $to_user_register_by;
        $data['apply_time'] = getCurrentTime();
        $data['state'] = self::STATE0;
        return $this->insert($data);
    }

    /**
     * 忽略好友
     *
     * @param string $to_user_id            
     * @param string $applyId            
     */
    public function ignore($to_user_id, $applyId)
    {
        $query = array();
        if (! empty($applyId)) {
            $query['_id'] = $applyId;
        }
        $query['to_user_id'] = $to_user_id;
        return $this->remove($query);
    }

    /**
     * 同意好友
     *
     * @param string $to_user_id            
     * @param string $applyId            
     */
    public function agree($to_user_id, $applyId)
    {
        $query = array();
        if (! empty($applyId)) {
            $query['_id'] = $applyId;
        }
        $query['to_user_id'] = $to_user_id;
        $data = array();
        $data['state'] = self::STATE1;
        $data['agree_time'] = getCurrentTime();
        return $this->update($query, array(
            '$set' => $data
        ));
    }

    /**
     * 删除好友
     *
     * @param string $to_user_id            
     * @param string $from_user_id            
     */
    public function delete($to_user_id, $from_user_id)
    {
        $query1 = array();
        $query1['to_user_id'] = $to_user_id;
        $query1['from_user_id'] = $from_user_id;
        
        $query2 = array();
        $query2['from_user_id'] = $to_user_id;
        $query2['to_user_id'] = $from_user_id;
        $query = array(
            '__QUERY_OR__' => array(
                $query1,
                $query2
            )
        );
        return $this->remove($query);
    }

    public function check($from_user_id, $to_user_id)
    {
        $query1 = array();
        $query1['from_user_id'] = $from_user_id;
        $query1['to_user_id'] = $to_user_id;
        
        $query2 = array();
        $query2['from_user_id'] = $to_user_id;
        $query2['to_user_id'] = $from_user_id;
        $query = array(
            '__QUERY_OR__' => array(
                $query1,
                $query2
            )
        );
        
        return $this->findOne($query);
    }

    public function getMyFriendIds($my, $page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $otherConditions = array();
        $query1 = array();
        $query1['to_user_id'] = $my;
        $query1['state'] = \App\Member\Models\Friend::STATE1;
        
        $query2 = array();
        $query2['from_user_id'] = $my;
        $query2['state'] = \App\Member\Models\Friend::STATE1;
        
        $otherConditions = array(
            '__QUERY_OR__' => array(
                $query1,
                $query2
            )
        );
        $friendList = $this->getAgreeList($page, $limit, $otherConditions, $sort);
        $friend_ids = array();
        if (! empty($friendList['datas'])) {            
            foreach ($friendList['datas'] as $friend) {
                if ($friend['from_user_id'] == $my) {
                    $friend_ids[] = $friend['to_user_id'];
                } else {
                    $friend_ids[] = $friend['from_user_id'];
                }
            }
        }
        return $friend_ids;
    }
}