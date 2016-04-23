<?php
namespace App\Yungou\Controllers;

/**
 * 云购
 * 商品
 *
 * @author Kan
 *        
 */
class ProductController extends ControllerBase
{

    private $modelGoods = null;

    private $modelGoodsImages = null;

    private $modelGoodsCommon = null;

    private $modelOrderGoods = null;

    private $modelGoodsCategory = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->setLayout('index');
        $this->modelGoods = new \App\Goods\Models\Goods();
        $this->modelGoodsCategory = new \App\Goods\Models\Category();
        $this->modelGoodsImages = new \App\Goods\Models\Images();
        $this->modelGoodsCommon = new \App\Goods\Models\GoodsCommon();
        $this->modelOrderGoods = new \App\Order\Models\Goods();
    }

    /**
     * 单个商品进行中和结束信息页
     */
    public function indexAction()
    {
        // http://www.jizigou.com/yungou/product/index?id=xxx
        $goods_id = $this->get('id', '');
        if (empty($goods_id)) {
            $this->goToError();
            return;
        }
        // 获取商品公共信息
        $goodsCommonInfo = $this->modelGoodsCommon->getInfoById($goods_id);
        if (empty($goodsCommonInfo)) {
            $this->goToError();
            return;
        }
        
        // 商品详细信息
        $period_goods_id = $goodsCommonInfo['period_goods_id'];
        $goodsInfo = $this->modelGoods->getInfoById($period_goods_id);
        if (empty($goodsInfo)) {
            $this->goToError();
            return;
        }
        
        $goodsInfo = array_merge($goodsCommonInfo, $goodsInfo);
        $this->assign('goodsInfo', $goodsInfo);
        
        // 获取商品图片列表
        $goodsImageList = $this->modelGoodsImages->getListByGoodsCommonColorAndStoreIds(array(
            $goodsInfo['goods_commonid']
        ), array(), array());
        $this->assign('goodsImageList', $goodsImageList);
        
        if ($goodsInfo['sale_state'] == \App\Goods\Models\Goods::SALE_STATE1) {} else {
            // 即将揭晓
            $announcedSoonGoodsList = $this->modelGoods->getAnnouncedSoonList(1, 4);
            $this->assign('announcedSoonGoodsList', $announcedSoonGoodsList);
        }
    }

    /**
     * 商品列表页面
     */
    public function listAction()
    {
        // http://www.jizigou.com/yungou/product/list?i=56360fd4adfb3842018b4569&b=563613f7adfb3809008b4a37&r=10
        $page = $this->get('page', '1');
        $size = $this->get('size', '40');
        $i = $this->get('i', ''); // 商品分类
        $this->assign('i', $i);
        $b = $this->get('b', ''); // 商品品牌
        $this->assign('b', $b);
        $r = $this->get('r', '10'); // 排序方式
        $this->assign('r', $r);
        $tag = $this->get('tag', ''); // 限购tag=10
        $this->assign('tag', $tag);
        
        if (! empty($i)) {
            $categoryInfo = $this->modelGoodsCategory->getInfoById($i);
            if (! empty($categoryInfo)) {
                $this->assign('categoryInfo', $categoryInfo);
            }
        }
        
        // 检索条件
        $query = $this->getQuery($r, $i, $b, $tag, "");
        // 排序
        $sort = $this->getSort($r);
        // 限购
        if ($tag == 10) {
            $sort = array();
        }
        // 获取商品分页列表信息
        $goodsList = $this->modelGoods->getPageList($page, $size, $query, $sort, array());
        $this->assign('goodsList', $goodsList);
        // 创建分页信息
        $url = $this->getSelfUrl();
        $sch = array(
            'i' => $i,
            'b' => $b,
            'r' => $r
        );
        if ($tag) {
            $sch['tag'] = $tag;
        }
        $pager = createPager($url, $goodsList['total'], $page, $size, $sch);
        $this->assign('pager', $pager);
    }

    /**
     * 商品搜索页面
     */
    public function searchAction()
    {
        // http://www.jizigou.com/yungou/product/search?r=10&q=xxx
        // 当前页
        $page = $this->get('page', '1');
        // 每页显示记录数
        $size = $this->get('size', '40');
        $r = $this->get('r', '10'); // 排序方式
                                    // 关键字
        $q = $this->get('q', '');
        $q = urldecode($q);
        $this->assign('q', $q);
        
        // 检索条件
        $query = $this->getQuery($r, '', '', '', $q);
        // 排序
        $sort = array();
        // 获取商品分页列表信息
        $goodsList = $this->modelGoods->getPageList($page, $size, $query, $sort, array());
        $this->assign('goodsList', $goodsList);
        
        // 创建分页信息
        $url = $this->getSelfUrl();
        $sch = array(
            'q' => $q
        );
        $pager = createPager($url, $goodsList['total'], $page, $size, $sch);
        $this->assign('pager', $pager);
    }

    /**
     * 单个商品 揭晓中信息页
     */
    public function detailAction()
    {
        // http://www.jizigou.com/yungou/product/detail?id=xxx
        $goods_id = $this->get('id', '');
        if (empty($goods_id)) {
            $this->goToError();
            return;
        }
        $goodsInfo = $this->modelGoods->getInfoById($goods_id);
        if (empty($goodsInfo)) {
            $this->goToError();
            return;
        }
        
        // 检查是否在进行中的时候,跳转到商品页面
        if ($goodsInfo['sale_state'] == \App\Goods\Models\Goods::SALE_STATE1) {
            $url = $this->url->get("yungou/product/index?id={$goodsInfo['goods_commonid']}");
            $this->_redirect($url);
            exit();
        }
        
        // 检查是否已经已揭晓并且揭晓时间已到的时候,跳转到lottery页面
        if ($goodsInfo['sale_state'] == \App\Goods\Models\Goods::SALE_STATE3 && $goodsInfo['prize_time'] <= getMilliTime()) {
            $url = $this->url->get("yungou/lottery/detail?id={$goods_id}");
            $this->_redirect($url);
            exit();
        }
        
        // 获取商品公共信息
        $goods_commonid = $goodsInfo['goods_commonid'];
        $goodsCommonInfo = $this->modelGoodsCommon->getInfoById($goods_commonid);
        if (empty($goodsCommonInfo)) {
            $this->goToError();
            return;
        }
        $goodsInfo = array_merge($goodsCommonInfo, $goodsInfo);
        $this->assign('goodsInfo', $goodsInfo);
    }

    /**
     * 根据 即将揭晓 人气 剩余人次 最新 价值 限购等排序字段进行排序
     *
     * @param number $r            
     * @return array
     */
    private function getSort($r)
    {
        $sort = array();
        
        if ($r == 10) { // 即将揭晓 r=10 or 30 限购tag=10
            $sort = array(
                'complete_percent' => - 1
            );
        } elseif ($r == 20) { // 人气r=20
            $sort = array(
                'collect' => - 1
            );
        } elseif ($r == 40) { // 剩余人次r=40
            $sort = array(
                'remain_person_time' => 1
            );
        } elseif ($r == 50) { // 最新r=50
            $sort = array(
                '_id' => - 1
            );
        } elseif ($r == 31) { // 价值r=31
            $sort = array(
                'price' => 1
            );
        } elseif ($r == 30) { // 价值r=30
            $sort = array(
                'price' => - 1
            );
        }
        
        return $sort;
    }

    /**
     * 根据 即将揭晓 人气 剩余人次 最新 价值 限购等排序字段获取商品列表
     *
     * @param string $i            
     * @param string $b            
     * @param number $tag            
     * @param string $q            
     */
    private function getQuery($r, $i, $b, $tag, $q)
    {
        $query = array();
        // 第1层分类
        if (! empty($i)) {
            $query['gc_id_1'] = $i;
        }
        // 品牌
        if (! empty($b)) {
            $query['brand_id'] = $b;
        }
        // 限购
        if ($tag == 10) {
            $query['restrict_person_time'] = array(
                '$gt' => 0
            );
        }
        // 关键字
        if (! empty($q)) {
            $query['name'] = array(
                '$like' => '%' . $q . '%'
            );
        }
        // 进行中的商品
        $query['sale_state'] == \App\Goods\Models\Goods::SALE_STATE1;
        
        return $query;
    }
}

