<?php
namespace App\Points\Views\Helpers;

use App\Points\Models\User;

class PointsUserHelper extends \Phalcon\Tag
{

    /**
     * 获取积分
     *
     * @return array
     */
    static public function getPoints($member_id, $category)
    {
        $modelPointsUser = new User();
        $info = $modelPointsUser->getInfoByUserId($member_id, $category);
        if (empty($info)) {
            return 0;
        } else {
            return $info['current'];
        }
    }
}