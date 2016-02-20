<?php
namespace Webcms\Weixin\Models;

use Weixin\Client;

class User extends \Webcms\Common\Models\Weixin\User
{

    private $_weixin;

    public function setWeixinInstance(Client $weixin)
    {
        $this->_weixin = $weixin;
    }

    /**
     * 检测用户是否授权过
     *
     * @param string $openid            
     * @return boolean
     */
    public function checkOpenId($openid)
    {
        $rst = $this->findOne(array(
            'openid' => $openid
        ));
        if ($rst == null) {
            return false;
        }
        return true;
    }

    /**
     * 获取用户信息
     *
     * @param string $openid            
     */
    public function getUserInfoById($openid)
    {
        return $this->findOne(array(
            'openid' => $openid
        ));
    }

    /**
     * 根据用户的互动行为，通过服务器端token获取该用户的个人信息
     * openid不存在或者随机100次执行一次更新用户信息
     */
    public function updateUserInfoByAction($openid)
    {
        $check = $this->findOne(array(
            'openid' => $openid
        ));
        
        $range = (rand(0, 100) === 1);
        if ($check == null || empty($check['subscribe']) || $range) {
            $userInfo = $this->_weixin->getUserManager()->getUserInfo($openid);
            if (! isset($userInfo['errcode'])) {
                $userInfo['subscribe'] = $userInfo['subscribe'] == 1 ? true : false;
                if (! empty($userInfo['subscribe'])) {
                    $userInfo['subscribe_time'] = getCurrentTime($userInfo['subscribe_time']);
                }
            } elseif (! $range) {
                // 针对订阅号的情况，记录关注用户的openid
                $userInfo = array();
                $userInfo['subscribe'] = true;
                $userInfo['subscribe_time'] = getCurrentTime();
            } else {
                return false;
            }
            
            if (! empty($check)) {
                return $this->update(array(
                    'openid' => $openid
                ), array(
                    '$set' => $userInfo
                ), array(
                    'upsert' => true
                ));
            } else {
                return $this->insert($userInfo);
            }
        }
        return false;
    }

    /**
     * 通过活动授权更新微信用户个人信息
     *
     * @param string $openid            
     * @param array $userInfo            
     */
    public function updateUserInfoBySns($openid, $userInfo)
    {
        $userInfo['access_token'] = isset($_SESSION['iWeixin']['accessToken']) ? $_SESSION['iWeixin']['accessToken'] : false;
        $check = $this->checkOpenId($openid);
        if ($check) {
            return $this->update(array(
                'openid' => $openid
            ), array(
                '$set' => $userInfo
            ), array(
                'upsert' => true
            ));
        } else {
            return $this->insert($userInfo);
        }
    }

    /**
     * 获取用户信息 最新有效的
     *
     * @param string $openid            
     */
    public function getUserInfoByIdLastWeek($openid)
    {
        return $this->findOne(array(
            'openid' => $openid,
            '__MODIFY_TIME__' => array(
                '$gt' => getCurrentTime(time() - 7 * 86400)
            )
        ));
    }
}