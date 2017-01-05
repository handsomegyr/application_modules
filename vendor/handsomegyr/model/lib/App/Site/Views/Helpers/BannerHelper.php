<?php
namespace App\Site\Views\Helpers;

use App\Site\Models\Banner;

class BannerHelper extends \Phalcon\Tag
{

    /**
     * 获取Banner图的地址
     *
     * @return array
     */
    static public function getImagePath($baseUrl, $image, $x = 0, $y = 0)
    {
        $modelBanner = new Banner();
        return $modelBanner->getImagePath($baseUrl, $image, $x, $y);
    }

    /**
     * 广告位
     *
     * @return array
     */
    static public function getList()
    {
        $modelBanner = new Banner();
        $bannerList = $modelBanner->getAll();
        return $bannerList;
    }
}