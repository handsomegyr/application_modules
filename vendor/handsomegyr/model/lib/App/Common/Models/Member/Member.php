<?php
namespace App\Common\Models\Member;

use App\Common\Models\Base\Base;

class Member extends Base
{
    // 私信
    const PRIVACY_MSGSET1 = 1; // 1:仅限好友
    const PRIVACY_MSGSET2 = 2; // 2:禁止
                               
    // 地理位置
    const PRIVACY_AREASET0 = 0; // 0:允许
    const PRIVACY_AREASET1 = 1; // 1:禁止

    // 好友搜索 0:允许 1:禁止
    const PRIVACY_SEARCHSET0 = 0; // 0:允许
    const PRIVACY_SEARCHSET1 = 1; // 1:禁止
    
    // 云购记录
    const PRIVACY_BUYSET0 = 0; // 0:所有人可见
    const PRIVACY_BUYSET1 = 1; // 1:好友可见
    const PRIVACY_BUYSET2 = 2; // 2:仅自己可见
                               
    // 个人主页-获得的商品
    const PRIVACY_RAFSET0 = 0; // 0:所有人可见
    const PRIVACY_RAFSET1 = 1; // 1:好友可见
    const PRIVACY_RAFSET2 = 2; // 2:仅自己可见
                               
    // 云购记录
    const PRIVACY_POSTSET0 = 0; // 0:所有人可见
    const PRIVACY_POSTSET1 = 1; // 1:好友可见
    const PRIVACY_POSTSET2 = 2; // 2:仅自己可见
    const REGISTERBY1 = 1; // 手机注册
    const REGISTERBY2 = 2; // 邮箱注册
    const REGISTERBY3 = 3; // 帐号注册
                           
    // 会员的注册方式 1为手机 2为邮箱 3为账户
    const REGISTERBYDATAS = array(
        '1' => array(
            'name' => '手机',
            'value' => '1'
        ),
        '2' => array(
            'name' => '邮箱',
            'value' => '2'
        ),
        '3' => array(
            'name' => '账户',
            'value' => '3'
        )
    );

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Member\Mysql\Member());
    }

    public function getImagePath($baseUrl, $image, $x = 0, $y = 0)
    {
        if (empty($image)) {
            $image = "UserFace-160-0000.jpg";
        }
        $uploadPath = $this->getUploadPath();
        // return "{$baseUrl}upload/{$uploadPath}/{$image}";
        $xyStr = "";
        if (! empty($x)) {
            $xyStr .= "&w={$x}";
        }
        if (! empty($y)) {
            $xyStr .= "&h={$y}";
        }
        return "{$baseUrl}service/file/index?id={$image}&upload_path={$uploadPath}{$xyStr}";
    }

    public function getUploadPath()
    {
        return trim("member/avatar", '/');
    }
}