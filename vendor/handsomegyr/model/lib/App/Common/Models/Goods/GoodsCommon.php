<?php
namespace App\Common\Models\Goods;

use App\Common\Models\Base\Base;

class GoodsCommon extends Base
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

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Goods\Mysql\GoodsCommon());
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