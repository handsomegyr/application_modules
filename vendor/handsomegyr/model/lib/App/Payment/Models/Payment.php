<?php
namespace App\Payment\Models;

class Payment extends \App\Common\Models\Payment\Payment
{

    public function getAlipayConfig()
    {
        $code = 'alipay_direct';
        $list = $this->getAll();
        if (isset($list[$code])) {
            $config = $list[$code]['config'];
            $alipayConfig = array();
            $alipayConfig['partner'] = $config['partner'];
            $alipayConfig['key'] = $config['key'];
            $alipayConfig['seller_email'] = $config['seller_email'];
            $alipayConfig['sign_type'] = 'MD5';
            $alipayConfig['input_charset'] = 'utf-8';
            $alipayConfig['cacert'] = APP_PAY_PATH . 'Alipay/cacert.pem';
            $alipayConfig['transport'] = 'http';
            return $alipayConfig;
        } else {
            throw new \Exception("{$code}所对应的配置信息不存在");
        }
    }

    public function getWeixinpayConfig()
    {
        $code = 'weixinpay';
        $list = $this->getAll();
        if (isset($list[$code])) {
            $config = $list[$code]['config'];
            $weixinpayConfig = array();
            $weixinpayConfig['appId'] = $config['appId'];
            $weixinpayConfig['appSecret'] = $config['appSecret'];
            $weixinpayConfig['mchid'] = $config['mchid'];
            $weixinpayConfig['sub_mch_id'] = $config['sub_mch_id'];
            $weixinpayConfig['key'] = $config['key'];
            $weixinpayConfig['cert'] = APP_PAY_PATH . 'weixinpay/apiclient_cert.pem';
            $weixinpayConfig['certKey'] = APP_PAY_PATH . 'weixinpay/apiclient_key.pem';
            $weixinpayConfig['sign_type'] = 'MD5';
            $weixinpayConfig['input_charset'] = 'utf-8';
            return $weixinpayConfig;
        } else {
            throw new \Exception("{$code}所对应的配置信息不存在");
        }
    }

    public function getAll()
    {
        $cacheKey = cacheKey(__FILE__, __CLASS__, __METHOD__);
        $cache = $this->getDI()->get('cache');
        $list = $cache->get($cacheKey);
        if (empty($list)) {
            $query = array();
            $ret = $this->findAll($query);
            $list = array();
            if (! empty($ret)) {
                foreach ($ret as $item) {
                    $list[$item['code']] = $item;
                }
            }
            if (! empty($list)) {
                $cache->save($cacheKey, $list, 60 * 60); // 一个小时
            }
        }
        return $list;
    }
}