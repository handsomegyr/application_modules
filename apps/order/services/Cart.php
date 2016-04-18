<?php
namespace App\Order\Services;

class Cart
{

    private $modelGoods = null;

    private $modelGoodsCommon = null;

    private $modelOrderCart = null;

    function __construct()
    {
        $this->modelGoods = new \App\Goods\Models\Goods();
        $this->modelGoodsCommon = new \App\Goods\Models\GoodsCommon();
        $this->modelOrderCart = new \App\Order\Models\Cart();
    }

    /**
     * 根据买家ID获取未结算的购物列表
     *
     * @param string $buyer_id            
     * @return array
     */
    public function getCartByBuyerId($buyer_id)
    {
        if (empty($buyer_id)) {
            // COOKIE中获取内容
            $cart = getCookieValue('cart');
        } else {
            // 从数据库中根据购买者获取内容
            $cart = $this->modelOrderCart->getCartByBuyerId($buyer_id);
        }
        $cart = $cart ? $cart : array();
        return $cart;
    }

    /**
     * 加入购物车
     *
     * @param string $buyer_id            
     * @param string $goods_id            
     * @param number $quantity            
     * @return array
     */
    public function addCart($buyer_id, $goods_id, $quantity)
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        
        // 获取购物车
        $cart = $this->getCartByBuyerId($buyer_id);
        if (key_exists($goods_id, $cart)) {
            // 原来的数量加上新增的数量
            $quantity = intval($cart[$goods_id]['goods_num']) + $quantity;
        }
        // 检查购物车信息
        $checkRet = $this->checkCartInfo($buyer_id, $goods_id, $quantity, $cart);
        if (! empty($checkRet['error_code'])) {
            $ret['error_code'] = $checkRet['error_code'];
            $ret['error_msg'] = $checkRet['error_msg'];
            return $ret;
        }
        $cart = $checkRet['result']['cart'];
        $ret['result'] = $cart;
        
        return $ret;
    }

    /**
     * 更新商品的购买数量
     *
     * @param string $buyer_id            
     * @param string $goods_id            
     * @param number $num            
     */
    public function updateCart($buyer_id, $goods_id, $quantity)
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        // 获取购物车
        $cart = $this->getCartByBuyerId($buyer_id);
        if (key_exists($goods_id, $cart)) {
            // 检查商品信息
            $checkRet = $this->checkCartInfo($buyer_id, $goods_id, $quantity, $cart);
            if (! empty($checkRet['error_code'])) {
                $ret['error_code'] = $checkRet['error_code'];
                $ret['error_msg'] = $checkRet['error_msg'];
                return $ret;
            }
            $cart = $checkRet['result']['cart'];
        } else {
            $ret['error_code'] = - 1;
            $ret['error_msg'] = "商品ID不存在";
            return $ret;
        }
        $ret['result'] = $cart;
        return $ret;
    }

    /**
     * 清空购物车
     *
     * @param string $buyer_id            
     * @param array $goods_ids            
     */
    public function clearCart($buyer_id, array $goods_ids)
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        // 获取购物车
        $cart = $this->getCartByBuyerId($buyer_id);
        if (! empty($buyer_id)) {
            // 从数据库中清空该购买者对应的购物内容
            $this->modelOrderCart->clear($buyer_id, $goods_ids);
        }
        if (empty($goods_ids)) {
            $cart = array();
            setCookieValue('cart', $cart, time() - 3600, '/', null, null, true);
        } else {
            foreach ($goods_ids as $goods_id) {
                unset($cart[$goods_id]);
            }
            setCookieValue('cart', $cart, 3600, '/', null, null, true);
        }
        $ret['result'] = $cart;
        return $ret;
    }

    public function mergeCart($buyer_id)
    {
        $cartInCookie = $this->getCartByBuyerId("");
        if (empty($cartInCookie)) {
            return;
        }
        $cartInDb = $this->getCartByBuyerId($buyer_id);
        if (! empty($cartInDb)) {
            foreach ($cartInDb as $goods_id => $item) {
                if (array_key_exists($goods_id, $cartInCookie)) {
                    unset($cartInCookie[$goods_id]);
                }
            }
        }
        if (! empty($cartInCookie)) {
            foreach ($cartInCookie as $goods_id => $item) {
                $this->addCart($buyer_id, $goods_id, $item['goods_num']);
            }
        }
        $this->clearCart("", array());
    }

    /**
     * 检查购物车信息
     *
     * @param string $buyer_id            
     * @param string $goods_id            
     * @param number $quantity            
     * @param array $cart            
     * @return array
     */
    public function checkCartInfo($buyer_id, $goods_id, $quantity, array $cart = array())
    {
        $ret = array(
            'error_code' => '',
            'error_msg' => '',
            'result' => array()
        );
        
        // 判断商品ID是否正确
        $goodsInfo = $this->modelGoods->getInfoById($goods_id);
        if (empty($goodsInfo)) {
            $ret['error_code'] = - 1;
            $ret['error_msg'] = '该商品不存在';
            return $ret;
        }
        
        if ($goodsInfo['state'] != \App\Common\Models\Goods\Goods::STATE1 || $goodsInfo['verify'] != \App\Common\Models\Goods\Goods::VERIFY1) {
            $ret['error_code'] = - 3;
            $ret['error_msg'] = '该商品已失效';
            return $ret;
        }
        
        // 本期的商品也不在进行中
        if ($goodsInfo['sale_state'] != \App\Common\Models\Goods\Goods::SALE_STATE1) {
            // 获取下一期的商品
            $goodsCommonInfo = $this->modelGoodsCommon->getInfoById($goodsInfo['goods_commonid']);
            if ($goodsCommonInfo['period_goods_id'] != $goodsInfo['goods_id']) {
                $goodsInfo = $this->modelGoods->getInfoById($goodsCommonInfo['period_goods_id']);
            }
            // 下一期的商品也不在进行中
            if ($goodsInfo['sale_state'] != \App\Common\Models\Goods\Goods::SALE_STATE1) {
                $ret['error_code'] = - 4;
                $ret['error_msg'] = '该商品已失效';
                return $ret;
            }
        }
        
        // 购买数量 = 剩余人次和购买人次中的最小值
        $quantity = min($quantity, $goodsInfo['remain_person_time']);
        // 记录原来的商品id
        $goodsInfo['original_goods_id'] = $goods_id;
        
        // 如果原来的商品ID有发生变化的话，说明期数有更新
        if ($goodsInfo['original_goods_id'] != $goodsInfo['_id']) {
            // 删除原来的商品
            $clearRet = $this->clearCart($buyer_id, array(
                $goodsInfo['original_goods_id']
            ));
            if (! empty($clearRet['error_code'])) {
                $ret['error_code'] = $clearRet['error_code'];
                $ret['error_msg'] = $clearRet['error_msg'];
                return $ret;
            }
            $cart = $clearRet['result'];
        }
        
        $goods_id = $goodsInfo['_id'];
        if (key_exists($goods_id, $cart)) {
            $cart[$goods_id]['goods_num'] = $quantity;
            // 更新数据库里的记录
            $this->modelOrderCart->updateNum($buyer_id, $goods_id, $quantity);
        } else {
            // 存入购物车
            $goodsInfo['goods_num'] = $quantity;
            // 数据库中记录数据
            $cart[$goods_id] = $this->modelOrderCart->record($buyer_id, $goodsInfo);
        }
        if (empty($buyer_id)) {
            // cookie中记录
            setCookieValue('cart', $cart, time() + 3600, '/', null, null, true);
        }
        $ret['result'] = array(
            'cart' => $cart,
            'goodsInfo' => $goodsInfo
        );
        return $ret;
    }

    /**
     * 更新结算字段信息
     *
     * @param string $cart_id            
     */
    public function checkout($cart_id)
    {
        $this->modelOrderCart->checkout($cart_id);
    }
}