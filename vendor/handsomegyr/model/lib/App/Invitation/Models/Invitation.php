<?php
namespace App\Invitation\Models;

class Invitation extends \App\Common\Models\Invitation\Invitation
{

    private $isExclusive = true;

    /**
     * 设置排他
     *
     * @param boolean $isExclusive            
     */
    public function setIsExclusive($isExclusive)
    {
        $this->isExclusive = $isExclusive;
    }

    /**
     * 根据邀请内容ID获取信息
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @param array $otherCondition            
     * @return array
     */
    public function getInfoByUserId($user_id, $activity_id = '', array $otherCondition = array())
    {
        $query = array(
            'user_id' => $user_id,
            'activity_id' => $activity_id
        );
        if (! empty($otherCondition)) {
            $query = array_merge($query, $otherCondition);
        }
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 根据邀请内容ID获取最新信息
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @param array $otherCondition            
     * @return array
     */
    public function getLatestInfoByUserId($user_id, $activity_id = '', array $otherCondition = array())
    {
        $query = array(
            'user_id' => $user_id,
            'activity_id' => $activity_id
        );
        if (! empty($otherCondition)) {
            $query = array_merge($query, $otherCondition);
        }
        $sort = array(
            'send_time' => - 1
        );
        $list = $this->find($query, $sort, 0, 1);
        if (! empty($list['datas'])) {
            return $list['datas'][0];
        } else {
            return null;
        }
    }

    /**
     * 生成邀请函
     *
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param string $url            
     * @param string $desc            
     * @param number $worth            
     * @param number $worth2            
     * @param number $invited_total            
     * @param number $personal_receive_num            
     * @param boolean $is_need_subscribed            
     * @param string $subscibe_hint_url            
     * @param string $activity_id            
     * @param array $memo            
     * @return array
     */
    public function create($user_id, $user_name, $user_headimgurl, $url, $desc, $worth = 0, $worth2 = 0, $invited_total = 0, $personal_receive_num = 0, $is_need_subscribed = false, $subscibe_hint_url = "", $activity_id = '', array $memo = array('memo'=>''))
    {
        $data = array();
        $data['activity_id'] = $activity_id; // 邀请活动
        $data['user_id'] = $user_id; // 微信ID
        $data['user_name'] = $user_name; // 邀请函用户名
        $data['user_headimgurl'] = $user_headimgurl; // 邀请函头像
        $data['url'] = $url; // 邀请函URL
        $data['desc'] = $desc; // 邀请函详细
        $data['worth'] = $worth; // 价值
        $data['worth2'] = $worth2; // 价值2
        $data['invited_num'] = 0; // 接受邀请次数
        $data['invited_total'] = $invited_total; // 接受邀请总次数，如果为0，不限制
        $data['send_time'] = getCurrentTime(); // 发送时间
        $data['is_need_subscribed'] = $is_need_subscribed; // 是否需要微信关注
        $data['subscibe_hint_url'] = $subscibe_hint_url; // 微信关注提示页面链接
        $data['personal_receive_num'] = $personal_receive_num; // 个人领取次数，如果为0，不限制
        $data['lock'] = false; // 未锁定
        $data['expire'] = getCurrentTime(); // 过期时间
        $data['memo'] = $memo; // 备注
        
        $info = $this->insert($data);
        return $info;
    }

    /**
     * 根据user_id生成或获取邀请函
     *
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param string $url            
     * @param string $desc            
     * @param number $worth            
     * @param number $worth2            
     * @param number $invited_total            
     * @param number $personal_receive_num            
     * @param boolean $is_need_subscribed            
     * @param string $subscibe_hint_url            
     * @param string $activity_id            
     * @param array $memo            
     * @return array
     */
    public function getOrCreateByUserId($user_id, $user_name, $user_headimgurl, $url, $desc, $worth = 0, $worth2 = 0, $invited_total = 0, $personal_receive_num = 0, $is_need_subscribed = false, $subscibe_hint_url = "", $activity_id = '', array $memo = array())
    {
        $info = $this->getInfoByUserId($user_id, $activity_id);
        if (empty($info)) {
            $info = $this->create($user_id, $user_name, $user_headimgurl, $url, $desc, $worth, $worth2, $invited_total, $personal_receive_num, $is_need_subscribed, $subscibe_hint_url, $activity_id, $memo);
        }
        return $info;
    }

    /**
     * 发送邀请次数
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @return int
     */
    public function getSentCount($user_id, $activity_id = '')
    {
        $count = $this->count(array(
            'user_id' => $user_id,
            'activity_id' => $activity_id
        ));
        return $count;
    }

    /**
     * 加锁
     *
     * @param string $invitationId            
     * @param boolean $isExclusive            
     * @throws Exception
     * @return boolean
     */
    public function lock($invitationId)
    {
        if (! $this->isExclusive) { // 非排他
            return false;
        }
        // 锁定之前，先清除过期锁
        $this->expire($invitationId);
        
        // 查找当前用户的锁
        $lock = $this->findOne(array(
            '_id' => ($invitationId)
        ));
        if ($lock == null) {
            throw new \Exception("未初始化锁");
        } else {
            $query = array(
                '_id' => $lock['_id'],
                'lock' => false
            );
        }
        
        $options = array();
        $options['query'] = $query;
        $options['update'] = array(
            '$set' => array(
                'lock' => true,
                'expire' => getCurrentTime(time() + 300)
            )
        );
        $options['new'] = false; // 返回更新之前的值
        
        $rst = $this->findAndModify($options);
        if (empty($rst['ok'])) {
            throw new \Exception("findandmodify失败");
        }
        
        if (empty($rst['value'])) {
            // 已经被锁定
            return true;
        } else {
            // 未被加锁，但是现在已经被锁定
            return false;
        }
    }

    /**
     * 解锁
     *
     * @param string $invitationId            
     */
    public function unlock($invitationId)
    {
        if (! $this->isExclusive) { // 非排他
            return;
        }
        return $this->update(array(
            '_id' => ($invitationId)
        ), array(
            '$set' => array(
                'lock' => false,
                'expire' => getCurrentTime()
            )
        ));
    }

    /**
     * 自动清除过期的锁
     *
     * @param string $invitationId            
     */
    public function expire($invitationId)
    {
        return $this->update(array(
            '_id' => ($invitationId),
            'expire' => array(
                '$lte' => getCurrentTime()
            )
        ), array(
            '$set' => array(
                'lock' => false
            )
        ));
    }

    /**
     * 增加接受邀请次数
     *
     * @param mixed $idOrObject            
     * @param int $invited_num            
     * @param int $worth            
     * @param int $worth2            
     * @param array $otherIncData            
     * @param array $otherUpdateData            
     * @throws Exception
     * @return boolean
     */
    public function incInvitedNum($idOrObject, $invited_num = 1, $worth = 0, $worth2 = 0, array $otherIncData = array(), array $otherUpdateData = array())
    {
        if (is_string($idOrObject)) {
            $info = $this->getInfoById($idOrObject);
        } else {
            $info = $idOrObject;
        }
        if (empty($info)) {
            throw new \Exception("邀请函记录不存在");
        }
        $query = array(
            '_id' => $info['_id']
        );
        
        if ($this->isExclusive) { // 排他
            $query['lock'] = true;
        }
        
        if (! empty($info['invited_total'])) {
            $query['invited_num'] = array(
                '$lt' => $info['invited_total']
            );
        }
        
        $options = array();
        $options['query'] = $query;
        
        $update = array(
            '$inc' => array(
                'invited_num' => 1,
                'worth' => $worth,
                'worth2' => $worth2
            )
        );
        if (! empty($otherIncData)) {
            $update['$inc'] = array_merge($update['$inc'], $otherIncData);
        }
        
        if (! empty($otherUpdateData)) {
            $update['$set'] = $otherUpdateData;
        }
        
        $options['update'] = $update;
        $options['new'] = true; // 返回更新之后的值
        
        $rst = $this->findAndModify($options);
        if (empty($rst['ok'])) {
            throw new \Exception("findandmodify失败");
        }
        
        if (! empty($rst['value'])) {
            return $rst['value'];
        } else {
            throw new \Exception("接受邀请次数增加失败");
        }
    }

    /**
     * 分页读取某个用户的全部邀请函
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @param number $page            
     * @param number $limit            
     * @return array
     */
    public function getListByPage($user_id, $activity_id = '', $page = 1, $limit = 10)
    {
        $sort = array(
            'send_time' => - 1
        );
        $query = array();
        $query['user_id'] = $user_id;
        $query['activity_id'] = $activity_id;
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit);
        return $list;
    }

    /**
     * 是否同一个人领了
     *
     * @param array $info            
     * @param string $user_id            
     * @return boolean
     */
    public function isSame($info, $user_id)
    {
        $isSame = ($info['user_id'] == $user_id) ? true : false;
        return $isSame;
    }

    /**
     * 是否已经领完了
     *
     * @param array $info            
     * @throws Exception
     * @return boolean
     */
    public function isOver($info)
    {
        $isOver = (! empty($info['invited_total']) && $info['invited_num'] >= $info['invited_total']) ? true : false;
        return $isOver;
    }

    /**
     * 获取总价值和总邀请次数
     *
     * @param string $user_id            
     * @param string $activity_id            
     * @return number
     */
    public function getTotalByUserId($user_id, $activity_id = '')
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
                    'user_id' => $user_id,
                    'activity_id' => $activity_id
                )
            ),
            array(
                '$group' => array(
                    '_id' => '$user_id',
                    'totalWorth' => array(
                        '$sum' => '$worth'
                    ),
                    'totalInvitedNum' => array(
                        '$sum' => '$invited_num'
                    )
                )
            )
        ));
        
        if (! empty($rst['result'])) {
            return $rst['result'][0];
        } else {
            return 0;
        }
    }

    /**
     * 更新用户名和頭像
     *
     * @param string $invitationId            
     * @param string $user_name            
     * @param string $user_headimgurl            
     */
    public function updateWeixinUserInfo($invitationId, $user_name, $user_headimgurl = "")
    {
        return $this->update(array(
            '_id' => ($invitationId)
        ), array(
            '$set' => array(
                'user_name' => $user_name,
                'user_headimgurl' => $user_headimgurl
            )
        ));
    }

    public function incWorth($invitation_id, $worth, $worth2 = 0)
    {
        $query = array();
        $query['_id'] = $invitation_id; // 邀请函ID
        $this->update($query, array(
            '$inc' => array(
                'worth' => $worth,
                'worth2' => $worth2
            )
        ));
    }
}