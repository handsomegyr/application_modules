<?php
namespace App\Yungou\Controllers;

/**
 * 云购
 * 最新揭晓
 *
 * @author Kan
 *        
 */
class LotteryController extends ControllerBase
{

    private $modelGoods = null;

    private $modelGoodsCommon = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->setLayout('index');
        $this->modelGoods = new \App\Goods\Models\Goods();
        $this->modelGoodsCommon = new \App\Goods\Models\GoodsCommon();
    }

    /**
     * 首页
     */
    public function indexAction()
    {
        // http://www.myapplicationmodule.com.com/yungou/lottery/list?i=56360fd4adfb3842018b4569&b=563613f7adfb3809008b4a37&r=10
        // 共揭晓商品
        $lotteryCount = $this->modelGoods->getRaffleCount();
        $this->assign('lotteryCount', $lotteryCount);
    }

    /**
     * 单个商品信息页
     */
    public function detailAction()
    {
        // http://www.myapplicationmodule.com.com/yungou/lottery/detail?id=xxx
        $goods_id = $this->get('id', '');
        if (empty($goods_id)) {
            $this->goToError();
            return;
        }
        // 商品详细信息
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
        // 检查是否已经已揭晓或者揭晓时间未到的时候,跳转到product页面
        if ($goodsInfo['sale_state'] < \App\Goods\Models\Goods::SALE_STATE3 || $goodsInfo['prize_time'] > getMilliTime()) {
            $url = $this->url->get("yungou/product/detail?id={$goods_id}");
            $this->_redirect($url);
            exit();
        }
        // 获取商品公共信息
        $goods_common_id = $goodsInfo['goods_commonid'];
        $goodsCommonInfo = $this->modelGoodsCommon->getInfoById($goods_common_id);
        if (empty($goodsCommonInfo)) {
            $this->goToError();
            return;
        }
        
        $goodsInfo = array_merge($goodsCommonInfo, $goodsInfo);
        $this->assign('goodsInfo', $goodsInfo);
    }
}

