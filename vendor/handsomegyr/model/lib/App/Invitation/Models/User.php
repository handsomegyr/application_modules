<?php
namespace App\Invitation\Models;

class User extends \App\Common\Models\Invitation\User
{

    private $isExclusive = false;

    /**
     * 设置排他
     *
     * @param unknown $isExclusive            
     */
    public function setIsExclusive($isExclusive)
    {
        $this->isExclusive = $isExclusive;
    }

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
            'activity_id' => $activity_id
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
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param number $worth            
     * @param number $worth2            
     * @param string $activity_id            
     * @param array $memo            
     * @return array
     */
    public function create($user_id, $user_name, $user_headimgurl, $worth = 0, $worth2 = 0, $activity_id = 0, array $memo = array('memo'=>''))
    {
        $data = array();
        $data['activity_id'] = $activity_id; // 邀请活动
        $data['user_id'] = $user_id; // 微信ID
        $data['user_name'] = $user_name; // 用户名
        $data['user_headimgurl'] = $user_headimgurl; // 头像
        $data['worth'] = $worth; // 价值
        $data['worth2'] = $worth2; // 价值2
        $data['lock'] = false; // 未锁定
        $data['expire'] = getCurrentTime(); // 过期时间
        $data['memo'] = $memo; // 备注
        $data['log_time'] = getCurrentTime(); // 时间
        $info = $this->insert($data);
        return $info;
    }

    /**
     * 根据user_id生成或获取记录
     *
     * @param string $user_id            
     * @param string $user_name            
     * @param string $user_headimgurl            
     * @param number $worth            
     * @param number $worth2            
     * @param string $activity_id            
     * @param array $memo            
     * @return array
     */
    public function getOrCreateByUserId($user_id, $user_name, $user_headimgurl, $worth = 0, $worth2 = 0, $activity_id = 0, array $memo = array())
    {
        $info = $this->getInfoByUserId($user_id, $activity_id);
        if (empty($info)) {
            $info = $this->create($user_id, $user_name, $user_headimgurl, $worth, $worth2, $activity_id, $memo);
        }
        return $info;
    }

    /**
     * 加锁
     *
     * @param string $id            
     * @param boolean $isExclusive            
     * @throws Exception
     * @return boolean
     */
    public function lock($id)
    {
        if (! $this->isExclusive) { // 非排他
            return false;
        }
        // 锁定之前，先清除过期锁
        $this->expire($id);
        
        // 查找当前用户的锁
        $lock = $this->findOne(array(
            '_id' => ($id)
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
     * @param string $id            
     */
    public function unlock($id)
    {
        if (! $this->isExclusive) { // 非排他
            return;
        }
        return $this->update(array(
            '_id' => ($id)
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
     * @param string $id            
     */
    public function expire($id)
    {
        return $this->update(array(
            '_id' => ($id),
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
     * 增加价值
     *
     * @param mixed $idOrObject            
     * @param int $worth            
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
            throw new \Exception("记录不存在");
        }
        $query = array(
            '_id' => $info['_id']
        );
        
        if ($this->isExclusive) { // 排他
            $query['lock'] = true;
        }
        
        $options = array();
        $options['query'] = $query;
        
        $update = array(
            '$inc' => array(
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
            throw new \Exception("价值增加失败");
        }
    }
}