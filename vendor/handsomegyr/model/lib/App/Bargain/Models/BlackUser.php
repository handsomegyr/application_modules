<?php
namespace App\Bargain\Models;

class BlackUser extends \App\Common\Models\Bargain\BlackUser
{

    /**
     * 根据user_id获取信息
     *
     * @param string $user_id            
     * @return array
     */
    public function getInfoByUserId($user_id)
    {
        $query = array(
            'user_id' => $user_id
        );
        $info = $this->findOne($query);
        return $info;
    }
}
