<?php

/**
 * 享道出行api-业务平台车展活动V1.1
 * 业务平台API使用文档 V0.1 2019-01-28
 */
class Saicmobility
{
    
    // API接口
    private $gateway = "";
    
    // 应用Key，对应开发者账号中的appId
    private $appId = 0;
    
    // 应用密钥，app_secret
    private $app_secret = '';
    
    // 是否正式环境
    private $is_product = true;

    private $v = 1;

    private $format = 'json';

    private $signAlgorithm = 'md5';

    public function __construct($appId, $app_secret, $is_product = true)
    {
        $this->appId = $appId;
        $this->app_secret = $app_secret;
        $this->is_product = $is_product;
        
        // 测试环境
        if (! $this->is_product) {
            // API接口域名测试
            $this->gateway = 'http://api-test.saicmobility.com:10080/auth/v2';
        } else {
            // API接口域名正式
            $this->gateway = 'https://api.saicmobility.com/auth/v2';
        }
    }

    /**
     * 请求调用api
     * data:业务数据
     *
     * @return bool
     */
    protected function makeRequest($url, $data)
    {
        $reqParams = $this->bulidRequestParams($data);
        $resp = $this->getHttpRequestWithPost($url, json_encode($reqParams));
        return $this->parseResponseData($resp);
    }

    /**
     * 构造请求数据
     * data:业务参数
     */
    protected function bulidRequestParams($data)
    {
        $json = json_encode($data);
        
        // format ：（指定传输 方式）：固定使用json
        $data['format'] = $this->format;
        // version ： (版本号)：现在API默认版本为1
        $data['version'] = $this->v;
        // signAlgorithm（指定加签方式）:使用md5
        $data['signAlgorithm'] = $this->signAlgorithm;
        // timestamp：生成数据时间
        $data['timestamp'] = "20190130112315";
        // $data['timestamp'] = date("YmdHis", time());
        // sign ：md5生成的数字摘要
        $data['sign'] = $this->_sign($json, $data);
        
        return $data;
    }

    /**
     * 签名生成signature
     */
    protected function _sign($json, $data)
    {
        // 1.升序排序
        // ksort($data);
        
        // 2.字符串拼接
        $args = "json={$json}format={$data['format']}timestamp={$data['timestamp']}signAlgorithm={$data['signAlgorithm']}version={$data['version']}{$this->app_secret}";
        
        // 3.MD5签名,转为大写
        $sign = strtoupper(md5($args));
        
        // die($args . "<br>" . $sign);
        
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
        
        // print_r($headers);
        // print_r(json_decode($data, true));
        // die($data);
        
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
        die();
        if (! empty($ret['errCode'])) {
            throw new \Exception($ret['errMsg'], $ret['errCode']);
        }
        return $ret;
    }

    /**
     * 券发放
     * 环境参数
     * 参数名称 环境 参数值 时间 备注
     * 生产 https://api.saicmobility.com/auth/v2/activity/gtwtrustsendcoupon https协议
     * 测试 api-test.saicmobility.com:10080/auth/v2/activity/gtwtrustsendcoupon 使用http协议
     *
     * 1） 请求参数准备
     * http header里面的参数如下：
     *
     * http body里面的参数设定如下：
     *
     *
     * 参数说明：
     * appId: 应用id 1040001
     * mobile:手机号 13032124712
     * source:活动 CarExhibition201902
     * commercialId:商户号 saicgroup
     * sign ：md5生成的数字摘要
     * signAlgorithm（指定加签方式）:使用md5
     * timestamp：生成数据时间
     * version ： (版本号)：现在API默认版本为1
     * format ：（指定传输 方式）：固定使用json
     * openId：微信openId
     * 3）签名算法规则说明：
     *
     * 摘要生成代码实例：
     *
     * 2） 出参
     * 失败：
     * errCode 不等于0请求失败，errCode 错误码，errMsg 错误信息
     * 例：
     * {
     * "data": {
     * "rewardType": 0
     * },
     * "errCode": -200002,
     * "errMsg": "手机号不能为空"
     * }
     * 成功：
     * errCode 等于0请求成功，
     * rewardType 奖励类型(1-注册奖励，2-登录奖励，3-已领奖励)
     * {
     * "data": {
     * "rewardType": 3
     * },
     * "errCode": 0
     * }
     */
    public function gtwtrustSendCoupon($mobile, $source, $commercialId, $openId)
    {
        $url = $this->gateway . "/activity/gtwtrustsendcoupon";
        
        $data = array();
        // appId: 应用id 1040001
        $data['appId'] = intval($this->appId);
        // mobile:手机号 13032124712
        $data['mobile'] = $mobile;
        // source:活动 CarExhibition201902
        $data['source'] = $source;
        // commercialId:商户号 saicgroup
        $data['commercialId'] = $commercialId;
        // openId：微信openId
        $data['openId'] = $openId;
        // print_r($data);
        // die('xxx');
        
        // 请求接口
        $ret = $this->makeRequest($url, $data);
        return $ret;
    }
}