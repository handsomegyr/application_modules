<?php
namespace App\Common\Models\Post;

use App\Common\Models\Base\Base;

class Post extends Base
{
    
    // 状态 -1未晒单 0待审核 1未通过 2审核通过
    const STATEDATAS = array(
        '-1' => array(
            'name' => '未晒单',
            'value' => '-1'
        ),
        '0' => array(
            'name' => '待审核',
            'value' => '0'
        ),
        '1' => array(
            'name' => '未通过',
            'value' => '1'
        ),
        '2' => array(
            'name' => '已通过',
            'value' => '2'
        )
    );

    const STATE_NONE = - 1; // 未晒单
    const STATE0 = 0; // 待审核
    const STATE1 = 1; // 未通过
    const STATE2 = 2; // 审核通过
    function __construct()
    {
        $this->setModel(new \App\Common\Models\Post\Mysql\Post());
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
        return "{$baseUrl}service/file/index?id={$image}&upload_path={$uploadPath}";
    }

    public function getUploadPath()
    {
        return trim("post", '/');
    }
}