<?php

/**
 * 会员管理第三方发券接口文档
 */
class EvcardMmp
{
    
    // API接口
    private $gateway = "";
    
    // 应用Key，对应开发者账号中的app_key
    private $app_key = '';
    
    // 应用密钥，app_secret
    private $app_secret = '';
    
    // 是否正式环境
    private $is_product = true;

    public function __construct($app_key, $app_secret, $is_product = true)
    {
        $this->app_key = $app_key;
        $this->app_secret = $app_secret;
        $this->is_product = $is_product;
        
        // 测试环境
        if (! $this->is_product) {
            // API接口域名测试
            $this->gateway = 'http://csms-test.evcard.vip/evcard-mmp';
        } else {
            // API接口域名正式
            $this->gateway = 'http://csms.evcard.vip/evcard-mmp';
        }
        
        // $appKey = "cocacolatest";
        // $secret = "dc283494-b999-47b1-af4d-316cebb56d66";
    }

    /**
     * 请求调用api
     * data:业务数据
     *
     * @return bool
     */
    protected function makeRequest($url, $data)
    {
        $resp = $this->getHttpRequestWithPost($url, json_encode($data));
        return $this->parseResponseData($resp);
    }

    /**
     * 签名生成signature
     */
    protected function _sign()
    {
        $timestamp = time() . "000";
        // 加密顺序（appKey+ timeStamp +secret）
        $sn = md5($this->app_key . $timestamp . $this->app_secret);
        $sign = array(
            'timestamp' => $timestamp,
            'sn' => $sn
        );
        return $sign;
    }

    /**
     * 发送请求,POST
     *
     * @param $url 指定URL完整路径地址            
     * @param $data 请求的数据            
     */
    protected function getHttpRequestWithPost($url, $data)
    {
        // json
        $headers = array(
            'Content-Type: application/json'
        );
        // 获取签名
        $signArr = $this->_sign();
        $headers[] = "timestamp: {$signArr['timestamp']}";
        $headers[] = "sn: {$signArr['sn']}";
		
		//print_r($headers);
		//die($data);
		
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $resp = curl_exec($curl);
        // var_dump( curl_error($curl) );//如果在执行curl的过程中出现异常，可以打开此开关查看异常内容。
        $info = curl_getinfo($curl);
        curl_close($curl);
        if (isset($info['http_code']) && $info['http_code'] == 200) {
            return $resp;
        }
        return '';
    }

    /**
     * 解析响应数据
     *
     * @param $arr返回的数据 响应数据格式：{"status":"success","result":{},"code":0,"msg":"成功"}            
     */
    protected function parseResponseData($arr)
    {
        if (empty($arr)) {
            throw new \Exception('网络请求失败,响应数据为空');
        }
        $ret = json_decode($arr, true);
        if (empty($ret)) {
            throw new \Exception('网络请求失败,无法解析响应数据');
        }
        print_r($ret);
		die;
        if (! empty($ret['code'])) {
            throw new \Exception($ret['msg'], $ret['code']);
        }
        return $ret;
    }

    /**
     * EVcard第三方发券-新组合券发放接口 新
     * ## 接口详情
     *
     * > 服务地址：
     * 测试环境：
     * http://csms-test.evcard.vip/evcard-mmp/api/thirdCoupon/offerThirdCoupon
     * 正式环境：
     * http://csms.evcard.vip/evcard-mmp/api/thirdCoupon/offerThirdCoupon
     * > HTTP Method：POST
     *
     * • 请求path参数：
     * Header中的参数
     * 参数名|类型|必须|说明
     * ---|---|---|---
     * timestamp|String|是|调用时的时间轴（毫秒）
     * sn|String|是|md5加密后的值，加密顺序（appKey+ timeStamp +secret）,appKey渠道key ，secret为渠道key生成后的secret
     *
     * • 请求body参数：
     * 参数名|类型|必须|说明
     * ---|---|---|---
     * activityId|String|是|加密后的活动id(详见活动详情界面)|
     * mobilePhone| String |是|手机号|
     * validSmsFlag| String |否|是否需要验证短信验证码标志，需要验证传字符串“1”|
     * smsCode| String |否|短信验证码，validSmsFlag 传“1”的时候必传|
     * couponVals| String |是|优惠券模板发放组合，传入json数组对象类型如：“[{'couponSeq':1001,'offerNum':1},{'couponSeq':1002,'offerNum':2}]” 其中 couponSeq |Integer|是|优惠券模板记录id |offerNum |Integer|是|发放券数量|
     * 请悉知：couponSeq原传入的是优惠券模板编号 现修改为传入值为 优惠券模板记录id，此优惠券模板记录id取活动模板配置列表中 模板记录id 一栏。。参数名等都不作变化
     *
     * • Response（返回结果）json
     *
     * 参数名 | 明细 | 类型 | 含义 | 说明
     * ---|---|---|---|---
     * code | | String | 状态码 | 0:成功 0以外失败（参照《附1：状态码定义（全局）》） |
     * message | | String | 返回信息 | "OK":成功 |
     *
     * {
     * "code": "0",
     * "message": "优惠券已发放",
     * "data": null
     * }
     */
    public function offerThirdCoupon($activityId, $mobilePhone, array $couponVals, $validSmsFlag = "", $smsCode = "")
    {
        $url = $this->gateway . "/api/thirdCoupon/offerThirdCoupon";
        
        $data = array();
        
        // activityId|String|是|加密后的活动id(详见活动详情界面)|
        $data['activityId'] = $activityId;
        // mobilePhone| String |是|手机号|
        $data['mobilePhone'] = $mobilePhone;
        // validSmsFlag| String |否|是否需要验证短信验证码标志，需要验证传字符串“1”|
        if (! empty($validSmsFlag)) {
            $data['validSmsFlag'] = $validSmsFlag;
        }
        // smsCode| String |否|短信验证码，validSmsFlag 传“1”的时候必传|
        if (! empty($smsCode)) {
            $data['smsCode'] = $smsCode;
        }
        // couponVals| String |是|优惠券模板发放组合，传入json数组对象类型如：“[{'couponSeq':1001,'offerNum':1},{'couponSeq':1002,'offerNum':2}]” 其中 couponSeq |Integer|是|优惠券模板记录id |offerNum |Integer|是|发放券数量|
        // 请悉知：couponSeq原传入的是优惠券模板编号 现修改为传入值为 优惠券模板记录id，此优惠券模板记录id取活动模板配置列表中 模板记录id 一栏。。参数名等都不作变化
        $data['couponVals'] = json_encode($couponVals);
        
        // print_r($data);
        // die('xxx');
        
        // 请求接口
        $ret = $this->makeRequest($url, $data);
        return $ret;
    }
    
    // 只发一张优惠券
    public function offerSingleThirdCoupon($activityId, $mobilePhone, $couponSeq, $validSmsFlag = "", $smsCode = "")
    {
        $couponVals = array(
            array(
                'couponSeq' => $couponSeq,
                'offerNum' => 1
            )
        );
        return $this->offerThirdCoupon($activityId, $mobilePhone, $couponVals, $validSmsFlag, $smsCode);
    }
}