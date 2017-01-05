<?php
namespace App\Member\Models;

class Member extends \App\Common\Models\Member\Member
{

    /**
     * 根据用户name获得用户信息
     *
     * @param string $name            
     * @return array
     */
    public function getInfoByName($name)
    {
        $result = $this->findOne(array(
            "name" => $name
        ));
        return $result;
    }

    /**
     * 根据用户email获得用户信息
     *
     * @param string $email            
     * @return array
     */
    public function getInfoByEmail($email)
    {
        $result = $this->findOne(array(
            "email" => $email
        ));
        return $result;
    }

    /**
     * 根据用户mobile获得用户信息
     *
     * @param string $mobile            
     * @return array
     */
    public function getInfoByMobile($mobile)
    {
        $result = $this->findOne(array(
            "mobile" => $mobile
        ));
        return $result;
    }

    /**
     * 根据用户QQ账号获得用户信息
     *
     * @param string $qqopenid            
     * @return array
     */
    public function getInfoByQQOpenid($qqopenid)
    {
        $result = $this->findOne(array(
            "qqopenid" => $qqopenid
        ));
        return $result;
    }

    /**
     * 根据用户微信账号获得用户信息
     *
     * @param string $weixinopenid
     * @return array
     */
    public function getInfoByWeixinOpenid($weixinopenid)
    {
        $result = $this->findOne(array(
            "weixinopenid" => $weixinopenid
        ));
        return $result;
    }
    
    /**
     * 注册处理
     *
     * @param string $name            
     * @param string $email            
     * @param string $mobile            
     * @param string $password            
     * @param string $inviter_id            
     * @return array
     */
    public function register($name, $email, $mobile, $password, $inviter_id = '')
    {
        $userData = array();
        if (! empty($name)) {
            $userData['register_by'] = self::REGISTERBY3;
            $userData['name'] = $name;
        } elseif (! empty($email)) {
            $userData['register_by'] = self::REGISTERBY2;
            $userData['email'] = $email;
            $userData['email_bind'] = true;
        } elseif (! empty($mobile)) {
            $userData['register_by'] = self::REGISTERBY1;
            $userData['mobile'] = $mobile;
            $userData['mobile_bind'] = true;
        }
        // 头像
        $userData['avatar'] = "UserFace-160-0000.jpg";
        // 密码
        $userData['passwd'] = md5($password);
        // 邀请人(推荐人)
        $userData['inviter_id'] = $inviter_id;
        // 注册时间
        $userData['reg_time'] = getCurrentTime();
        // 登录时间
        $userData['login_time'] = getCurrentTime();
        // 登录IP
        $userData['login_ip'] = getIp();
        // 是否开启
        $userData['state'] = true;
        // 隐私设定
        $userData['privacy'] = $this->getPrivacyInfo();
        // 常用设置
        $userData['noticesettings'] = $this->getNoticeSettings();
        $memberInfo = $this->insert($userData);
        
        return $memberInfo;
    }

    /**
     * 登录处理
     *
     * @param array $memberInfo
     *            会员信息
     */
    public function login($memberInfo = array(), $isLogin = true)
    {
        // 存入会话
        $this->saveSession($memberInfo);
        
        $query = array(
            '_id' => $memberInfo['_id']
        );
        $data = array(
            '$exp' => array(
                'old_login_time' => 'login_time',
                'old_login_ip' => 'login_ip'
            ),
            '$set' => array(
                'login_time' => getCurrentTime(),
                'login_ip' => getIp()
            ),
            '$inc' => array(
                'login_num' => 1
            )
        );
        return $this->update($query, $data);
    }

    /**
     * 更新用户信息
     *
     * @param string $id            
     * @param array $memberInfo            
     */
    public function updateMemberInfo($id, array $memberInfo)
    {
        $query = array(
            '_id' => $id
        );
        $this->update($query, array(
            '$set' => $memberInfo
        ));
    }

    /**
     * 更新头像
     *
     * @param string $id            
     * @param string $avatar            
     */
    public function updateAvatar($id, $avatar)
    {
        $data = array();
        $data['avatar'] = $avatar;
        $this->updateMemberInfo($id, $data);
        $_SESSION['avatar'] = $avatar;
    }

    /**
     * 更新密码
     *
     * @param string $id            
     * @param string $new_password            
     */
    public function updatePwd($id, $new_password)
    {
        $data = array();
        if (! empty($new_password)) {
            $data['passwd'] = md5($new_password);
        } else {
            $data['passwd'] = '';
        }
        $this->updateMemberInfo($id, $data);
    }

    /**
     * 更新支付密码
     *
     * @param string $id            
     * @param string $new_password            
     */
    public function updatePaypwd($id, $new_password)
    {
        $data = array();
        if (! empty($new_password)) {
            $data['paypwd'] = md5($new_password);
        } else {
            $data['paypwd'] = '';
        }
        $this->updateMemberInfo($id, $data);
    }

    /**
     * 更新会员的开启状态
     *
     * @param string $id            
     * @param boolean $state            
     */
    public function updateState($id, $state)
    {
        $data = array();
        $data['state'] = $state;
        $this->updateMemberInfo($id, $data);
        $_SESSION['state'] = $state;
    }

    /**
     * 重置密码
     *
     * @param string $id            
     */
    public function resetPwd($id, $new_password)
    {
        if (empty($new_password)) {
            $new_password = createRandCode(15);
        }
        $this->updatePwd($id, $new_password);
    }

    public function bindEmail($member_id, $email)
    {
        $data = array(
            'email' => $email,
            'email_bind' => true
        );
        $this->updateMemberInfo($member_id, $data);
    }

    public function bindMobile($member_id, $mobile)
    {
        $data = array(
            'mobile' => $mobile,
            'mobile_bind' => true
        );
        $this->updateMemberInfo($member_id, $data);
    }

    public function bindQQOpenid($member_id, $qqopenid, array $qqinfo)
    {
        $data = array(
            'qqopenid' => $qqopenid,
            'qqinfo' => json_encode($qqinfo)
        );
        $this->updateMemberInfo($member_id, $data);
    }

    public function bindWeixinOpenid($member_id, $weixinopenid, array $weixininfo)
    {
        $data = array(
            'weixinopenid' => $weixinopenid,
            'weixininfo' => json_encode($weixininfo)
        );
        $this->updateMemberInfo($member_id, $data);
    }

    /**
     * 登录保护设置
     *
     * @param string $member_id            
     * @param boolean $is_open            
     */
    public function setLoginTip($member_id, $is_open = true)
    {
        $data = array(
            'is_login_tip' => $is_open
        );
        $this->updateMemberInfo($member_id, $data);
    }

    /**
     * 小额免密码设置
     *
     * @param string $member_id            
     * @param boolean $is_open            
     * @param number $smallmoney            
     */
    public function setSmallMoney($member_id, $is_open = true, $smallmoney = 0)
    {
        $data = array(
            'is_smallmoney_open' => $is_open,
            'smallmoney' => $smallmoney
        );
        $this->updateMemberInfo($member_id, $data);
    }

    public function saveSession(array $memberInfo)
    {
        $_SESSION['is_login'] = true;
        $_SESSION['member_id'] = $memberInfo['_id'];
        $_SESSION['member_name'] = $memberInfo['name'];
        $_SESSION['member_nickname'] = $memberInfo['nickname'];
        $_SESSION['member_email'] = $memberInfo['email'];
        $_SESSION['member_mobile'] = $memberInfo['mobile'];
        $_SESSION['is_buy'] = isset($memberInfo['is_buy']) ? $memberInfo['is_buy'] : 1;
        $_SESSION['avatar'] = $memberInfo['avatar'];
        
        if (! empty($memberInfo['qqopenid'])) {
            $_SESSION['qqopenid'] = $memberInfo['qqopenid'];
        }
        if (! empty($memberInfo['sinaopenid'])) {
            $_SESSION['sinaopenid'] = $memberInfo['sinaopenid'];
        }
    }

    /**
     * 会员登录检查
     *
     * @return boolean
     */
    public function checkloginMember()
    {
        if (empty($_SESSION['member_id'])) {
            return false;
        } else {
            return true;
        }
    }

    public function clearCookies()
    {
        session_unset();
        session_destroy();
    }

    /**
     * 分页获取查找好友的列表
     *
     * @param number $page            
     * @param number $limit            
     * @param array $otherConditions            
     * @param array $sort            
     * @return array
     */
    public function getSearchFriendsList($page = 1, $limit = 5, array $otherConditions = array(), array $sort = array())
    {
        $query = array();
        if (! empty($otherConditions)) {
            $query = array_merge($otherConditions, $query);
        }
        $list = $this->find($query, $sort, ($page - 1) * $limit, $limit, array());
        return $list;
    }

    /**
     * 获取会员登录名称
     *
     * @return string
     */
    public function getLoginName(array $memberInfo = array())
    {
        $loginName = "";
        if (empty($memberInfo)) {
            return $loginName;
        } else {
            if (! empty($memberInfo['nickname'])) {
                $loginName = $memberInfo['nickname'];
            } else {
                $loginName = $this->getRegisterName($memberInfo);
            }
        }
        return $loginName;
    }

    /**
     * 获取会员注册名称
     *
     * @return string
     */
    public function getRegisterName(array $memberInfo, $isHidden = false)
    {
        if ($memberInfo['register_by'] == \App\Member\Models\Member::REGISTERBY1) {
            $registerName = $memberInfo['mobile'];
            if ($isHidden) {
                $registerName = getHiddenMobile($registerName);
            }
        } elseif ($memberInfo['register_by'] == \App\Member\Models\Member::REGISTERBY2) {
            $registerName = $memberInfo['email'];
            if ($isHidden) {
                $registerName = getHiddenEmail($registerName);
            }
        } elseif ($memberInfo['register_by'] == \App\Member\Models\Member::REGISTERBY3) {
            $registerName = $memberInfo['name'];
        }
        return $registerName;
    }

    /**
     * 隐私设定
     * $msgSet = 1; // 私信 1:仅限好友 2 禁止
     * $areaSet = 0; // 地理位置 0:允许 1:禁止
     * $searchSet = 0; // 好友搜索 0:允许 1:禁止
     * $buySet = 0; // 个人主页-云购记录 0:所有人可见 1:好友可见 2:仅自己可见
     * $buyShowNum = 0; // 个人主页-云购记录 显示
     * $rafSet = 0; // 个人主页-获得的商品 0:所有人可见 1:好友可见 2:仅自己可见
     * $rafShowNum = 0; // 个人主页-获得的商品 显示
     * $postSet = 0; // 个人主页-晒单 0:所有人可见 1:好友可见 2:仅自己可见
     * $postShowNum = 0; // 个人主页-晒单 显示
     *
     * @param number $msgSet            
     * @param number $areaSet            
     * @param number $searchSet            
     * @param number $buySet            
     * @param number $buyShowNum            
     * @param number $rafSet            
     * @param number $rafShowNum            
     * @param number $postSet            
     * @param number $postShowNum            
     * @return array
     */
    public function getPrivacyInfo($msgSet = 1, $areaSet = 0, $searchSet = 0, $buySet = 0, $buyShowNum = 0, $rafSet = 0, $rafShowNum = 0, $postSet = 0, $postShowNum = 0)
    {
        $privacy = array();
        $privacy['msgSet'] = intval($msgSet);
        $privacy['areaSet'] = intval($areaSet);
        $privacy['searchSet'] = intval($searchSet);
        $privacy['buySet'] = intval($buySet);
        $privacy['rafSet'] = intval($rafSet);
        $privacy['postSet'] = intval($postSet);
        $privacy['buyShowNum'] = intval($buyShowNum);
        $privacy['rafShowNum'] = intval($rafShowNum);
        $privacy['postShowNum'] = intval($postShowNum);
        return $privacy;
    }

    /**
     * 常用设置
     *
     * @param number $sysMsgSet            
     * @param number $wxMailSet            
     * @return array
     */
    public function getNoticeSettings($sysMsgSet = 0, $wxMailSet = 0)
    {
        // 更新处理
        $noticeset = array();
        $noticeset['sysMsgSet'] = intval($sysMsgSet);
        $noticeset['wxMailSet'] = intval($wxMailSet);
        return $noticeset;
    }

    public function isCanSendPrivMsg($memberInfo, $msgToUID)
    {
        // 私信 1:仅限好友 2 禁止
        if ($memberInfo['_id'] == $msgToUID) {
            return false;
        }
        if ($memberInfo['privacy']['msgSet'] == self::PRIVACY_MSGSET2) {
            return false;
        } elseif ($memberInfo['privacy']['msgSet'] == self::PRIVACY_MSGSET1) {
            // 是否是朋友
            $modelMemberFriend = new Friend();
            $friendInfo = $modelMemberFriend->check($memberInfo['_id'], $msgToUID);
            if (empty($friendInfo)) {
                return false;
            }
        }
        return true;
    }

    public function isCanSeeAreaInfo($memberInfo)
    {
        // 地理位置 0:允许 1:禁止
        if ($memberInfo['privacy']['areaSet'] == self::PRIVACY_AREASET1) {
            return false;
        }
        return true;
    }

    public function isCanFriendSearch($memberInfo)
    {
        // 好友搜索 0:允许 1:禁止
        if ($memberInfo['privacy']['searchSet'] == self::PRIVACY_SEARCHSET1) {
            return false;
        }
        return true;
    }

    public function isCanBuySee($memberInfo, $userID)
    {
        // 个人主页-云购记录 0:所有人可见 1:好友可见 2:仅自己可见
        // 好友可见的时候
        if ($memberInfo['privacy']['buySet'] == self::PRIVACY_BUYSET1) {
            // 检查是否是好友
            $modelMemberFriend = new Friend();
            $friendInfo = $modelMemberFriend->check($memberInfo['_id'], $userID);
            if (empty($friendInfo)) {
                return false;
            }
        } elseif ($memberInfo['privacy']['buySet'] == self::PRIVACY_BUYSET2) {
            if ($memberInfo['_id'] != $userID) {
                return false;
            }
        }
        return true;
    }

    public function isCanRafSee($memberInfo, $userID)
    {
        // 个人主页-获得的商品 0:所有人可见 1:好友可见 2:仅自己可见
        // 好友可见的时候
        if ($memberInfo['privacy']['rafSet'] == self::PRIVACY_RAFSET1) {
            // 检查是否是好友
            $modelMemberFriend = new Friend();
            $friendInfo = $modelMemberFriend->check($memberInfo['_id'], $userID);
            if (empty($friendInfo)) {
                return false;
            }
        } elseif ($memberInfo['privacy']['rafSet'] == self::PRIVACY_RAFSET2) {
            if ($memberInfo['_id'] != $userID) {
                return false;
            }
        }
        return true;
    }

    public function isCanPostSee($memberInfo, $userID)
    {
        // 个人主页-晒单 0:所有人可见 1:好友可见 2:仅自己可见
        // 好友可见的时候
        if ($memberInfo['privacy']['postSet'] == self::PRIVACY_POSTSET1) {
            // 检查是否是好友
            $modelMemberFriend = new Friend();
            $friendInfo = $modelMemberFriend->check($memberInfo['_id'], $userID);
            if (empty($friendInfo)) {
                return false;
            }
        } elseif ($memberInfo['privacy']['postSet'] == self::PRIVACY_POSTSET2) {
            if ($memberInfo['_id'] != $userID) {
                return false;
            }
        }
        return true;
    }

    /**
     * 增加购买次数
     */
    public function incBuyNum($member_id, $buy_num = 1)
    {
        $query = array(
            '_id' => $member_id
        );
        $data = array(
            '$inc' => array(
                'buy_num' => $buy_num
            )
        );
        return $this->update($query, $data);
    }

    /**
     * 增加获取商品次数
     */
    public function incPrizedNum($member_id, $prized_num = 1)
    {
        $query = array(
            '_id' => $member_id
        );
        $data = array(
            '$inc' => array(
                'prized_num' => $prized_num
            )
        );
        return $this->update($query, $data);
    }

    /**
     * 是否需要支付密码
     *
     * @param array $buyerInfo            
     * @param number $pay_amount            
     * @return boolean
     */
    public function isNeedPaypwd(array $buyerInfo, $pay_amount)
    {
        // 检查该会员是否设置了支付密码
        if (empty($buyerInfo['paypwd'])) {
            return false;
        }
        
        // 小额免密码设置 开启后支付金额小于设置额度时，无需输入支付密码。
        if (! empty($buyerInfo['is_smallmoney_open'])) {
            if ($buyerInfo['smallmoney'] >= $pay_amount) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检查支付密码
     *
     * @param array $buyerInfo            
     * @param string $password            
     * @param number $pay_amount            
     * @return boolean
     */
    public function checkPaypwd(array $buyerInfo, $password, $pay_amount)
    {
        // 检查该会员是否需要填写支付密码进行支付
        $isNeed = $this->isNeedPaypwd($buyerInfo, $pay_amount);
        
        if (empty($isNeed)) {
            return true;
        }
        // 检查是否相同
        if ($buyerInfo['paypwd'] == md5($password)) {
            return true;
        }
        
        return false;
    }
}