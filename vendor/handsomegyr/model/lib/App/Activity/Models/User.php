<?php
namespace App\Activity\Models;

class User extends \App\Common\Models\Activity\User
{

    /**
     * 根据微信号获取信息
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
            'activity_id' => strval($activity_id)
        );
        if (! empty($otherCondition)) {
            $query = array_merge($query, $otherCondition);
        }
        $info = $this->findOne($query);
        return $info;
    }

    public function getInfoByThirdpartyUser($thirdparty_user, $activity_id = '', array $otherCondition = array())
    {
        $query = array(
            'thirdparty_user' => $thirdparty_user,
            'activity_id' => strval($activity_id)
        );
        if (! empty($otherCondition)) {
            $query = array_merge($query, $otherCondition);
        }
        $info = $this->findOne($query);
        return $info;
    }

    public function getInfoByRedpackUser($redpack_user, $activity_id = '', array $otherCondition = array())
    {
        $query = array(
            'redpack_user' => $redpack_user,
            'activity_id' => strval($activity_id)
        );
        if (! empty($otherCondition)) {
            $query = array_merge($query, $otherCondition);
        }
        $info = $this->findOne($query);
        return $info;
    }

    /**
     * 生成记录
     *
     * @param string $user_id            
     * @param string $nickname            
     * @param string $headimgurl            
     * @param string $redpack_user            
     * @param string $thirdparty_user            
     * @param number $worth            
     * @param number $worth2            
     * @param string $activity_id            
     * @param array $memo            
     * @return array
     */
    public function create($user_id, $nickname, $headimgurl, $redpack_user, $thirdparty_user, $worth = 0, $worth2 = 0, $activity_id, array $memo = array('memo'=>''))
    {
        $data = array();
        $data['activity_id'] = strval($activity_id); // 邀请活动
        $data['user_id'] = $user_id; // 微信ID
        $data['nickname'] = $nickname; // 昵称
        $data['headimgurl'] = $headimgurl; // 头像
        $data['redpack_user'] = $redpack_user; // 国泰微信ID
        $data['thirdparty_user'] = $thirdparty_user; // 第3方账号
        $data['worth'] = intval($worth); // 价值
        $data['worth2'] = intval($worth2); // 价值2
        $data['memo'] = $memo; // 备注
        $info = $this->insert($data);
        return $info;
    }

    /**
     * 根据userid生成或获取记录
     *
     * @param string $user_id            
     * @param string $nickname            
     * @param string $headimgurl            
     * @param string $redpack_user            
     * @param string $thirdparty_user            
     * @param number $worth            
     * @param number $worth2            
     * @param string $activity_id            
     * @param array $memo            
     * @return array
     */
    public function getOrCreateByUserId($user_id, $nickname, $headimgurl, $redpack_user, $thirdparty_user, $worth = 0, $worth2 = 0, $activity_id, array $memo = array())
    {
        $info = $this->getInfoByuserid($user_id, $activity_id);
        if (empty($info)) {
            $info = $this->create($user_id, $nickname, $headimgurl, $redpack_user, $thirdparty_user, $worth, $worth2, $activity_id, $memo);
        }
        return $info;
    }

    /**
     * 增加价值
     *
     * @param mixed $idOrObject            
     * @param int $worth            
     * @param int $worth2            
     * @param array $otherIncData            
     * @param array $otherUpdateData            
     * @throws Exception
     * @return boolean
     */
    public function incWorth($idOrObject, $worth = 0, $worth2 = 0, array $otherIncData = array(), array $otherUpdateData = array())
    {
        if (is_string($idOrObject)) {
            $info = $this->getInfoById($idOrObject);
        } else {
            $info = $idOrObject;
        }
        if (empty($info)) {
            throw new Exception("记录不存在");
        }
        $query = array(
            '_id' => $info['_id']
        );
        
        $options = array();
        $options['query'] = $query;
        
        $update = array(
            '$inc' => array(
                'worth' => $worth
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
            throw new Exception("findandmodify失败");
        }
        
        if (! empty($rst['value'])) {
            return $rst['value'];
        } else {
            throw new Exception("价值增加失败");
        }
    }
}