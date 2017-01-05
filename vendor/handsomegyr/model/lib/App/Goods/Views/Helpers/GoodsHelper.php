<?php
namespace App\Goods\Views\Helpers;

use App\Goods\Models\Category;
use App\Goods\Models\Brand;
use App\Goods\Models\Goods;

class GoodsHelper extends \Phalcon\Tag
{

    /**
     * 获取商品图的地址
     *
     * @return array
     */
    static public function getGoodsImage($baseUrl, $goods_image, $x = 0, $y = 0)
    {
        $modelGoods = new Goods();
        return $modelGoods->getImagePath($baseUrl, $goods_image, $x, $y);
    }

    static public function getSaleState($sale_state)
    {
        $modelGoods = new Goods();
        return $modelGoods->getSaleState($sale_state);
    }

    /**
     * 获取商品分类树
     *
     * @return array
     */
    static public function getCategoryTree($category_id1 = '', $category_id2 = '', $category_id3 = '')
    {
        $modelCategory = new Category();
        $tree = $modelCategory->getTree();
        return $tree;
    }

    /**
     * 获取所有的顶级商品分类列表
     *
     * @return array
     */
    static public function getTopLevelCategoryList()
    {
        $modelCategory = new Category();
        $categoryList = $modelCategory->getTopLevelExtendList();
        return $categoryList;
    }

    /**
     * 获取所有的品牌列表
     *
     * @return array
     */
    static public function getBrandList()
    {
        $modelBrand = new Brand();
        $brandList = $modelBrand->getAll();
        return $brandList;
    }

    /**
     * 获取云列表
     *
     * @return array
     */
    static public function getPeriodList($goods_common_id)
    {
        $modelGoods = new Goods();
        $periodList = $modelGoods->getPeriodList($goods_common_id);
        return $periodList['datas'];
    }

    /**
     * 获取前云和后云
     *
     * @return array
     */
    static public function getNextAndPrevPeriod(array $goodsInfo)
    {
        $goods_common_id = $goodsInfo['goods_commonid'];
        $period = $goodsInfo['period'];
        $modelGoods = new Goods();
        $nextAndPrevPeriod = array();
        $nextPeriodGoodsInfo = $modelGoods->getPeriodInfo($goods_common_id, $period + 1);
        if (! empty($nextPeriodGoodsInfo)) {
            $nextAndPrevPeriod['next'] = $nextPeriodGoodsInfo['_id'];
        }
        if ($period > 1) {
            $prevPeriodGoodsInfo = $modelGoods->getPeriodInfo($goods_common_id, $period - 1);
            if (! empty($prevPeriodGoodsInfo)) {
                $nextAndPrevPeriod['prev'] = $prevPeriodGoodsInfo['_id'];
            }
        }
        return $nextAndPrevPeriod;
    }

    /**
     * 商品广告位
     *
     * @return array
     */
    static public function getAdList()
    {
        $modelGoods = new \App\Goods\Models\Goods();
        $modelGoodsAd = new \App\Goods\Models\Ad();
        $modelGoodsCommon = new \App\Goods\Models\GoodsCommon();
        $goodsId4Ad = $modelGoodsAd->getGoodsIdList();
        $goodsAdList = array();
        if (! empty($goodsId4Ad)) {
            $goodsCommonList = $modelGoodsCommon->getListByIds($goodsId4Ad);
            $goodsIdList = array();
            foreach ($goodsCommonList as $item) {
                if (! empty($item['period_goods_id'])) {
                    $goodsIdList[] = $item['period_goods_id'];
                }
            }
            $goodsList = array();
            if (! empty($goodsIdList)) {
                $goodsList = $modelGoods->getListByIds($goodsIdList, 'goods_commonid');
            }
            foreach ($goodsId4Ad as $goods_commonid) {
                $goodsInfo = ! isset($goodsList[$goods_commonid]) ? array() : $goodsList[$goods_commonid];
                $goodsAdList[$goods_commonid] = array_merge($goodsInfo, $goodsCommonList[$goods_commonid]);
            }
        }
        return $goodsAdList;
    }

    /**
     * 是否已结束
     *
     * @return array
     */
    static public function isOver(array $goodInfo)
    {
        return ($goodInfo['current_period'] == $goodInfo['max_period'] && $goodInfo['sale_state'] == \App\Goods\Models\Goods::SALE_STATE3);
    }

    static public function getCategoryList($id1 = '', $id2 = '', $id3 = '')
    {
        $modelCategory = new Category();
        $categoryList = $modelCategory->getExtendList();
        $ret = array();
        if (! empty($categoryList)) {
            $list = array();
            foreach ($categoryList as $category) {
                $list[$category['_id']] = $category;
            }
            
            if (! empty($id1)) {
                if (isset($list[$id1])) {
                    $ret[] = $list[$id1];
                }
            }
            if (! empty($id2)) {
                if (isset($list[$id2])) {
                    $ret[] = $list[$id2];
                }
            }
            if (! empty($id3)) {
                if (isset($list[$id3])) {
                    $ret[] = $list[$id3];
                }
            }
        }        
        return $ret;
    }
}