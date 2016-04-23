<?php
namespace App\Order\Controllers;

/**
 * 微信支付服务
 *
 * 用户在成功完成支付后，微信后台通知（post）商户服务器（notify_url）支付结果。
 * 商户可以使用notify_url 的通知结果进行个性化页面的展示。
 * 对后台通知交互时，如果微信收到商户的应答不是success 或超时，微信认为通知失败，
 * 微信会通过一定的策略（如30 分钟共8 次）定期重新发起通知，尽可能提高通知的成功率,
 * 但微信不保证通知最终能成功。
 * 由于存在重新发送后台通知的情况，因此同样的通知可能会多次发送给商户系统。
 * 商户系统必须能够正确处理重复的通知。
 * 微信推荐的做法是，当收到通知进行处理时，首先检查对应业务数据的状态，
 * 判断该通知是否已经处理过，如果没有处理过再进行处理，如果处理过直接返回success。
 * 在对业务数据进行状态检查和处理之前，要采用数据锁进行并发控制，以避免函数重入造成的数据混乱。
 * 目前补单机制的间隔时间为：8s、10s、10s、30s、30s、60s、120s、360s、1000s。
 *
 * @author Admin
 *        
 */
class WeixinpayController extends ControllerBase
{

    private $modelPaymentNotify = null;

    private $modelOrderPay = null;

    private $serviceWeixinpay = null;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelOrderPay = new \App\Order\Models\Pay();
        $this->modelPaymentNotify = new \App\Payment\Models\Notify();
        $this->serviceWeixinpay = new \App\Payment\Services\Weixinpay();
    }

    /**
     * 购买商品之后的支付通知
     */
    public function goodsAction()
    {
        // 处理微信支付的通知
        $this->serviceWeixinpay->doNotify(array(
            $this,
            'notifyCallBack'
        ));
    }

    /**
     * 预付款充值之后的支付通知
     */
    public function predepositAction()
    {
        // 处理微信支付的通知
        $this->serviceWeixinpay->doNotify(array(
            $this,
            'notifyCallBack'
        ));
    }

    public function notifyCallBack(array $notifyData)
    {
        // 确认商户订单号out_trade_no的有效性
        $out_trade_no = $notifyData['out_trade_no'];
        $orderPayInfo = $this->modelOrderPay->findOne(array(
            '_id' => $out_trade_no,
            '__FOR_UPDATE__' => true
        ));
        
        if (empty($orderPayInfo)) {
            throw new \Exception("$out_trade_no对应的支付数据不存在");
        }
        
        // 进行支付处理
        if ($orderPayInfo['api_pay_state'] == \App\Order\Models\Pay::STATE0) {
            $this->modelOrderPay->changeToPaid($out_trade_no);
        }
        
        // 记录通知的数据
        $this->modelPaymentNotify->recordLog($out_trade_no, $GLOBALS['HTTP_RAW_POST_DATA']);
        
        // 后续的操作由队列处理
        return true;
    }
}