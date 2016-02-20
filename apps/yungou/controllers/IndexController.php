<?php
namespace Webcms\Yungou\Controllers;

/**
 * 云购
 *
 * @author Kan
 *        
 */
class IndexController extends ControllerBase
{

    private $modelSite = null;

    private $modelBanner = null;

    private $modelGoodsAd = null;

    private $modelGoods = null;

    private $modelCommon = null;

    private $modelGoodsBrand = null;

    private $modelGoodsCategory = null;

    private $modelGoodsImages = null;

    public function initialize()
    {
        parent::initialize();
        $this->modelSite = new \Webcms\Site\Models\Site();
        $this->modelBanner = new \Webcms\Site\Models\Banner();
        $this->modelGoodsAd = new \Webcms\Goods\Models\Ad();
        $this->modelGoods = new \Webcms\Goods\Models\Goods();
        $this->modelGoodsCommon = new \Webcms\Goods\Models\GoodsCommon();
        $this->modelGoodsBrand = new \Webcms\Goods\Models\Brand();
        $this->modelGoodsCategory = new \Webcms\Goods\Models\Category();
        $this->modelGoodsImages = new \Webcms\Goods\Models\Images();
    }

    /**
     * 首页
     */
    public function indexAction()
    {
        // http://webcms.didv.cn/yungou/index/index
        // 邀请ID
        $invited_id = $this->get('invited_id', '');
        if (! empty($invited_id)) {
            setCookieValue('invited_id', $invited_id, time() + 24 * 3600, '/');
        }        
        // 最新揭晓
        $raffleCount = $this->modelGoods->getRaffleCount();
        $this->assign('raffleCount', $raffleCount);
        // 热门推荐
        $hotGoodsList = $this->modelGoods->getHotList(1, 8);
        $this->assign('hotGoodsList', $hotGoodsList);
        // 即将揭晓
        $announcedSoonGoodsList = $this->modelGoods->getAnnouncedSoonList(1, 24);
        $this->assign('announcedSoonGoodsList', $announcedSoonGoodsList);
        // 新品上架
        $newGoodsList = $this->modelGoods->getNewList(1, 8);
        $this->assign('newGoodsList', $newGoodsList);
        // 晒单分享
    }
    
    public function historybuyrecordsAction()
    {
        // http://webcms.didv.cn/yungou/index/historybuyrecords
        
    }

    public function newestbuyrecordsAction()
    {
        // http://webcms.didv.cn/yungou/index/newestbuyrecords
        
    }

    /**
     * 限购专区页面
     */
    public function limitbuyAction()
    {
        // http://webcms.didv.cn/yungou/index/index
    }
}

