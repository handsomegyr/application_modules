<?php
namespace Webcms\Order\Controllers;

/**
 * 预存款服务
 *
 * @author Admin
 *        
 */
class PredepositController extends ControllerBase
{

    private $modelMember = null;

    private $modelOrderPay = null;

    private $servicePayment4Alipay = null;

    private $servicePayment4Weixinpay = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelMember = new \Webcms\Member\Models\Member();
        $this->modelOrderPay = new \Webcms\Order\Models\Pay();
        $this->servicePayment4Alipay = new \Webcms\Payment\Services\Alipay();
        $this->servicePayment4Weixinpay = new \Webcms\Payment\Services\Weixinpay();
    }

    /**
     * 微信支付,生成支付订单的接口
     */
    public function createAction()
    {
        try {
            // http://webcms.didv.cn/order/predeposit/create?predeposit=10&payway=weixin&pay_pwd=xxx
            // 预付款
            $predeposit = intval($this->get('predeposit', '0'));
            // 支付方式
            $payment_code = $payway = ($this->get('payway', ''));
            // 支付密码
            $pay_pwd = ($this->get('pay_pwd', ''));
            if ($predeposit <= 0) {
                echo ($this->error(- 3, '预付款金额不正确'));
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
            
            // 判断是否需要支付密码
            $isPaypwdOk = $this->modelMember->checkPaypwd($buyerInfo,$pay_pwd,$predeposit);
            if (empty($isPaypwdOk)) {
                echo ($this->error(- 4, '支付密码为空或不正确'));
                return false;
            }
            
            $buyerInfo['buyer_id'] = $buyerInfo['_id'];
            $buyerInfo['buyer_name'] = $this->modelMember->getRegisterName($buyerInfo);
            $buyerInfo['buyer_email'] = $buyerInfo['email'];
            $buyerInfo['buyer_mobile'] = $buyerInfo['mobile'];
            $buyerInfo['buyer_avatar'] = $buyerInfo['avatar'];
            $buyerInfo['buyer_register_by'] = $buyerInfo['register_by'];
            
            // 生成订单支付记录
            $pay_sn = $this->modelOrderPay->makePaySn();
            $order_amount = $goods_amount = $predeposit;
            $orderPayInfo = $this->modelOrderPay->create($pay_sn, $buyerInfo, $payment_code, $order_amount, $goods_amount, 0, 0, 0, 0, 0, false, false, 'predeposit');
            if (empty($orderPayInfo)) {
                throw new \Exception('支付订单生成失败', - 5);
            }
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
                        $notify_url = "{$scheme}://{$_SERVER['HTTP_HOST']}/order/weixinpay/predeposit";
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
            
            return true;
        } catch (\Exception $e) {
            echo ($this->error($e->getCode(), $e->getMessage()));
            return false;
        }
    }
}