<?php
namespace App\Common\Models\Goods;

use App\Common\Models\Base\Base;

class Goods extends Base
{
    
    // 商品状态 0下架，1正常，10违规（禁售）
    const STATEDATAS = array(
        '10' => array(
            'name' => '违规',
            'value' => '10'
        ),
        '1' => array(
            'name' => '正常',
            'value' => '1'
        ),
        '0' => array(
            'name' => '下架',
            'value' => '0'
        )
    );
    
    // 商品审核 1通过，0未通过，10审核中
    const VERIFYDATAS = array(
        '10' => array(
            'name' => '审核中',
            'value' => '10'
        ),
        '1' => array(
            'name' => '通过',
            'value' => '1'
        ),
        '0' => array(
            'name' => '未通过',
            'value' => '0'
        )
    );
    
    // 销售状态 1 进行中 2 揭晓中 3 已揭晓
    const SALESTATEDATAS = array(
        '1' => array(
            'name' => '进行中',
            'value' => '1'
        ),
        '2' => array(
            'name' => '揭晓中',
            'value' => '2'
        ),
        '3' => array(
            'name' => '已揭晓',
            'value' => '3'
        )
    );

    const SALE_STATE1 = 1; // 进行中
    const SALE_STATE2 = 2; // 揭晓中
    const SALE_STATE3 = 3; // 已揭晓
    const STATE0 = 0; // 下架
    const STATE1 = 1; // 出售中
    const STATE10 = 10; // 违规
    const VERIFY0 = 0; // 审核失败
    const VERIFY1 = 1; // 审核通过
    const VERIFY10 = 10; // 等待审核
    function __construct()
    {
        $this->setModel(new \App\Common\Models\Goods\Mysql\Goods());
    }

    public function getImagePath($baseUrl, $image, $x = 0, $y = 0)
    {
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
        return trim("goods/1", '/');
    }
}