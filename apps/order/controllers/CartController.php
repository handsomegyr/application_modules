<?php
namespace App\Order\Controllers;

/**
 * 购物车服务
 *
 * @author Admin
 *        
 */
class CartController extends ControllerBase
{

    private $modelGoods = null;

    private $modelOrder = null;

    private $modelOrderCart = null;

    private $modelOrderGoods = null;

    private $modelOrderCommon = null;

    private $modelOrderPay = null;

    private $modelStore = null;

    private $modelMember = null;

    private $modelMemberConsignee = null;

    private $serviceCart = null;

    private $servicePredeposit = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelOrderCart = new \App\Order\Models\Cart();
        $this->modelGoods = new \App\Goods\Models\Goods();
        $this->modelOrder = new \App\Order\Models\Order();
        $this->modelOrderGoods = new \App\Order\Models\Goods();
        $this->modelOrderCommon = new \App\Order\Models\OrderCommon();
        $this->modelOrderPay = new \App\Order\Models\Pay();
        $this->modelStore = new \App\Store\Models\Store();
        $this->modelMember = new \App\Member\Models\Member();
        $this->modelMemberConsignee = new \App\Member\Models\Consignee();
        
        $this->serviceCart = new \App\Order\Services\Cart();
    }

    /**
     * 向购物车中增加商品
     */
    public function addAction()
    {
        try {
            // http://www.jizigou.com/order/cart/add?goods_id=56372bd07f50eab004000443&quantity=1
            $goods_id = $this->get('goods_id', '');
            if (empty($goods_id)) {
                echo ($this->error(- 1, '商品ID为空'));
                return false;
            }
            $quantity = intval($this->get('quantity', 0));
            if (empty($quantity) || $quantity < 1) {
                echo ($this->error(- 2, '商品数量为空或不正确'));
                return false;
            }
            $buyer_id = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
            
            // 追加购物车商品处理
            $checkRet = $this->serviceCart->addCart($buyer_id, $goods_id, $quantity);
            if (! empty($checkRet['error_code'])) {
                echo ($this->error(- 3, $checkRet['error_msg']));
                return false;
            }
            // 数据库中记录数据
            $cart = $checkRet['result'];
            
            echo ($this->result("OK", $cart));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 更改购物车内某商品的数量
     */
    public function updatenumAction()
    {
        try {
            // http://www.jizigou.com/order/cart/updatenum?goods_id=56372bd07f50eab004000443&quantity=1
            $goods_id = trim($this->get('goods_id', '')); // 商品ID
            $quantity = intval($this->get('quantity')); // 商品数量
            
            if (empty($goods_id)) {
                echo ($this->error("-1", "商品ID为空"));
                return false;
            }
            if (empty($quantity) || $quantity < 1) {
                echo ($this->error("-2", "数量不能为空或数量不正确"));
                return false;
            }
            $buyer_id = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
            
            // 更新购物车数量
            $checkRet = $this->serviceCart->updateCart($buyer_id, $goods_id, $quantity);
            if (! empty($checkRet['error_code'])) {
                echo ($this->error(- 3, $checkRet['error_msg']));
                return false;
            }
            // 数据库中记录数据
            $cart = $checkRet['result'];
            echo ($this->result("OK", $cart));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 删除内某商品or清空购物车
     */
    public function clearAction()
    {
        try {
            // http://www.jizigou.com/order/cart/clear?goods_ids=56372bd07f50eab004000443,xxx,xxx
            $goods_ids = trim($this->get('goods_ids', '')); // 商品ID,以逗号分隔
            if (! empty($goods_ids)) {
                $goods_ids = explode(",", $goods_ids);
            } else {
                $goods_ids = array();
            }
            $buyer_id = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
            // 清空或指定多个商品的处理
            $checkRet = $this->serviceCart->clearCart($buyer_id, $goods_ids);
            if (! empty($checkRet['error_code'])) {
                echo ($this->error(- 3, $checkRet['error_msg']));
                return false;
            }
            // 数据库中记录数据
            $cart = $checkRet['result'];
            echo ($this->result("OK", $cart));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 查看购物车
     */
    public function viewAction()
    {
        try {
            // http://www.jizigou.com/order/cart/add?goods_id=56372bd07f50eab004000443&quantity=1
            $buyer_id = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
            // 获取购物车
            $cart = $this->serviceCart->getCartByBuyerId($buyer_id);
            if (empty($cart)) {
                echo ($this->result("购物车没有内容"));
                return true;
            }
            echo ($this->result("OK", $cart));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 获取购物车的统计信息
     */
    public function cartnumAction()
    {
        // http://www.jizigou.com/order/cart/cartnum
        try {
            $info = array(
                'num' => 0,
                'money' => 0.00
            );
            $buyer_id = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
            // 获取购物车
            $cart = $this->serviceCart->getCartByBuyerId($buyer_id);
            if (! empty($cart)) {
                foreach ($cart as $goods_id => $goodsInfo) {
                    $quantity = $goodsInfo['goods_num'];
                    $prize = $goodsInfo['goods_price'];
                    $info['num'] += $quantity;
                    $info['money'] += $quantity * $prize;
                }
            }
            echo ($this->result("OK", $info));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 购物车详情接口
     */
    public function cartlabelAction()
    {
        // http://cart.1yyg.com/JPData?action=cartlabel&fun=jQuery18105469640658702701_1452515680946&_=1452515713120
        // jQuery18105469640658702701_1452515680946({'code':0,'count':7,'money':4,'listItems':[{'codeID':2890420,'goodsPic':'20151116141603129.jpg','goodsName':'乐扣乐扣（LOCK&LOCK）缤纷马克保温杯 330ml','shopNum':1,'goodsID':22646,'codeQuantity':59,'codeSurplus':50,'codeType':0,'codeLimitBuy':0,'myLimitSales':0},{'codeID':2910467,'goodsPic':'20160107151328394.jpg','goodsName':'奔驰（Benz）长轴距C级 2015款 C 200 L 轿车','shopNum':1,'goodsID':22850,'codeQuantity':428888,'codeSurplus':103046,'codeType':0,'codeLimitBuy':0,'myLimitSales':0},{'codeID':2651601,'goodsPic':'20150831181841658.jpg','goodsName':'小米（MIUI）蓝牙耳机 白色','shopNum':1,'goodsID':22492,'codeQuantity':99,'codeSurplus':96,'codeType':0,'codeLimitBuy':0,'myLimitSales':0},{'codeID':2746052,'goodsPic':'20151119164928786.jpg','goodsName':'苹果（Apple）iPhone 6s Plus 16G版 4G手机','shopNum':1,'goodsID':22667,'codeQuantity':6088,'codeSurplus':1051,'codeType':3,'codeLimitBuy':5,'myLimitSales':0},{'codeID':2882137,'goodsPic':'20151105111557474.jpg','goodsName':'苹果（Apple）iPhone 6s Plus 128G版 4G手机','shopNum':1,'goodsID':22612,'codeQuantity':8090,'codeSurplus':0,'codeType':3,'codeLimitBuy':5,'myLimitSales':0},{'codeID':2919782,'goodsPic':'20150910150825965.jpg','goodsName':'苹果（Apple）iPhone 6s 16G版 4G手机','shopNum':1,'goodsID':22504,'codeQuantity':5188,'codeSurplus':0,'codeType':0,'codeLimitBuy':0,'myLimitSales':0},{'codeID':2785502,'goodsPic':'20151117180200543.jpg','goodsName':'闪迪（SanDisk）至尊高速 MicroSDXC UHS-I 存储卡 64GB-Class10-48Mb/s','shopNum':1,'goodsID':22658,'codeQuantity':109,'codeSurplus':0,'codeType':0,'codeLimitBuy':0,'myLimitSales':0}]})
        // http://www.jizigou.com/order/cart/cartlabel
        try {
            $buyer_id = empty($_SESSION['member_id']) ? '' : $_SESSION['member_id'];
            // 获取购物车
            $cart = $this->serviceCart->getCartByBuyerId($buyer_id);
            $num = 0;
            $money = 0;
            $datas = array();
            if (! empty($cart)) {
                $goodsIdList = array_keys($cart);
                $goodsInfoList = $this->modelGoods->getListByIds($goodsIdList);
                
                foreach ($cart as $goods_id => $goodsInfo) {
                    // 'codeID':2890420,
                    // 'goodsPic':'20151116141603129.jpg',
                    // 'goodsName':'乐扣乐扣（LOCK&LOCK）缤纷马克保温杯 330ml',
                    // 'shopNum':1,
                    // 'goodsID':22646,
                    // 'codeQuantity':59,
                    // 'codeSurplus':50,
                    // 'codeType':0,
                    // 'codeLimitBuy':0,
                    // 'myLimitSales':0
                    $data = array();
                    $data['codeID'] = $goodsInfo['goods_id'];
                    $data['goodsPic'] = $this->modelGoods->getImagePath($this->baseUrl, $goodsInfo['goods_image']);
                    $data['goodsName'] = $goodsInfo['goods_name'];
                    $data['shopNum'] = $goodsInfo['goods_num'];
                    $data['goodsID'] = $goodsInfo['goods_commonid'];
                    $data['codeQuantity'] = $goodsInfoList[$goods_id]['total_person_time'];
                    $data['codeSurplus'] = $goodsInfoList[$goods_id]['remain_person_time'];
                    $data['codeType'] = 0;
                    $data['codeLimitBuy'] = $goodsInfoList[$goods_id]['restrict_person_time'];
                    $data['myLimitSales'] = 0;
                    $num += $goodsInfo['goods_num'];
                    $money += $goodsInfo['goods_num'] * $goodsInfo['goods_price'];
                    $datas[] = $data;
                }
            }
            
            $info = array(
                'count' => $num,
                'money' => $money,
                'datas' => $datas
            );
            
            echo ($this->result("OK", $info));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 购物车结算处理
     */
    public function checkoutAction()
    {
        try {
            // http://www.jizigou.com/order/cart/checkout?goods_ids=xx,xxx,xxx&order_from=1&order_message=xxxx&pay_name=online
            // 购买商品信息
            $goods_ids = trim($this->get('goods_ids', '')); // 所选商品列表
            
            if (empty($goods_ids)) {
                $goods_ids = array();
            } else {
                $goods_ids = explode(',', $goods_ids);
            }
            if (empty($goods_ids)) {
                echo ($this->error(- 2, '购物车为空'));
                return false;
            }
            
            // 订单来源
            $order_from = intval($this->get('order_from', '1'));
            $order_from = ($order_from == 2) ? 2 : 1; // 1 WEB 2 MOBILE
                                                      
            // 订单留言
            $order_message = $this->get('order_message', '');
            
            // 付款方式 online/offline
            $pay_name = $this->get('pay_name', 'online');
            $pay_name = ($pay_name == 'offline') ? 'offline' : 'online';
            
            // 买家信息
            $buyer_id = $_SESSION['member_id'];
            if (empty($buyer_id)) {
                echo ($this->error(- 1, '购买者为空'));
                return false;
            }
            // 获取会员信息
            $buyerInfo = $this->modelMember->getInfoById($buyer_id);
            if (empty($buyerInfo)) {
                echo ($this->error(- 1, '购买者不存在'));
                return false;
            }
            $buyerInfo['buyer_id'] = $buyerInfo['_id'];
            $buyerInfo['buyer_name'] = $this->modelMember->getLoginName($buyerInfo);
            $buyerInfo['buyer_email'] = $buyerInfo['email'];
            $buyerInfo['buyer_mobile'] = $buyerInfo['mobile'];
            $buyerInfo['buyer_avatar'] = $buyerInfo['avatar'];
            $buyerInfo['buyer_register_by'] = $buyerInfo['register_by'];
            $buyerInfo['buyer_ip'] = getIp();
            
            // 根据购买者获取购物车的信息
            $cart = $this->serviceCart->getCartByBuyerId($buyer_id);
            
            // 按照门店分组计算以下信息
            $store_id = YUNGOU_STORE_ID;
            $list = array();
            if (! empty($cart)) {
                foreach ($cart as $goods_id => $cartItem) {
                    if (! in_array($goods_id, $goods_ids)) {
                        continue;
                    }
                    $checkRet = $this->serviceCart->checkCartInfo($buyer_id, $goods_id, $cartItem['goods_num'], $cart);
                    if (! empty($checkRet['error_code'])) {
                        continue;
                    }
                    // 更新结算字段
                    $this->serviceCart->checkout($cartItem['_id']);
                    $goodsInfo = $checkRet['result']['goodsInfo'];
                    $cartItem['goods_period'] = $goodsInfo['period']; // 期数
                    $cartItem['goods_commonid'] = $goodsInfo['goods_commonid']; // 商品公共ID
                    $cartItem['gc_id'] = $goodsInfo['gc_id']; // 商品分类
                    $cartItem['goods_total_person_time'] = $goodsInfo['total_person_time']; // 商品的总人次
                    $cartItem['goods_remain_person_time'] = $goodsInfo['remain_person_time']; // 商品的剩余人次
                    $cartItem['goods_value'] = $goodsInfo['price']; // 商品价值
                    $cartItem['lottery_prize_id'] = $goodsInfo['lottery_prize_id']; // 云购奖品ID
                    $cartItem['goods_type'] = 1;
                    
                    // 总商品金额
                    if (! isset($list[$store_id]['goods_amount'])) {
                        $list[$store_id]['goods_amount'] = 0.00;
                    }
                    $list[$store_id]['goods_amount'] += ($cartItem['goods_price'] * $cartItem['goods_num']);
                    // 总运费金额
                    $list[$store_id]['shipping_fee'] = 0.00;
                    // 商品列表
                    $list[$store_id]['goods_list'][$goods_id] = array(
                        'cartItem' => $cartItem,
                        'goodsInfo' => $goodsInfo
                    );
                }
            }
            if (empty($list)) {
                echo ($this->error(- 2, '购物车为空'));
                return false;
            }
            
            // 以下进行订单的处理
            try {
                $this->modelOrderCart->begin();
                $pay_sn = $this->modelOrderPay->makePaySn();
                $orderList = array();
                // 根据每个店铺处理以下逻辑
                foreach ($list as $store_id => $item) {
                    // 生成订单
                    $order_sn = $this->modelOrder->makeOrderSn();
                    $storeInfo = array();
                    $orderInfo = $this->modelOrder->create($pay_sn, $order_sn, $storeInfo, $buyerInfo, $pay_name, $item['goods_amount'], 0, 0, 0, $item['shipping_fee'], 0, $order_from);
                    if (empty($orderInfo)) {
                        throw new \Exception('订单保存失败[未生成订单数据]');
                    }
                    $orderInfo['order_id'] = $orderInfo['_id'];
                    $order_id = $orderInfo['order_id'];
                    $orderList[$order_id] = $orderInfo;
                    
                    // 生成订单扩展信息
                    // 收货人信息
                    $consigneeInfo = array();
                    // 发票信息
                    $invoiceInfo = array();
                    // 促销信息
                    $promotionInfo = array();
                    // 代金券
                    $voucherInfo = array();
                    $orderCommonInfo = $this->modelOrderCommon->create($orderInfo, $storeInfo, $consigneeInfo, $invoiceInfo, $promotionInfo, $voucherInfo, $order_message);
                    if (empty($orderCommonInfo)) {
                        throw new \Exception('订单保存失败[未生成订单扩展数据]');
                    }
                    $orderCommonInfo['order_common_id'] = $orderCommonInfo['_id'];
                    
                    // 生成订单商品信息
                    $goodsList = $item['goods_list'];
                    foreach ($goodsList as $goods_id => $goodsItem) {
                        $cartGoodsInfo = $goodsItem['cartItem'];
                        $commis_rate = 0.00;
                        $promotion_rate = 0.00;
                        $orderGoodsInfo = $this->modelOrderGoods->create($buyerInfo, $orderInfo, $storeInfo, $cartGoodsInfo, $commis_rate, $promotion_rate);
                        if (empty($orderGoodsInfo)) {
                            throw new \Exception('订单保存失败[未生成商品数据]');
                        }
                        $orderGoodsInfo['order_goods_id'] = $orderGoodsInfo['_id'];
                    }
                }
                $this->modelOrderCart->commit();
            } catch (\Exception $e) {
                $this->modelOrderCart->rollback();
                throw $e;
            }
            echo ($this->result("OK", $pay_sn));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}

