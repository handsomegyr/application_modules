<?php
namespace Webcms\Order\Controllers;

/**
 * 支付服务
 *
 * @author Admin
 *        
 */
class PayController extends ControllerBase
{

    private $modelMember = null;

    private $modelOrder = null;

    private $modelOrderPay = null;

    private $modelOrderGoods = null;

    private $modelOrderStatistics = null;

    private $modelPointsUser = null;

    private $servicePayment4Alipay = null;

    private $servicePayment4Weixinpay = null;

    private $servicePay = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        
        $this->modelMember = new \Webcms\Member\Models\Member();
        $this->modelOrder = new \Webcms\Order\Models\Order();
        $this->modelOrderPay = new \Webcms\Order\Models\Pay();
        $this->modelOrderStatistics = new \Webcms\Order\Models\Statistics();
        $this->modelOrderGoods = new \Webcms\Order\Models\Goods();
        $this->modelPointsUser = new \Webcms\Points\Models\User();
        $this->servicePay = new \Webcms\Order\Services\Pay();
        $this->servicePayment4Alipay = new \Webcms\Payment\Services\Alipay();
        $this->servicePayment4Weixinpay = new \Webcms\Payment\Services\Weixinpay();
        $this->servicePay = new \Webcms\Order\Services\Pay();
    }

    /**
     * 生成支付订单的接口
     */
    public function createAction()
    {
        try {
            // http://webcms.didv.cn/order/pay/create?pay_sn=567e1fd8887c22034a8b45a3&integral=0&predeposit=1&payway=weixin&pay_pwd=xxxx
            // 订单支付单号
            $pay_sn = trim($this->get('pay_sn', ''));
            // 是否使用福分
            $integral = intval($this->get('integral', '0'));
            // 是否使用预付款
            $predeposit = intval($this->get('predeposit', '0'));
            // 支付方式
            $payment_code = $payway = ($this->get('payway', ''));
            // 支付密码
            $pay_pwd = ($this->get('pay_pwd', ''));
            
            if (empty($pay_sn)) {
                echo ($this->error(- 3, '订单支付单号为空'));
                return false;
            }
            
            // 买家信息
            $buyer_id = $_SESSION['member_id'];
            if (empty($buyer_id)) {
                echo ($this->error(- 1, '购买者为空'));
                return false;
            }
            // 获取会员信息
            $buyerInfo = $this->modelMember->getInfoById($buyer_id);
            if (empty($buyerInfo)) {
                echo ($this->error(- 2, '购买者不存在'));
                return false;
            }
            $buyerInfo['buyer_id'] = $buyerInfo['_id'];
            $buyerInfo['buyer_name'] = $this->modelMember->getRegisterName($buyerInfo);
            $buyerInfo['buyer_email'] = $buyerInfo['email'];
            $buyerInfo['buyer_mobile'] = $buyerInfo['mobile'];
            $buyerInfo['buyer_avatar'] = $buyerInfo['avatar'];
            $buyerInfo['buyer_register_by'] = $buyerInfo['register_by'];
            
            // 获取订单列表信息
            $orderList = $this->modelOrder->getListByPaySn($pay_sn, $buyer_id);
            if (empty($orderList)) {
                echo ($this->error(- 4, '订单不存在'));
                return false;
            }
            // 福分金额
            $init_points_amount = 0.00;
            $is_points_used = false;
            // 预付款金额
            $init_pd_amount = 0.00;
            $is_pd_used = false;
            // // 检查福分
            // if (! empty($integral)) {
            // $pointInfo = $this->modelPointsUser->getInfoByUserId($buyer_id, POINTS_CATEGORY1); // 福分
            // if (empty($pointInfo)) {
            // echo ($this->error(- 5, '福分不够'));
            // return false;
            // }
            // $currentIntegral = ($pointInfo['current'] - $pointInfo['current'] % 100);
            // if (($pointInfo['current'] < 100)) {
            // echo ($this->error(- 5, '福分不够'));
            // return false;
            // }
            // $init_points_amount = $currentIntegral / 100;
            // }
            
            // 检查预存款
            if (! empty($predeposit)) {
                $predepositInfo = $this->modelPointsUser->getInfoByUserId($buyer_id, POINTS_CATEGORY3); // 预付款
                if (empty($predepositInfo)) {
                    echo ($this->error(- 6, '预存款金额不够'));
                    return false;
                }
                if (($predepositInfo['current'] < 1)) {
                    echo ($this->error(- 6, '预存款金额不够'));
                    return false;
                }
                $init_pd_amount = $predepositInfo['current'];
                $is_pd_used = true;
            }
            
            // 计算支付金额
            $amounts = $this->modelOrderPay->calculateAmounts($orderList, 0.00, 0.00, $init_pd_amount, $init_points_amount, 0.00, $is_points_used, $is_pd_used);
            
            // 判断是否需要支付密码
            $isPaypwdOk = $this->modelMember->checkPaypwd($buyerInfo, $pay_pwd, $amounts['pay_amount']);
            if (empty($isPaypwdOk)) {
                echo ($this->error(- 4, '支付密码为空或不正确'));
                return false;
            }
            
            // 生成订单支付记录
            $orderPayInfo = $this->modelOrderPay->create($pay_sn, $buyerInfo, $payment_code, $amounts['order_amount'], $amounts['goods_amount'], $amounts['rcb_amount'], $amounts['pd_amount'], $amounts['points_amount'], $amounts['shipping_fee'], $amounts['refund_amount'], $is_points_used, $is_pd_used);
            
            if (empty($orderPayInfo)) {
                throw new \Exception('支付订单生成失败', - 5);
            }
            $orderPayInfo['order_pay_id'] = $orderPayInfo['_id'];
            $out_trade_no = $orderPayInfo['_id'];
            $total_fee = $orderPayInfo['pay_amount'];
            $pay_url = "";
            // 如果未支付完成的话
            if ($orderPayInfo['api_pay_state'] != \Webcms\Order\Models\Pay::STATE1) {
                $pay_state = false;
                // 如果是微信支付的时候
                if ($payment_code == 'weixin') {
                    $pay_url = 'yungou/cart/weixinpay';
                    if (false) {
                        $body = "一元云购";
                        $attach = "";
                        $time_start = date("YmdHis", $orderPayInfo['__CREATE_TIME__']->sec);
                        $time_expire = date("YmdHis", $orderPayInfo['__CREATE_TIME__']->sec + 3600 * 2);
                        $goods_tag = "";
                        $scheme = $this->getRequest()->getScheme();
                        $notify_url = "{$scheme}://{$_SERVER['HTTP_HOST']}/order/weixinpay/goods";
                        $product_id = $orderPayInfo['pay_sn'];
                        $openid = "";
                        $unifiedorderInfo = $this->servicePayment4Weixinpay->nativePay($out_trade_no, $body, $attach, $total_fee, $time_start, $time_expire, $goods_tag, $notify_url, $openid, $product_id);
                    } else {
                        $out_trade_no = $orderPayInfo['_id'];
                        $unifiedorderInfo = array(
                            'prepay_id' => 'xxxxxxxx',
                            'code_url' => 'http://www.baidu.com/'
                        );
                    }
                    // 记录微信统一下单接口的结果
                    $this->modelOrderPay->recordWeixinUnifiedorderInfo($orderPayInfo['_id'], $unifiedorderInfo);
                } elseif ($payment_code == 'alipay') {
                    $subject = "一元云购";
                    $show_url = "";
                    $body = "一元云购";
                    $scheme = $this->getRequest()->getScheme();
                    $notify_url = "{$scheme}://{$_SERVER['HTTP_HOST']}/order/weixinpay/goods";
                    $return_url = "{$scheme}://{$_SERVER['HTTP_HOST']}/order/weixinpay/goods";
                    $pay_url = $this->servicePayment4Alipay->directPay($out_trade_no, $subject, $total_fee, $body, $show_url, $notify_url, $return_url);
                }
            } else {
                $pay_state = true;
                // 如果支付完成的话，支付处理
                // 将该支付单号入队列处理???
                // $this->servicePay->finishPay($out_trade_no);
            }
            
            echo ($this->result("OK", array(
                'out_trade_no' => $out_trade_no,
                'pay_state' => false,
                'pay_url' => $pay_url
            )));
            
            //
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    public function finishAction()
    {
        try {
            // http://webcms.didv.cn/order/pay/finish?out_trade_no=56c18084887c224f7b8b4577
            // 订单支付单号
            $out_trade_no = trim($this->get('out_trade_no', ''));
            $ret = $this->servicePay->finishPay($out_trade_no);
            if (! empty($ret['error_code'])) {
                echo ($this->error($ret['error_code'], $ret['error_msg']));
                return false;
            }
            echo ($this->result("OK", $ret));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }

    /**
     * 轮询获取支付结果的接口
     */
    public function getpayresultAction()
    {
        try {
            // http://webcms.didv.cn/order/pay/getpayresult?id=56640956887c22014a8b457c
            // 支付id
            $id = trim($this->get('id', ''));
            
            if (empty($id)) {
                echo ($this->error(- 1, '支付ID为空'));
                return false;
            }
            
            $orderPayInfo = $this->modelOrderPay->getInfoById($id);
            if (empty($orderPayInfo)) {
                echo ($this->error(- 2, '支付ID不正确'));
                return false;
            }
            
            // 检查是否已支付
            if ($orderPayInfo['api_pay_state'] != \Webcms\Order\Models\Pay::STATE1) {
                echo ($this->error(- 3, '该支付订单还未支付'));
                return false;
            }
            
            // 检查是否处理完成
            if (empty($orderPayInfo['process_state'])) {
                echo ($this->error(- 4, '该支付订单还未处理完成'));
                return false;
            }
            
            echo ($this->result("OK"));
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}

