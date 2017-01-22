<?php
namespace App\Weixincard\Models;

class CardBag extends \App\Common\Models\Weixincard\CardBag
{

    /**
     * 默认排序
     */
    public function getDefaultSort()
    {
        $sort = array(
            'CreateTime' => - 1,
            '_id' => - 1
        );
        return $sort;
    }

    /**
     * 默认查询条件
     */
    public function getQuery()
    {
        $query = array(
            'is_deleted' => false
        );
        return $query;
    }

    /**
     * 根据ID获取信息
     *
     * @param string $id            
     * @return array
     */
    public function getInfoById($id)
    {
        $query = array(
            '_id' => myMongoId($id)
        );
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 根据唯一查询条件获取信息
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @return array
     */
    public function getInfoByUnique($card_id, $UserCardCode)
    {
        $query = $this->queryUnique($card_id, $UserCardCode);
        $cardbagInfo = $this->findOne($query);
        return $cardbagInfo;
    }

    /**
     * 根据卡面号(卡券码)获取信息
     *
     * @param string $CardFaceNo            
     * @return array
     */
    public function getInfoByCardFaceNo($CardFaceNo)
    {
        $query = array(
            'memo.CardFaceNo' => $CardFaceNo
        );
        $cardbagInfo = $this->findOne($query);
        return $cardbagInfo;
    }

    /**
     * 创建
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param string $FromUserName            
     * @param number $OuterId            
     * @param array $memo            
     */
    public function addCard($card_id, $UserCardCode, $FromUserName, $OuterId = 0, array $memo = array('memo'=>''))
    {
        if (empty($memo)) {
            $memo = array(
                'memo' => ''
            );
        }
        $info = array();
        
        $info['card_id'] = (string) $card_id;
        $info['UserCardCode'] = (string) $UserCardCode;
        $info['FromUserName'] = (string) $FromUserName;
        
        $info['is_got'] = false;
        $info['got_time'] = new \MongoDate(0);
        $info['OuterId'] = intval($OuterId);
        $info['IsGiveByFriend'] = false;
        $info['FriendUserName'] = "";
        
        $info['encrypt_code'] = "";
        $info['new_code'] = "";
        
        $info['is_consumed'] = false;
        $info['consume_time'] = new \MongoDate(0);
        $info['consume_openid'] = "";
        
        $info['is_deleted'] = false;
        $info['delete_time'] = new \MongoDate(0);
        
        $info['is_unavailable'] = false;
        $info['unavailable_time'] = new \MongoDate(0);
        
        $info['memo'] = $memo;
        
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
     * @param array $memo            
     */
    public function userGetCard($card_id, $UserCardCode, $FromUserName, $CreateTime, $IsGiveByFriend, $FriendUserName, $OuterId = 0, array $memo = array('memo'=>''))
    {
        $query = $this->queryUnique($card_id, $UserCardCode);
        $cardbagInfo = $this->findOne($query);
        if ($cardbagInfo != null) {
            
            $modelCardUser = new WeixinCard_Model_CardUser();
            $modelCard = new WeixinCard_Model_Card();
            
            $info = array();
            $info['FromUserName'] = (string) $FromUserName;
            $info['is_got'] = true;
            $info['got_time'] = new \MongoDate(intval($CreateTime));
            $info['IsGiveByFriend'] = (intval($IsGiveByFriend) === 1) ? true : false;
            $info['FriendUserName'] = (string) $FriendUserName;
            $info['OuterId'] = intval($OuterId);
            
            if (empty($memo)) {
                $memo = array(
                    'memo' => ''
                );
            }
            foreach ($memo as $key => $value) {
                $info['memo.' . $key] = $value;
            }
            // 当用二维码扫描领取卡券的时候,预先是不知道领取用户是谁的
            // 所以如果在这种情况下,先生成一个卡券用户信息
            if (empty($cardbagInfo['memo']['user_info'])) {
                $userMemo = array(
                    'memo' => ''
                );
                $userInfo = $modelCardUser->getOrCreateByOpenid($FromUserName, "", "", 0, 0, $userMemo);
                $user_info = array(
                    'user_record_id' => myMongoId($userInfo['_id']),
                    'info' => $userInfo
                );
                $info['memo.user_info'] = $user_info;
            }
            
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
                throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包更新失败" . json_encode($rst));
            }
            if (empty($rst['value'])) {
                throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包更新失败" . json_encode($rst));
            }
            
            $newCardBag = $rst['value'];
            $card_record_id = $newCardBag['memo']['card_record_id'];
            // 增加该卡券的领取数量
            $modelCard->incReceivedNum($card_record_id, 1);
            
            $user_record_id = $newCardBag['memo']['user_info']['user_record_id'];
            // 增加用户的领取卡券数量
            $modelCardUser->incReceivedNum($user_record_id, $card_id, 1);
            
            // 如果该记录是从卡券平台创建的话,那么也要更新卡券平台对应的数据信息
            if (! empty($rst['value']['memo']['cardbag_id']) && class_exists('Card_Model_CardBag')) {
                $modelCardBag = new Card_Model_CardBag();
                $newBagInfo = $rst['value'];
                $cardBagMemo = array();
                $cardBagMemo['IsGiveByFriend'] = $newBagInfo['IsGiveByFriend'];
                $cardBagMemo['FriendUserName'] = $newBagInfo['FriendUserName'];
                $modelCardBag->userGetCard($newBagInfo['memo']['cardbag_id'], $newBagInfo['FromUserName'], $newBagInfo['got_time'], $newBagInfo['OuterId'], $cardBagMemo);
            }
            return $rst['value'];
        } else {
            // throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包记录不存在");
        }
    }

    /**
     * 删除卡券
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param array $memo            
     */
    public function userDelCard($card_id, $UserCardCode, array $memo = array('memo'=>''))
    {
        $query = $this->queryUnique($card_id, $UserCardCode);
        $cardbagInfo = $this->findOne($query);
        if ($cardbagInfo != null) {
            $info = array();
            $info['is_deleted'] = true;
            $info['delete_time'] = new \MongoDate();
            if (empty($memo)) {
                $memo = array(
                    'memo' => ''
                );
            }
            foreach ($memo as $key => $value) {
                $info['memo.' . $key] = $value;
            }
            $query = array();
            $query['_id'] = $cardbagInfo['_id'];
            $query['is_deleted'] = false;
            
            $options = array();
            $options['query'] = $query;
            $options['update'] = array(
                '$set' => $info
            );
            $options['new'] = true;
            $options['upsert'] = true;
            $rst = $this->findAndModify($options);
            if (empty($rst['ok'])) {
                throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包删除失败" . json_encode($rst));
            }
            if (empty($rst['value'])) {
                throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包删除失败" . json_encode($rst));
            }
            
            // 如果该记录是从卡券平台创建的话,那么也要更新卡券平台对应的数据信息
            if (! empty($rst['value']['memo']['cardbag_id']) && class_exists('Card_Model_CardBag')) {
                $modelCardBag = new Card_Model_CardBag();
                $newBagInfo = $rst['value'];
                $cardBagMemo = array();
                $modelCardBag->userDelCard($newBagInfo['memo']['cardbag_id'], $newBagInfo['delete_time'], $cardBagMemo);
            }
            
            return $rst['value'];
        } else {
            // throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包记录不存在");
        }
    }

    /**
     * 卡券核销
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param string $consume_openid            
     * @param array $memo            
     */
    public function userConsumeCard($card_id, $UserCardCode, $consume_openid, array $memo = array('memo'=>''))
    {
        $query = $this->queryUnique($card_id, $UserCardCode);
        $cardbagInfo = $this->findOne($query);
        if ($cardbagInfo != null) {
            
            $modelCardUser = new WeixinCard_Model_CardUser();
            $modelCard = new WeixinCard_Model_Card();
            
            $info = array();
            $info['is_consumed'] = true;
            $info['consume_openid'] = $consume_openid;
            $info['consume_time'] = new \MongoDate();
            if (empty($memo)) {
                $memo = array(
                    'memo' => ''
                );
            }
            foreach ($memo as $key => $value) {
                $info['memo.' . $key] = $value;
            }
            $query = array();
            $query['_id'] = $cardbagInfo['_id'];
            $query['is_consumed'] = false;
            
            $options = array();
            $options['query'] = $query;
            $options['update'] = array(
                '$set' => $info
            );
            $options['new'] = true;
            $options['upsert'] = true;
            $rst = $this->findAndModify($options);
            
            if (empty($rst['ok'])) {
                throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包核销失败" . json_encode($rst));
            }
            if (empty($rst['value'])) {
                throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包核销失败" . json_encode($rst));
            }
            
            $newCardBag = $rst['value'];
            $card_record_id = $newCardBag['memo']['card_record_id'];
            // 增加该卡券的核销数量
            $modelCard->incConsumedNum($card_record_id, 1);
            
            $user_record_id = $newCardBag['memo']['user_info']['user_record_id'];
            // 增加用户的领取卡券数量
            $modelCardUser->incConsumedNum($user_record_id, $card_id, 1);
            
            // 如果该记录是从卡券平台创建的话,那么也要更新卡券平台对应的数据信息
            if (! empty($rst['value']['memo']['cardbag_id']) && class_exists('Card_Model_CardBag')) {
                $modelCardBag = new Card_Model_CardBag();
                $newBagInfo = $rst['value'];
                $cardBagMemo = array();
                $cardBagMemo['consume_openid'] = $newBagInfo['consume_openid'];
                $modelCardBag->userConsumeCard($newBagInfo['memo']['cardbag_id'], $newBagInfo['consume_time'], $cardBagMemo);
            }
            
            return $rst['value'];
        } else {
            // throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包记录不存在");
        }
    }

    /**
     * 设置卡券失效
     *
     * @param string $card_id            
     * @param string $UserCardCode            
     * @param array $memo            
     */
    public function unavailableCard($card_id, $UserCardCode, array $memo = array('memo'=>''))
    {
        $query = $this->queryUnique($card_id, $UserCardCode);
        $cardbagInfo = $this->findOne($query);
        if ($cardbagInfo != null) {
            $info = array();
            $info['is_unavailable'] = true;
            $info['unavailable_time'] = new MongoDate();
            if (empty($memo)) {
                $memo = array(
                    'memo' => ''
                );
            }
            foreach ($memo as $key => $value) {
                $info['memo.' . $key] = $value;
            }
            $query = array();
            $query['_id'] = $cardbagInfo['_id'];
            $query['is_unavailable'] = false;
            
            $options = array();
            $options['query'] = $query;
            $options['update'] = array(
                '$set' => $info
            );
            $options['new'] = true;
            $options['upsert'] = true;
            $rst = $this->findAndModify($options);
            
            if (empty($rst['ok'])) {
                throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包设置卡券失效操作失败" . json_encode($rst));
            }
            if (empty($rst['value'])) {
                throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包设置卡券失效操作失败" . json_encode($rst));
            }
            return $rst['value'];
        } else {
            // throw new Exception("卡券ID:{$card_id}Code:{$UserCardCode}的对应卡包记录不存在");
        }
    }

    /**
     * 获取唯一查询条件
     */
    private function queryUnique($card_id, $UserCardCode)
    {
        $query = array();
        $query['UserCardCode'] = (string) $UserCardCode;
        if (! empty($card_id)) {
            $query['card_id'] = (string) $card_id;
        }
        return $query;
    }

    /**
     * 格式化信息
     *
     * @param array $info            
     * @return array
     */
    private function formatInfo($info)
    {
        $rst = array();
        foreach ($this->_keys as $key) {
            if (isset($info[$key])) {
                $rst[$key] = $info[$key];
            }
        }
        return $rst;
    }

    private $_keys = array(
        'card_id',
        'UserCardCode',
        'FromUserName',
        'is_got',
        'got_time',
        'OuterId',
        'IsGiveByFriend',
        'FriendUserName',
        'encrypt_code',
        'new_code',
        'is_consumed',
        'consume_openid',
        'consume_time',
        'is_deleted',
        'delete_time',
        'is_unavailable',
        'unavailable_time',
        'memo'
    );
}