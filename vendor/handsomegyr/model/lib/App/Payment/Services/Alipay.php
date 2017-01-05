<?php
namespace App\Payment\Services;

use App\Payment\Models\Payment;

class Alipay
{

    private $modelPayment = null;

    private $_alipayConfig = array();

    function __construct()
    {
        include_once APP_PAY_PATH . 'Alipay/Direct/alipay_notify.class.php';
        include_once APP_PAY_PATH . 'Alipay/Direct/alipay_submit.class.php';
        $this->modelPayment = new Payment();
        $this->_alipayConfig = $this->modelPayment->getAlipayConfig();
    }

    /**
     * 支付宝支付操作处理
     *
     * @param string $out_trade_no
     *            外部订单号
     * @param string $subject
     *            商品标题
     * @param double $total_fee
     *            总金额
     * @param string $body
     *            详细内容信息
     * @param string $show_url
     *            产品展示链接
     * @return string
     */
    public function directPay($out_trade_no, $subject, $total_fee, $body, $show_url, $notify_url, $return_url)
    {
        $total_fee = showPrice($total_fee, 2);
        
        $this->params = array(
            'service' => 'create_direct_pay_by_user',
            'paymethod' => 'directPay',
            'enable_paymethod' => 'directPay^bankPay^cartoon^cash',
            'partner' => $this->_alipayConfig['partner'],
            'payment_type' => 1,
            'notify_url' => $notify_url,
            'return_url' => $return_url,
            'seller_email' => $this->_alipayConfig['seller_email'], // anny.wang@chinadistributionltd.com
            'out_trade_no' => $out_trade_no,
            'subject' => $subject,
            'total_fee' => $total_fee,
            'body' => $body,
            'show_url' => $show_url,
            'anti_phishing_key' => '',
            'exter_invoke_ip' => '',
            'extra_common_param' => '',
            '_input_charset' => $this->_alipayConfig['input_charset']
        );
        
        $alipaySubmit = new AlipaySubmit($this->_alipayConfig);
        $payUrl = $alipaySubmit->alipay_gateway_new . $alipaySubmit->buildRequestParaToString($this->params);
        return $payUrl;
    }

    /**
     * 处理支付宝的异步支付请求
     */
    public function doNotify()
    {
        $alipayNotify = new AlipayNotify($this->_alipayConfig);
        $verify_result = $alipayNotify->verifyNotify();
        
        if ($verify_result) {
            $out_trade_no = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
            $trade_no = isset($_POST['trade_no']) ? $_POST['trade_no'] : ''; // 支付宝交易号
            $trade_status = isset($_POST['trade_status']) ? $_POST['trade_status'] : ''; // 交易状态
            $buyer_email = isset($_POST['buyer_email']) ? $_POST['buyer_email'] : '';
            
            if ($trade_status == 'TRADE_FINISHED') {} else 
                if ($trade_status == 'TRADE_SUCCESS') {
                    $this->notifyCallback($out_trade_no, $trade_status);
                }
            
            echo 'success';
        } else {
            echo 'fail';
        }
    }

    /**
     * 处理支付宝的同步支付请求
     */
    public function doReturn()
    {
        $alipayNotify = new AlipayNotify($this->_alipayConfig);
        $verify_result = $alipayNotify->verifyReturn();
        
        if ($verify_result) {
            $out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
            $trade_no = isset($_GET['trade_no']) ? $_GET['trade_no'] : '';
            $trade_status = isset($_GET['trade_status']) ? $_GET['trade_status'] : '';
            $buyer_email = isset($_GET['buyer_email']) ? $_GET['buyer_email'] : '';
            
            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                $this->returnCallback($out_trade_no, $trade_status);
            } else {
                echo "trade_status=" . $trade_status;
            }
        } else {
            echo 'verify fail';
        }
    }

    /**
     *
     * @param string $out_trade_no            
     * @param string $trade_status            
     */
    private function returnCallback($out_trade_no, $trade_status)
    {
        $url = $this->_projectInfo['callback'];
        if (strpos($url, '?') === false)
            $url .= '?';
        
        $params = array(
            'out_trade_no' => $out_trade_no,
            'trade_status' => $trade_status,
            'sign' => $this->sign($out_trade_no)
        );
        
        $url .= '&' . http_build_query($params);
        header('location:' . $url);
        exit();
    }

    /**
     * 异步通知处理
     *
     * @param string $out_trade_no            
     * @param string $trade_status            
     */
    private function notifyCallback($out_trade_no, $trade_status)
    {
        try {
            $params = array(
                'out_trade_no' => $out_trade_no,
                'trade_status' => $trade_status,
                'sign' => $this->sign($out_trade_no)
            );
            
            doGet($this->_callbackUrl, $params);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 签名算法
     *
     * @param string $out_trade_no            
     * @return string
     */
    private function sign($out_trade_no)
    {
        return md5($out_trade_no);
    }
}