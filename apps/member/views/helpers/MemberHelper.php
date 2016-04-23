<?php
namespace App\Member\Views\Helpers;

use App\Member\Models\Member;
use App\Member\Models\Consignee;

class MemberHelper extends \Phalcon\Tag
{

    /**
     * 获取会员登录名称
     *
     * @return string
     */
    static public function getLoginName(array $memberInfo = array())
    {
        $modelMember = new Member();
        $loginName = $modelMember->getLoginName($memberInfo);
        return $loginName;
    }

    /**
     * 获取会员注册名称
     *
     * @return string
     */
    static public function getRegisterName(array $memberInfo = array(), $isHidden = false)
    {
        $modelMember = new Member();
        $registerName = $modelMember->getRegisterName($memberInfo, $isHidden);
        return $registerName;
    }

    /**
     * 获取会员头像
     *
     * @return string
     */
    static public function getImagePath($baseUrl, $avatar, $x = 0, $y = 0)
    {
        $modelMember = new Member();
        return $modelMember->getImagePath($baseUrl, $avatar, $x, $y);
    }

    /**
     * 获取会员的收货地址列表
     *
     * @return array
     */
    static public function getConsigneeList($member_id, $is_default = false)
    {
        $modelConsignee = new Consignee();
        $consigneeList = $modelConsignee->getListByMemberId($member_id, $is_default);
        return $consigneeList;
    }

    /**
     * 检测是否需要支付密码
     *
     * @return boolean
     */
    static public function isNeedPaypwd(array $buyerInfo, $pay_amount)
    {
        $modelMember = new Member();
        return $modelMember->isNeedPaypwd($buyerInfo, $pay_amount);
    }
}