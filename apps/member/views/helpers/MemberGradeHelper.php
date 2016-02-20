<?php
namespace Webcms\Member\Helpers;

use Webcms\Member\Models\Grade;

class MemberGradeHelper extends \Phalcon\Tag
{

    /**
     * 根据经验值，获取会员等级信息
     *
     * @return array
     */
    static public function getGradeInfo($exp = 0)
    {
        $modelMemberGrade = new Grade();
        return $modelMemberGrade->getGradeInfo($exp);
    }
}