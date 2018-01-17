<?php
namespace App\Live\Controllers;

class RoomController extends ControllerBase
{

    private $_cookie_expire = 259200;

    private $modelRoom = null;

    private $modelUser = null;

    private $modelAuchor = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelRoom = new \App\Live\Models\Room();
        $this->modelUser = new \App\Live\Models\User();
        $this->modelAuchor = new \App\Live\Models\Auchor();
    }

    /**
     * 房间登陆的接口
     */
    public function loginAction()
    {
        // http://www.jizigou.com/live/room/login?room_id=xxx&openid=xxx&nickname=xxx&headimgurl=xxx&authtype=anonymous,weixin,weibo&source=anonymous,weixin&channel=anonymous,weixin
        try {
            $room_id = trim($this->get('room_id', ''));
            $authtype = trim($this->get('authtype', 'anonymous'));
            $source = trim($this->get('source', 'anonymous'));
            $channel = trim($this->get('channel', 'anonymous'));
            
            $is_superman = intval($this->get('is_superman', '0'));
            
            if (empty($room_id)) {
                echo $this->error(- 1, "房间ID为空");
                return false;
            }
            if (empty($authType)) {
                echo $this->error(- 2, "授权方式错误");
                return false;
            }
            
            $roomInfo = $this->modelRoom->getInfoById($room_id);
            if (empty($roomInfo)) {
                echo ($this->error(- 3, 'room_id不正确'));
                return false;
            }
            // 超级权限直接进入逻辑
            $is_virtual = ! empty($is_superman) ? true : false;
            $roomInfo = $this->modelRoom->getState($roomInfo, $is_virtual);
            
            if ($authtype == 'anonymous') {
                // 匿名用户登录
            } elseif ($authtype == 'weixin') {
                // 微信授权用户登录
                $user_id = trim($this->get('FromUserName', ''));
                $nickname = trim($this->get('nickname', ''));
                $headimgurl = trim($this->get('headimgurl', ''));
                $unionid = trim($this->get('unionid', ''));
                
                if (empty($user_id)) {
                    echo $this->error(- 4, "用户ID为空");
                    return false;
                }
                
                // 检查是否锁定，如果没有锁定加锁
                $key = cacheKey(__FILE__, __CLASS__, __METHOD__, $user_id);
                $objLock = new \iLock($key);
                if ($objLock->lock()) {
                    echo $this->error(- 888, "上次操作还未完成,请等待");
                    return false;
                }
                
                $userInfo = $this->weixinlogin($user_id, $nickname, $headimgurl, $union_id, $room_id, $authtype, $source, $channel);
            } elseif ($authtype == 'login') {
                // 登陆方式
            } else {
                echo $this->error(- 4, "授权方式错误");
                return false;
            }
            
            // 检查用户信息
            if (empty($userInfo)) {
                echo $this->error(- 5, '用户不存在');
                return false;
            }
            
            // 返回
            $ret = array();
            $ret['userInfo'] = $userInfo;
            $ret['roomInfo'] = $roomInfo;
            echo ($this->result("OK", $ret));
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 微信登陆方式
     */
    protected function weixinlogin($user_id, $nickname, $headimgurl, $union_id, $room_id, $authtype, $source, $channel)
    {
        // 查找该用户信息
        $userInfo = $this->modelUser->getInfoByUserId($user_id);
        // 如果不存在
        if (empty($userInfo)) {
            $memo = array(
                'first_room_id' => $room_id
            );
            $is_auchor = false;
            $is_vip = false;
            $is_test = false;
            // 如果没有昵称就用一个默认微信昵称
            if (strlen(trim($nickname)) === 0) {
                $nickname = "微信访客" . uniqid();
            }
            // 如果没有头像就用一个默认微信头像
            if (empty($headimgurl)) {
                $headimgurl = "";
            }
            $userInfo = $this->modelUser->create($user_id, $nickname, $headimgurl, '', $union_id, 0, 0, $room_id, $authtype, $source, $channel, $is_auchor, $is_vip, $is_test, $memo);
        } else {
            // 如果没有昵称就沿用原来的微信昵称
            if (strlen(trim($nickname)) === 0) {
                $nickname = $userInfo['nickname'];
            }
            // 如果没有头像就沿用原来的微信头像
            if (empty($headimgurl)) {
                $headimgurl = $userInfo['headimgurl'];
            }
            
            $otherUpdateData = array(
                'nickname' => $nickname,
                'headimgurl' => $headimgurl,
                'room_id' => $room_id,
                'authtype' => $authtype,
                'source' => $source,
                'channel' => $channel
            );
            
            $userInfo = $this->modelUser->incWorth($userInfo, 0, 0 0, array(), $otherUpdateData);
        }
        // 记录用户数据到REDIS里
        $this->modelUser->saveInfoInRedis($userInfo);
        // 生成cookie
        setcookie('live_user_id' . $authtype, $userInfo['_id'], time() + $this->_cookie_expire, '/');
        
        return $userInfo;
    }
}