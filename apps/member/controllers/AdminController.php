<?php
namespace App\Member\Controllers;

/**
 * 账号设置
 *
 * @author Kan
 *        
 */
class AdminController extends ControllerBase
{

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 个人资料页面
     */
    public function membermodifyAction()
    {
        // http://www.myapplicationmodule.com/member/admin/membermodify
    }

    /**
     * 个人头像页面
     */
    public function userphotoAction()
    {
        // http://www.myapplicationmodule.com/member/admin/userphoto
    }

    /**
     * 收货地址页面
     */
    public function addressAction()
    {
        // http://www.myapplicationmodule.com/member/admin/address
        $consigneeList = $this->modelConsignee->getListByMemberId($_SESSION['member_id']);
        $this->assign('consigneeList', $consigneeList);
    }

    /**
     * 隐私设置页面
     */
    public function privacysettingsAction()
    {
        // http://www.myapplicationmodule.com/member/admin/privacysettings
    }

    /**
     * 常用设置页面
     */
    public function noticesettingsAction()
    {
        // http://www.myapplicationmodule.com/member/admin/noticesettings
    }

}

