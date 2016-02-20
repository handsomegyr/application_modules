<?php
namespace Sms\Yunpian;

/**
 * yunpian模版API
 *
 * @author guoyongrong <handsomegyr@gmail.com>
 */
class Template
{

    protected $apikey;

    private $_url = 'http://yunpian.com/v1/tpl/';

    public function __construct($apikey, $options = array())
    {
        $this->apikey = $apikey;
    }

    /**
     * 1、取默认模板
     * URL：http://yunpian.com/v1/tpl/get_default.json
     * 功能说明：用于获取系统提供的默认短信模板。使用默认模板，你无需创建模板便可进行模板短信发送，可以加快你接入的速度。
     * 访问方式：POST
     * 参数：
     * 参数名	类型	是否必须	描述	示例
     * apikey	String	是	用户唯一标识	9b11127a9701975c734b8aee81ee3526
     * tpl_id	Long	否	模板id，64位长整形。指定id时返回id对应的默认
     * 模板。未指定时返回所有默认模板	1
     * 1)指定tpl_id时，调用成功的返回值示例：
     * {
     * "code": 0,
     * "msg": "OK",
     * "template": {
     * "tpl_id": 1,
     * "tpl_content": "【#company#】您的验证码是#code#",
     * "check_status": "SUCCESS",
     * "reason": null
     * }
     * }
     * 2)未指定tpl_id时，调用成功的返回值示例：
     * {
     * "code": 0,
     * "msg": "OK",
     * "template": [{
     * "tpl_id": 1,
     * "tpl_content": "【#company#】您的验证码是#code#",
     * "check_status": "SUCCESS",
     * "reason ": null
     * }, {
     * "tpl_id": 2,
     * "tpl_content": "【#company#】您的验证码是#code#。如非本人操作，请忽略本短信",
     * "check_status": "SUCCESS",
     * "reason ": null
     * }]
     * }
     * 当前系统提供的默认短信模板有：
     * 模板id(tpl_id)	模板内容	模板参数示例(tpl_value)	模板参数值含义
     * 1	【#company#】您的验证码是#code#	#code#=1234&#company#=云片网	code:验证码
     * company:公司名或产品名
     * 2	【#company#】您的验证码是#code#。如非本人操作，请忽略本短信	同上	同上
     * 3	【#company#】亲爱的#name#，您的验证码是#code#。如非本人操作，请忽略本短信	#name#=Jacky&#code#=1234&
     * #company#=云片网	name:会员名，其他同上
     * 4	【#company#】亲爱的#name#，您的验证码是#code#。有效期为#hour#小时，请尽快验证	#name#=苍老师&#code#=1234
     * &#hour#=1&#company#=云片网	hour:时长，其他同上
     * 5	【#company#】感谢您注册#app#，您的验证码是#code#	#app#=云片短信平台&#code#=1234&#company#=云片网	app:产品名或网站名，其他同上
     * 6	【#company#】欢迎使用#app#，您的手机验证码是#code#。本条信息无需回复	同上	同上
     * 7	【#company#】正在找回密码，您的验证码是#code#	#code#=1234&#company#=云片网	同上
     * 8	【#company#】激活码是#code#。如非本人操作，请致电#tel#	#code#=1234&#tel#=400-081-2798#company#=云片网	tel:电话号码，其他同上
     * 9	【#company#】#code#(#app#手机动态码，请完成验证)，如非本人操作，请忽略本短信	#code#=1234&#app#=云片短信平台&#company#=云片网	同上
     */
    public function getDefault($tpl_id = 0)
    {
        $params = array();
        $params['apikey'] = $this->apikey;
        if (! empty($tpl_id)) {
            $params['tpl_id'] = $tpl_id;
        }
        $rst = $this->post($this->_url . 'get_default.json', $params);
        if (! empty($rst['code'])) {
            throw new \Exception($rst['msg'], $rst['code']);
        } else {
            return $rst;
        }
    }

    /**
     * 获取微信服务器信息
     *
     * @param string $url            
     * @param array $params            
     * @return mixed
     */
    public function get($url, $params = array())
    {
        $client = new \Guzzle\Http\Client($this->_url);
        $request = $client->get($url, array(), array(
            'query' => $params
        ));
        $request->getCurlOptions()->set(CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
        $response = $client->send($request);
        if ($response->isSuccessful()) {
            return $response->json();
        } else {
            throw new \Exception("云片短信服务器未有效的响应请求");
        }
    }

    /**
     * 推送消息给到微信服务器
     *
     * @param string $url            
     * @param array $params            
     * @return mixed
     */
    public function post($url, $params = array())
    {
        $client = new \Guzzle\Http\Client($this->_url);
        $client->setDefaultOption('body', $params);
        $request = $client->post($url);
        $request->getCurlOptions()->set(CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
        $response = $client->send($request);
        if ($response->isSuccessful()) {
            return $response->json();
        } else {
            throw new \Exception("云片短信服务器未有效的响应请求");
        }
    }
}
