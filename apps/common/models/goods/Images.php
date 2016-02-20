<?php
namespace Webcms\Common\Models\Goods;

use Webcms\Common\Models\Base;

class Images extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Goods\Images());
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