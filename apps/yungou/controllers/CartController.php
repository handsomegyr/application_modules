<?php
namespace App\Yungou\Controllers;

/**
 * 购物,下单,支付
 *
 * @author Kan
 *        
 */
class CartController extends ControllerBase
{

    private $modelGoods = null;

    private $modelOrder = null;

    private $modelOrderPay = null;

    private $modelOrderGoods = null;

    private $modelPointsUser = null;

    private $serviceCart = null;
    
    private $modelPointsRule = null;
    
    public function initialize()
    {
        parent::initialize();
        
        $this->modelGoods = new \App\Goods\Models\Goods();
        $this->modelOrder = new \App\Order\Models\Order();
        $this->modelOrderPay = new \App\Order\Models\Pay();
        $this->modelOrderGoods = new \App\Order\Models\Goods();
        $this->modelPointsUser = new \App\Points\Models\User();
        $this->modelPointsRule = new \App\Points\Models\Rule();
        $this->serviceCart = new \App\Order\Services\Cart();
    }

    /**
     * 购物车页面
     */
    public function listAction()
    {
        // http://www.myapplicationmodule.com/yungou/cart/list
        $buyer_id = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
        $cart = $this->serviceCart->getCartByBuyerId($buyer_id);
        $totalAmount = 0.00;
        $list = array();
        if (! empty($cart)) {
            foreach ($cart as $goods_id => $cartItem) {
                $checkRet = $this->serviceCart->checkCartInfo($buyer_id, $goods_id, $cartItem['goods_num'], $cart);
                if (! empty($checkRet['error_code'])) {
                    $cartItem['is_valid'] = false; // 无效
                } else {
                    $cartItem['is_valid'] = true; // 有效
                    $cart = $checkRet['result']['cart'];
                    $goodsInfo = $checkRet['result']['goodsInfo'];
                    // 已为您更新至第{}云
                    $cartItem['is_period_updated'] = ($goodsInfo['original_goods_id'] != $goodsInfo['_id']);
                    // 期数
                    $cartItem['period'] = $goodsInfo['period'];
                    // 剩余人次
                    $cartItem['remain_person_time'] = $goodsInfo['remain_person_time'];
                }
                $list[$goods_id] = $cartItem;
                $totalAmount += $cartItem['goods_num'] * $cartItem['goods_price'];
            }
        }
        $this->assign('cartList', $list);
        $this->assign('totalAmount', $totalAmount);
        
        // 人气推荐
        $commendGoodsList = $this->modelGoods->getCommendList();
        $this->assign('commendGoodsList', $commendGoodsList);
    }

    /**
     * 订单页面
     */
    public function paymentAction()
    {
        // http://www.myapplicationmodule.com/yungou/cart/payment?pay_sn=xxx
        $pay_sn = $this->get('pay_sn', '');
        if (empty($pay_sn)) {
            die('错误');
        }
        $this->assign('pay_sn', $pay_sn);
        $buyer_id = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
        if (empty($buyer_id)) {
            die('登陆');
        }
        $buyerInfo = $this->modelMember->getInfoById($buyer_id);
        $this->assign('buyerInfo', $buyerInfo);
        
        // 获取订单信息 & 总支付金额
        $orderList = $this->modelOrder->getListByPaySn($pay_sn, $buyer_id);
        if (empty($orderList)) {
            die('错误');
        }
        $this->assign('orderList', $orderList);
        
        // 根据订单号获取相应的订单商品列表
        $order_ids = array_keys($orderList);
        $orderGoodsList = $this->modelOrderGoods->getListByOrderIds($order_ids);
        $this->assign('orderGoodsList', $orderGoodsList);
        
        // // 福分
        // $pointInfo = $this->modelPointsUser->getInfoByUserId($buyer_id, POINTS_CATEGORY1);
        // $this->assign('pointInfo', $pointInfo);
        
        // 预存款金额
        $predepositInfo = $this->modelPointsUser->getInfoByUserId($buyer_id, POINTS_CATEGORY3);
        $this->assign('predepositInfo', $predepositInfo);
        
        // 购买所得的积分信息
        $pointsRuleInfo = $this->modelPointsRule->getInfoByCategoryAndCode(POINTS_CATEGORY1, 'buy');
        $this->assign('pointsRuleInfo', $pointsRuleInfo);
    }

    /**
     * 微信支付页面
     */
    public function weixinpayAction()
    {
        // http://www.myapplicationmodule.com/yungou/cart/weixinpay?id=xxx
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        $id = $this->get('id', '');
        if (! empty($id)) {
            $orderPayInfo = $this->modelOrderPay->getInfoById($id);
        }
        if (empty($orderPayInfo)) {
            die('id 不正确');
        }
        $this->assign('orderPayInfo', $orderPayInfo);
    }

    /**
     * 支付成功或失败页面
     */
    public function shopokAction()
    {
        // http://www.myapplicationmodule.com/yungou/cart/shopok?id=xxxx
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        $id = $this->get('id', '');
        if (! empty($id)) {
            $orderPayInfo = $this->modelOrderPay->getInfoById($id);
        }
        if (empty($orderPayInfo)) {
            die('id 不正确');
        }
        $this->assign('orderPayInfo', $orderPayInfo);
        // 获取订单信息
        $orderList = $this->modelOrder->getListByPaySn($orderPayInfo['pay_sn'], $orderPayInfo['buyer_id']);
        $this->assign('orderList', $orderList);
        // 获取订单商品列表
        $order_ids = array_keys($orderList);
        $orderGoodsList = $this->modelOrderGoods->getListByOrderIds($order_ids);
        $this->assign('orderGoodsList', $orderGoodsList);
    }
}

