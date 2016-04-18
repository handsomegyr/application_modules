<?php
namespace App\Invitation\Models;

class InvitationGotDetail extends \App\Common\Models\Invitation\InvitationGotDetail
{

    /**
     * 根据user_id获取信息
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @return array
     */
    public function getInfoByUserId($user_id, $activity_id = '')
    {
        $query = array(
            'got_user_id' => $user_id,
            'activity_id' => $activity_id
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 生成接受记录
     *
     * @param string $invitation_id            
     * @param string $owner_user_id            
     * @param string $owner_user_name            
     * @param string $owner_user_headimgurl            
     * @param string $got_user_id            
     * @param string $got_user_name            
     * @param string $got_user_headimgurl            
     * @param int $got_worth 
     * @param int $got_worth2            
     * @param string $activity_id            
     * @param array $memo            
     * @return array
     */
    public function create($invitation_id, $owner_user_id, $owner_user_name, $owner_user_headimgurl, $got_user_id, $got_user_name, $got_user_headimgurl, $got_worth = 0,$got_worth2 = 0, $activity_id = '', array $memo = array())
    {
        $data = array();
        $data['activity_id'] = $activity_id; // 邀请活动
        $data['invitation_id'] = $invitation_id; // 邀请函ID
        $data['owner_user_id'] = $owner_user_id; // 发送邀请函的user_id
        $data['owner_user_name'] = $owner_user_name; // 发送邀请函的用户名
        $data['owner_user_headimgurl'] = $owner_user_headimgurl; // 发送邀请函的头像
        $data['got_user_id'] = $got_user_id; // 领邀请函的user_id
        $data['got_user_name'] = $got_user_name; // 领邀请函的用户名
        $data['got_user_headimgurl'] = $got_user_headimgurl; // 领邀请函的头像
        $data['got_time'] = getCurrentTime(); // 获取时间
        $data['got_worth'] = $got_worth; // 获取价值
        $data['got_worth2'] = $got_worth2; // 获取价值2
        $data['memo'] = $memo; // 备注
        $info = $this->insert($data);
        return $info;
    }

    /**
     * 是否已经领过或领取次数已达到
     *
     * @param string $invitation_id            
     * @param string $got_user_id            
     * @return boolean
     */
    public function isGot($invitation_id, $got_user_id, $receive_limit = 0)
    {
        if (empty($receive_limit)) {
            return false;
        }
        
        $query = array();
        $query['invitation_id'] = $invitation_id; // 邀请函ID
        $query['got_user_id'] = $got_user_id; // 领邀请函的user_id
        $num = $this->count($query);
        return ($num > ($receive_limit - 1));
    }

    /**
     * 分页读取某个用户的全部邀请函
     *
     * @param string $invitationId            
     * @param number $page            
     * @param number $limit            
     * @return array
     */
    public function getListByPage($invitationId, $page = 1, $limit = 10)
    {
        $sort = array(
            'got_time' => - 1
        );
        $query = array();
        $query['invitation_id'] = $invitationId;
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit);
        return $list;
    }

    /**
     * 分页读取朋友帮我的列表
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @param number $page            
     * @param number $limit            
     * @param array $sort            
     * @return array
     */
    public function getListByOwnerUserId($user_id, $activity_id = '', $page = 1, $limit = 10, array $sort = array())
    {
        if (empty($sort)) {
            $sort = array(
                'got_time' => - 1
            );
        }
        $query = array();
        $query['owner_user_id'] = $user_id;
        $query['activity_id'] = $activity_id;
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit);
        return $list;
    }

    /**
     * 分页读取我帮朋友的列表
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @param number $page            
     * @param number $limit            
     * @param array $sort            
     * @return array
     */
    public function getListByGotUserId($user_id, $activity_id = '', $page = 1, $limit = 10, array $sort = array())
    {
        if (empty($sort)) {
            $sort = array(
                'got_time' => - 1
            );
        }
        $query = array();
        $query['got_user_id'] = $user_id;
        $query['activity_id'] = $activity_id;
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit);
        return $list;
    }

    /**
     * 获取朋友帮我收集的总价值
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @return number
     */
    public function getTotalByOwnerUserId($user_id, $activity_id = '')
    {
        /**
         * [
         * { $match: { status: "A" } },
         * { $group: { _id: "$cust_id", total: { $sum: "$amount" } } },
         * { $sort: { total: -1 } }
         * ]
         */
        $rst = $this->aggregate(array(
            array(
                '$match' => array(
                    'owner_user_id' => $user_id,
                    'activity_id' => $activity_id
                )
            ),
            array(
                '$group' => array(
                    '_id' => '$owner_user_id',
                    'total' => array(
                        '$sum' => '$got_worth'
                    )
                )
            )
        ));
        
        if (! empty($rst['result'])) {
            return $rst['result'][0]['total'];
        } else {
            return 0;
        }
    }

    /**
     * 获取我帮朋友收集的总价值
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @return number
     */
    public function getTotalByGotUserId($user_id, $activity_id = '')
    {
        /**
         * [
         * { $match: { status: "A" } },
         * { $group: { _id: "$cust_id", total: { $sum: "$amount" } } },
         * { $sort: { total: -1 } }
         * ]
         */
        $rst = $this->aggregate(array(
            array(
                '$match' => array(
                    'got_user_id' => $user_id,
                    'activity_id' => $activity_id
                )
            ),
            array(
                '$group' => array(
                    '_id' => '$got_user_id',
                    'total' => array(
                        '$sum' => '$got_worth'
                    )
                )
            )
        ));
        
        if (! empty($rst['result'])) {
            return $rst['result'][0]['total'];
        } else {
            return 0;
        }
    }

    /**
     * 获取我帮朋友收集的总次数
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @return number
     */
    public function getTotalCountByGotUserId($user_id, $activity_id = '')
    {
        $query = array(
            'got_user_id' => $user_id,
            'activity_id' => $activity_id
        );
        $num = $this->count($query);
        return $num;
    }

    /**
     * 根据邀请函ID和领邀请函微信号
     *
     * @param string $invitation_id            
     * @param string $got_user_id            
     * @return array
     */
    public function getInfoByInvitationIdAndGotUserId($invitation_id, $got_user_id)
    {
        $query = array();
        $query['invitation_id'] = $invitation_id; // 邀请函ID
        $query['got_user_id'] = $got_user_id; // 领邀请函的user_id
        $info = $this->findOne($query);
        return $info;
    }

    public function incWorth($invitation_id, $got_user_id, $worth, $worth2 = 0)
    {
        $query = array();
        $query['invitation_id'] = $invitation_id; // 邀请函ID
        $query['got_user_id'] = $got_user_id; // 领邀请函的user_id
        $this->update($query, array(
            '$inc' => array(
                'got_worth' => $worth,
                'got_worth2' => $worth2
            )
        ));
    }
}