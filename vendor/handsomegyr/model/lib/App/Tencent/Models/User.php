<?php
namespace App\Tencent\Models;

class User extends \App\Common\Models\Tencent\User
{

    /**
     * 获取用户信息
     *
     * @param string $openid            
     */
    public function getUserInfoByOpenid($openid)
    {
        return $this->findOne(array(
            'openid' => $openid
        ));
    }

    /**
     * 通过活动授权更新微信用户个人信息
     *
     * @param string $openid            
     * @param array $userInfo            
     */
    public function updateUserInfoBySns($openid, $userInfo)
    {
        $check = $this->checkOpenId($openid);
        $data = $this->getPrepareData($userInfo);
        if ($check) {
            return $this->update(array(
                'openid' => $openid
            ), array(
                '$set' => $data
            ));
        } else {
            return $this->insert($data);
        }
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

    private function getPrepareData($userInfo)
    {
        $data = array();
        // 'ret' => int 0
        // 'msg' => string '' (length=0)
        // 'is_lost' => int 0
        // 'nickname' => string '郭永荣' (length=9)
        // 'gender' => string '男' (length=3)
        // 'province' => string '上海' (length=6)
        // 'city' => string '杨浦' (length=6)
        // 'year' => string '1979' (length=4)
        // 'figureurl' => string 'http://qzapp.qlogo.cn/qzapp/101327614/6E140DB768432C8FABB9C9D840CB7493/30' (length=73)
        // 'figureurl_1' => string 'http://qzapp.qlogo.cn/qzapp/101327614/6E140DB768432C8FABB9C9D840CB7493/50' (length=73)
        // 'figureurl_2' => string 'http://qzapp.qlogo.cn/qzapp/101327614/6E140DB768432C8FABB9C9D840CB7493/100' (length=74)
        // 'figureurl_qq_1' => string 'http://q.qlogo.cn/qqapp/101327614/6E140DB768432C8FABB9C9D840CB7493/40' (length=69)
        // 'figureurl_qq_2' => string 'http://q.qlogo.cn/qqapp/101327614/6E140DB768432C8FABB9C9D840CB7493/100' (length=70)
        // 'is_yellow_vip' => string '0' (length=1)
        // 'vip' => string '0' (length=1)
        // 'yellow_vip_level' => string '0' (length=1)
        // 'level' => string '0' (length=1)
        // 'is_yellow_year_vip' => string '0' (length=1)
        $data['openid'] = isset($userInfo['openid']) ? $userInfo['openid'] : '';
        $data['nickname'] = isset($userInfo['nickname']) ? $userInfo['nickname'] : '';
        $data['headimgurl'] = isset($userInfo['headimgurl']) ? $userInfo['headimgurl'] : '';
        $data['gender'] = isset($userInfo['gender']) ? $userInfo['gender'] : '';
        $data['province'] = isset($userInfo['province']) ? $userInfo['province'] : '';
        $data['city'] = isset($userInfo['city']) ? $userInfo['city'] : '';
        $data['year'] = isset($userInfo['year']) ? $userInfo['year'] : 0;
        $data['is_yellow_vip'] = isset($userInfo['is_yellow_vip']) ? $userInfo['is_yellow_vip'] : 0;
        $data['vip'] = isset($userInfo['vip']) ? $userInfo['vip'] : 0;
        $data['yellow_vip_level'] = isset($userInfo['yellow_vip_level']) ? $userInfo['yellow_vip_level'] : 0;
        $data['level'] = isset($userInfo['level']) ? $userInfo['level'] : 0;
        $data['is_yellow_year_vip'] = isset($userInfo['is_yellow_year_vip']) ? $userInfo['is_yellow_year_vip'] : 0;
        return $data;
    }
}
