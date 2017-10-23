<?php
namespace App\Alipay\Models;

class User extends \App\Common\Models\Alipay\User
{

    /**
     * 检测用户是否授权过
     *
     * @param string $user_id            
     * @return boolean
     */
    public function checkUserId($user_id)
    {
        $rst = $this->findOne(array(
            'user_id' => $user_id
        ));
        if ($rst == null) {
            return false;
        }
        return true;
    }

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
     * 通过活动授权更新微信用户个人信息
     *
     * @param string $user_id            
     * @param array $userInfo            
     */
    public function updateUserInfoBySns($user_id, $userInfo)
    {
        $check = $this->checkUserId($user_id);
        $data = $this->getPrepareData($userInfo);
        if ($check) {
            return $this->update(array(
                'user_id' => $user_id
            ), array(
                '$set' => $data
            ));
        } else {
            return $this->insert($data);
        }
    }

    /**
     * 获取用户信息 最新有效的
     *
     * @param string $user_id            
     */
    public function getUserInfoByIdLastWeek($user_id)
    {
        return $this->findOne(array(
            'user_id' => $user_id,
            '__MODIFY_TIME__' => array(
                '$gt' => getCurrentTime(time() - 7 * 86400)
            )
        ));
    }

    private function getPrepareData($userInfo)
    {
        $data = array();
        $data['app_id'] = isset($userInfo['app_id']) ? $userInfo['app_id'] : '';
        $data['user_id'] = isset($userInfo['user_id']) ? $userInfo['user_id'] : '';
        $data['nick_name'] = isset($userInfo['nick_name']) ? $userInfo['nick_name'] : '';
        $data['avatar'] = isset($userInfo['avatar']) ? $userInfo['avatar'] : '';
        $data['province'] = isset($userInfo['province']) ? $userInfo['province'] : '';
        $data['city'] = isset($userInfo['city']) ? $userInfo['city'] : '';
        $data['is_student_certified'] = isset($userInfo['is_student_certified']) ? $userInfo['is_student_certified'] : '';
        $data['user_type'] = isset($userInfo['user_type']) ? $userInfo['user_type'] : '';
        $data['user_status'] = isset($userInfo['user_status']) ? $userInfo['user_status'] : '';
        $data['is_certified'] = isset($userInfo['is_certified']) ? $userInfo['is_certified'] : '';
        $data['gender'] = isset($userInfo['groupid']) ? $userInfo['groupid'] : '';
        $data['access_token'] = isset($userInfo['access_token']) ? $userInfo['access_token'] : '';
        
        return $data;
    }
}