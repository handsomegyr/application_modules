<?php
namespace App\Tencent\Models;

class User extends \App\Common\Models\Tencent\User
{

    /**
     * 获取用户信息
     *
     * @param string $user_id            
     */
    public function getUserInfoByUserId($user_id)
    {
        return $this->findOne(array(
            'user_id' => $user_id
        ));
    }

    /**
     * 通过授权更新微信用户个人信息
     *
     * @param string $user_id            
     * @param array $userInfo            
     */
    public function updateUserInfo($user_id, $userInfo)
    {
        if (empty($userInfo['access_token'])) {
            $userInfo['access_token'] = isset($_SESSION['iTencent']['accessToken']) ? $_SESSION['iTencent']['accessToken'] : false;
        }
        
        return $this->update(array(
            'user_id' => $user_id
        ), array(
            '$set' => $userInfo
        ), array(
            'upsert' => true
        ));
    }
}
