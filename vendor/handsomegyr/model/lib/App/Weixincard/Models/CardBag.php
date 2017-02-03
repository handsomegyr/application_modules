<?php
namespace App\Weixincard\Models;

class CardBag extends \App\Common\Models\Weixincard\CardBag
{

    /**
     * 根据唯一查询条件获取信息
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param string $FromUserName            
     * @return array
     */
    public function getInfoByUnique($card_id, $UserCardCode, $FromUserName)
    {
        $query = $this->queryUnique($card_id, $UserCardCode, $FromUserName);
        $cardbagInfo = $this->findOne($query);
        return $cardbagInfo;
    }

    /**
     * 创建
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param string $FromUserName            
     * @param number $IsGiveByFriend            
     * @param string $FriendUserName            
     * @param number $OuterId            
     * @param string $OldUserCardCode            
     * @param number $IsRestoreMemberCard            
     * @param number $IsRecommendByFriend            
     * @param string $SourceScene            
     * @param string $encrypt_code            
     * @param string $new_code            
     * @param number $is_got            
     * @param number $got_time            
     * @param number $is_consumed            
     * @param number $consume_time            
     * @param string $StaffOpenId            
     * @param string $ConsumeSource            
     * @param string $LocationId            
     * @param string $LocationName            
     * @param number $is_deleted            
     * @param number $delete_time            
     * @param number $is_unavailable            
     * @param number $unavailable_time            
     * @param number $is_give_to_friend            
     * @param number $give_to_friend_time            
     * @param string $friend_card_bag_id            
     * @param array $memo            
     */
    public function addCard($card_id, $UserCardCode, $FromUserName, $IsGiveByFriend, $FriendUserName, $OuterId, $OldUserCardCode, $IsRestoreMemberCard, $IsRecommendByFriend, $SourceScene, $encrypt_code, $new_code, $is_got, $got_time, $is_consumed, $consume_time, $StaffOpenId, $ConsumeSource, $LocationId, $LocationName, $is_deleted, $delete_time, $is_unavailable, $unavailable_time, $is_give_to_friend, $give_to_friend_time, $friend_card_bag_id, array $memo = array('memo'=>''))
    {
        $info = array();
        $info['card_id'] = (string) $card_id;
        $info['UserCardCode'] = (string) $UserCardCode;
        $info['FromUserName'] = (string) $FromUserName;
        $info['IsGiveByFriend'] = intval($IsGiveByFriend);
        $info['FriendUserName'] = (string) $FriendUserName;
        $info['OuterId'] = intval($OuterId);
        $info['OldUserCardCode'] = (string) $OldUserCardCode;
        $info['IsRestoreMemberCard'] = intval($IsRestoreMemberCard);
        $info['IsRecommendByFriend'] = intval($IsRecommendByFriend);
        $info['SourceScene'] = (string) $SourceScene;
        $info['encrypt_code'] = (string) $encrypt_code;
        $info['new_code'] = (string) $new_code;
        
        $info['is_got'] = intval($is_got);
        $info['got_time'] = new \MongoDate(intval($got_time));
        
        $info['is_consumed'] = intval($is_consumed);
        $info['consume_time'] = new \MongoDate(intval($consume_time));
        
        $info['StaffOpenId'] = $StaffOpenId;
        $info['ConsumeSource'] = $ConsumeSource;
        $info['LocationId'] = $LocationId;
        $info['LocationName'] = $LocationName;
        
        $info['is_deleted'] = intval($is_deleted);
        $info['delete_time'] = new \MongoDate(intval($delete_time));
        
        $info['is_unavailable'] = intval($is_unavailable);
        $info['unavailable_time'] = new \MongoDate(intval($unavailable_time));
        
        $info['is_give_to_friend'] = $is_give_to_friend;
        $info['give_to_friend_time'] = new \MongoDate(intval($give_to_friend_time));
        $info['friend_card_bag_id'] = $friend_card_bag_id;
        
        $info['memo'] = array(
            'get' => $memo
        );
        return $this->insert($info);
    }

    /**
     * 领取卡券
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param string $FromUserName            
     * @param number $CreateTime            
     * @param number $IsGiveByFriend            
     * @param string $FriendUserName            
     * @param number $OuterId            
     * @param string $OldUserCardCode            
     * @param number $IsRestoreMemberCard            
     * @param number $IsRecommendByFriend            
     * @param string $SourceScene            
     * @param string $encrypt_code            
     * @param string $new_code            
     * @param array $memo            
     */
    public function userGetCard($card_id, $UserCardCode, $FromUserName, $CreateTime, $IsGiveByFriend, $FriendUserName, $OuterId, $OldUserCardCode, $IsRestoreMemberCard, $IsRecommendByFriend, $SourceScene, $encrypt_code, $new_code, array $memo = array('memo'=>''))
    {
        $query = $this->queryUnique($card_id, $UserCardCode, $FromUserName);
        $cardbagInfo = $this->findOne($query);
        
        $is_got = 1;
        $got_time = intval($CreateTime);
        
        // 如果已经存在的话，更新處理
        if (! empty($cardbagInfo)) {
            
            $info = array();
            $info['IsGiveByFriend'] = intval($IsGiveByFriend);
            $info['FriendUserName'] = (string) $FriendUserName;
            $info['OuterId'] = intval($OuterId);
            $info['OldUserCardCode'] = (string) $OldUserCardCode;
            $info['IsRestoreMemberCard'] = intval($IsRestoreMemberCard);
            $info['IsRecommendByFriend'] = intval($IsRecommendByFriend);
            $info['SourceScene'] = (string) $SourceScene;
            
            $info['is_got'] = $is_got;
            $info['got_time'] = new \MongoDate($got_time);
            
            $info['memo'] = array_merge($cardbagInfo['memo'], array(
                'get' => $memo
            ));
            
            print_r($info);
            die('xxx222');
            
            $query = array();
            $query['_id'] = $cardbagInfo['_id'];
            $query['is_got'] = false;
            
            $options = array();
            $options['query'] = $query;
            $options['update'] = array(
                '$set' => $info
            );
            $options['new'] = true;
            $options['upsert'] = true;
            $rst = $this->findAndModify($options);
            if (empty($rst['ok'])) {
                throw new Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包更新失败" . json_encode($rst));
            }
            if (empty($rst['value'])) {
                throw new Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包更新失败" . json_encode($rst));
            }
            return $rst['value'];
        } else { // 新增一条记录
            $is_consumed = 0;
            $consume_time = 0;
            
            $StaffOpenId = '';
            $ConsumeSource = '';
            $LocationId = '';
            $LocationName = '';
            
            $is_deleted = 0;
            $delete_time = 0;
            $is_unavailable = 0;
            $unavailable_time = 0;
            
            $is_give_to_friend = 0;
            $give_to_friend_time = 0;
            $friend_card_bag_id = '';
            
            return $this->addCard($card_id, $UserCardCode, $FromUserName, $IsGiveByFriend, $FriendUserName, $OuterId, $OldUserCardCode, $IsRestoreMemberCard, $IsRecommendByFriend, $SourceScene, $encrypt_code, $new_code, $is_got, $got_time, $is_consumed, $consume_time, $StaffOpenId, $ConsumeSource, $LocationId, $LocationName, $is_deleted, $delete_time, $is_unavailable, $unavailable_time, $is_give_to_friend, $give_to_friend_time, $friend_card_bag_id, $memo);
        }
    }

    /**
     * 将卡券赠送给朋友的处理
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param string $FromUserName            
     * @param number $CreateTime            
     * @param string $friend_card_bag_id            
     * @param array $memo            
     */
    public function giveCardToFriend($card_id, $UserCardCode, $FromUserName, $CreateTime, $friend_card_bag_id, array $memo = array('memo'=>''))
    {
        $query = $this->queryUnique($card_id, $UserCardCode, $FromUserName);
        $cardbagInfo = $this->findOne($query);
        
        // 如果已经存在的话，更新處理
        if (! empty($cardbagInfo)) {
            $info = array();
            $info['is_give_to_friend'] = 1;
            $info['give_to_friend_time'] = new \MongoDate(intval($CreateTime));
            $info['friend_card_bag_id'] = $friend_card_bag_id;
            
            $info['memo'] = array_merge($cardbagInfo['memo'], array(
                'give_to_friend' => $memo
            ));
            
            $query = array();
            $query['_id'] = $cardbagInfo['_id'];
            $query['is_give_to_friend'] = 0;
            
            $options = array();
            $options['query'] = $query;
            $options['update'] = array(
                '$set' => $info
            );
            $options['new'] = true;
            $options['upsert'] = true;
            $rst = $this->findAndModify($options);
            if (empty($rst['ok'])) {
                throw new Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包更新失败" . json_encode($rst));
            }
            if (empty($rst['value'])) {
                throw new Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包更新失败" . json_encode($rst));
            }
            return $rst['value'];
        } else {
            throw new \Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包记录不存在");
        }
    }

    /**
     * 卡券核销
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param string $FromUserName            
     * @param number $CreateTime            
     * @param string $ConsumeSource            
     * @param string $StaffOpenId            
     * @param string $LocationId            
     * @param string $LocationName            
     * @param array $memo            
     */
    public function userConsumeCard($card_id, $UserCardCode, $FromUserName, $CreateTime, $ConsumeSource, $StaffOpenId, $LocationId, $LocationName, array $memo = array('memo'=>''))
    {
        $query = $this->queryUnique($card_id, $UserCardCode, $FromUserName);
        $cardbagInfo = $this->findOne($query);
        
        // 如果已经存在的话，更新處理
        if (! empty($cardbagInfo)) {
            
            $info = array();
            $info['is_consumed'] = 1;
            $info['consume_time'] = new \MongoDate(intval($CreateTime));
            
            $info['StaffOpenId'] = $StaffOpenId;
            $info['ConsumeSource'] = $ConsumeSource;
            $info['LocationId'] = $LocationId;
            $info['LocationName'] = $LocationName;
            $info['memo'] = array_merge($cardbagInfo['memo'], array(
                'consume' => $memo
            ));
            
            $query = array();
            $query['_id'] = $cardbagInfo['_id'];
            $query['is_consumed'] = 0;
            
            $options = array();
            $options['query'] = $query;
            $options['update'] = array(
                '$set' => $info
            );
            $options['new'] = true;
            $options['upsert'] = true;
            $rst = $this->findAndModify($options);
            
            if (empty($rst['ok'])) {
                throw new \Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包核销失败" . json_encode($rst));
            }
            if (empty($rst['value'])) {
                throw new \Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包核销失败" . json_encode($rst));
            }
            return $rst['value'];
        } else {
            throw new \Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包记录不存在");
        }
    }

    /**
     * 删除卡券
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param string $FromUserName            
     * @param number $CreateTime            
     * @param array $memo            
     */
    public function userDelCard($card_id, $UserCardCode, $FromUserName, $CreateTime, array $memo = array('memo'=>''))
    {
        $query = $this->queryUnique($card_id, $UserCardCode, $FromUserName);
        $cardbagInfo = $this->findOne($query);
        
        // 如果已经存在的话，更新處理
        if (! empty($cardbagInfo)) {
            $info = array();
            $info['is_deleted'] = 1;
            $info['delete_time'] = new \MongoDate(intval($CreateTime));
            $info['memo'] = array_merge($cardbagInfo['memo'], array(
                'delete' => $memo
            ));
            $query = array();
            $query['_id'] = $cardbagInfo['_id'];
            $query['is_deleted'] = 0;
            
            $options = array();
            $options['query'] = $query;
            $options['update'] = array(
                '$set' => $info
            );
            $options['new'] = true;
            $options['upsert'] = true;
            $rst = $this->findAndModify($options);
            if (empty($rst['ok'])) {
                throw new \Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包删除失败" . json_encode($rst));
            }
            if (empty($rst['value'])) {
                throw new \Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包删除失败" . json_encode($rst));
            }
            return $rst['value'];
        } else {
            throw new \Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包记录不存在");
        }
    }

    /**
     * 设置卡券失效
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param string $FromUserName            
     * @param number $CreateTime            
     * @param array $memo            
     */
    public function unavailableCard($card_id, $UserCardCode, $FromUserName, $CreateTime, array $memo = array('memo'=>''))
    {
        $query = $this->queryUnique($card_id, $UserCardCode, $FromUserName);
        $cardbagInfo = $this->findOne($query);
        
        // 如果已经存在的话，更新處理
        if (! empty($cardbagInfo)) {
            
            $info = array();
            $info['is_unavailable'] = 1;
            $info['unavailable_time'] = new \MongoDate(intval($CreateTime));
            $info['memo'] = array_merge($cardbagInfo['memo'], array(
                'unavailable' => $memo
            ));
            
            $query = array();
            $query['_id'] = $cardbagInfo['_id'];
            $query['is_unavailable'] = 0;
            
            $options = array();
            $options['query'] = $query;
            $options['update'] = array(
                '$set' => $info
            );
            $options['new'] = true;
            $options['upsert'] = true;
            $rst = $this->findAndModify($options);
            
            if (empty($rst['ok'])) {
                throw new \Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包设置卡券失效操作失败" . json_encode($rst));
            }
            if (empty($rst['value'])) {
                throw new \Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包设置卡券失效操作失败" . json_encode($rst));
            }
            return $rst['value'];
        } else {
            throw new \Exception("卡券ID:{$card_id} Code:{$UserCardCode}的对应卡包记录不存在");
        }
    }

    /**
     * 获取唯一查询条件
     */
    private function queryUnique($card_id, $UserCardCode, $FromUserName)
    {
        $query = array();
        $query['card_id'] = (string) $card_id;
        $query['UserCardCode'] = (string) $UserCardCode;
        $query['FromUserName'] = (string) $FromUserName;
        return $query;
    }
}