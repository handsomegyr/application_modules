<?php
namespace App\Site\Views\Helpers;

use App\Site\Models\Site;

class SiteHelper extends \Phalcon\Tag
{

    /**
     * 获取网站设置
     *
     * @return array
     */
    static public function getSettings()
    {
        $modelSite = new Site();
        // 热门搜索
        $siteSettingInfo = $modelSite->getSettings(YUNGOU_SITE_ID);
        return $siteSettingInfo;
    }

    /**
     * 热门搜索
     *
     * @return array
     */
    static public function getHotSearchList()
    {
        $siteSettingInfo = self::getSettings();
        $hot_search_list = explode(',', $siteSettingInfo['hot_search']);
        return $hot_search_list;
    }
}