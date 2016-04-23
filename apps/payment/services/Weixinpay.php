<?php
namespace App\Payment\Services;

use App\Payment\Models\Payment;

class Weixinpay
{

    private $modelWeixinApplication = null;

    private $modelPayment = null;

    private $_config = array();

    function __construct()
    {
        $this->modelPayment = new Payment();
        $this->modelWeixinApplication = new \App\Weixin\Models\Application();
        $this->_config = $this->modelPayment->getWeixinpayConfig();
    }

    /**
     * 支付操作处理
     *
     * @return array
     */
    public function nativePay($out_trade_no, $body, $attach, $total_fee, $time_start, $time_expire, $goods_tag, $notify_url, $openid, $product_id)
    {
        // 调用微信统一下单接口，生成预支付交易链接
        
        // PC网页或公众号内支付请传"WEB"
        $device_info = "WEB";
        $nonce_str = \Weixin\Helpers::createNonceStr(32);
        $spbill_create_ip = getIp();
        $trade_type = "NATIVE";
        $weixinApi = $this->getWeixinPayApi();
        $unifiedorderInfo = $weixinApi->unifiedorder($device_info, $nonce_str, $body, $attach, $out_trade_no, $total_fee, $spbill_create_ip, $time_start, $time_expire, $goods_tag, $notify_url, $trade_type, $openid, $product_id);
        return $unifiedorderInfo;
    }

    /**
     * 处理异步支付请求
     */
    public function doNotify($callback)
    {
        $msg = "OK";
        
        // 如果返回成功则验证签名
        try {
            // 获取通知的数据
            $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
            $notifyData = \Weixin\Helpers::xmlToArray($xml);
            $$notifyData = $this->checkSign($notifyData);
            $result = $this->notifyProcess($callback, $notifyData);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $result = false;
        }
        
        if ($result == false) {
            $ret = array();
            $ret['return_code'] = "FAIL";
            $ret['return_msg'] = $msg;
        } else {
            // 该分支在成功回调到NotifyCallBack方法，处理完成之后流程
            $ret = array();
            $ret['return_code'] = "SUCCESS";
            $ret['return_msg'] = "OK";
        }
        $xml = \Weixin\Helpers::arrayToXml($ret);
        echo $xml;
        
        return;
    }

    /**
     * 回调方法入口，子类可重写该方法
     * 注意：
     * 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器
     * 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
     *
     * @param function $callback            
     * @param array $notifyData
     *            回调解释出的参数
     * @return true 回调出来完成不需要继续回调，false 回调处理未完成需要继续回调
     */
    final private function notifyProcess($callback, $notifyData)
    {
        // 用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
        if (! array_key_exists("transaction_id", $notifyData)) {
            throw new \Exception("输入参数不正确");
        }
        // 查询订单，判断订单真实性
        if (! $this->orderquery($notifyData["transaction_id"], $notifyData["out_trade_no"])) {
            throw new \Exception("订单查询失败");
        }
        
        return call_user_func($callback, $notifyData);
    }

    /**
     *
     * @return \Weixin\Pay337
     */
    private function getWeixinPayApi()
    {
        $tokenInfo = $this->modelWeixinApplication->getTokenByAppid($this->_config['appId']);
        $weixinApi = new \Weixin\Pay337();
        $weixinApi->setAccessToken($tokenInfo['access_token']);
        $weixinApi->setAppId($this->_config['appId']);
        $weixinApi->setAppSecret($this->_config['appSecret']);
        $weixinApi->setMchid($this->_config['mchid']);
        $weixinApi->setSubMchId($this->_config['sub_mch_id']);
        $weixinApi->setKey($this->_config['key']);
        $weixinApi->setCert($this->_config['cert']);
        $weixinApi->setCertKey($this->_config['certKey']);
        return $weixinApi;
    }
    
    // 查询订单
    private function orderquery($transaction_id, $out_trade_no)
    {
        try {
            $nonce_str = \Weixin\Helpers::createNonceStr(32);
            $weixinApi = $this->getWeixinPayApi();
            $weixinApi->orderquery($transaction_id, $out_trade_no, $nonce_str);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 检测签名
     */
    private function checkSign($notifyData)
    {
        if ($notifyData['return_code'] == 'FAIL') {
            throw new \Exception($notifyData['return_msg']);
        } else {
            if ($notifyData['result_code'] == 'FAIL') {
                throw new \Exception($notifyData['err_code'] . "." . $notifyData['err_code_des']);
            }
        }
        
        // fix异常
        if (! array_key_exists('sign', $notifyData)) {
            throw new \Exception("签名错误！");
        }
        
        $weixinApi = $this->getWeixinPayApi();
        $sign = $weixinApi->getSign($notifyData);
        if ($notifyData['sign'] != $sign) {
            throw new \Exception("签名错误！");
        }
        return true;
    }
}