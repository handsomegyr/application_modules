<?php

/**
 *类名：alipay_service
 *功能：支付宝Wap服务接口控制
 *详细：该页面是请求参数核心处理文件，不需要修改
 *版本：2.0
 *日期：2011-09-01
 '说明：
 '以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 '该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
*/

require_once ("alipay_function.php");
class alipay_service {
	// 线上
	var $gateway_order = "http://wappaygw.alipay.com/service/rest.htm?";
	
	var $_key; // 安全校验码
	var $mysign; // 签名结果
	var $sign_type; // 签名类型 相当于config文件中的sec_id
	var $parameter; // 需要签名的参数数组
	var $format; // 字符编码格式
	var $req_data = ''; // post请求数据
	
	/**
	 * 构造函数
	 */
	function alipay_service() {
	}
	
	/**
	 * 创建alipay.wap.trade.create.direct接口
	 */
	function alipay_wap_trade_create_direct($parameter, $key, $sign_type) {
		$this->_key = $key; // MD5校验码
		$this->sign_type = $sign_type; // 签名类型，此处为MD5
		$this->parameter = para_filter ( $parameter ); // 除去数组中的空值和签名参数
		$this->req_data = $parameter ['req_data'];
		$this->format = $this->parameter ['format']; // 编码格式，此处为utf-8
		$sort_array = arg_sort ( $this->parameter ); // 得到从字母a到z排序后的签名参数数组
		$this->mysign = build_mysign ( $sort_array, $this->_key, $this->sign_type ); // 生成签名
		$this->req_data = create_linkstring ( $this->parameter ) . '&sign=' . urlencode ( $this->mysign ); // 配置post请求数据，注意sign签名需要urlencode
		                                                                                          
		// Post提交请求
		$result = $this->post ( $this->gateway_order );
		
		// 调用GetToken方法，并返回token
		return $this->getToken ( $result );
	}
	
	/**
	 * 调用alipay_Wap_Auth_AuthAndExecute接口
	 */
	function alipay_Wap_Auth_AuthAndExecute($parameter, $key) {
		$this->parameter = para_filter ( $parameter );
		$sort_array = arg_sort ( $this->parameter );
		$this->sign_type = $this->parameter ['sec_id'];
		$this->_key = $key;
		$this->mysign = build_mysign ( $sort_array, $this->_key, $this->sign_type );
		$RedirectUrl = $this->gateway_order . create_linkstring ( $this->parameter ) . '&sign=' . urlencode ( $this->mysign );
		return $RedirectUrl;
		// 跳转至该地址
		//Header ( "Location: $RedirectUrl" );
	}
	
	/**
	 * 返回token参数
	 * 参数 result 需要先urldecode
	 */
	function getToken($result) {
		$result = urldecode ( $result ); // URL转码
		$Arr = explode ( '&', $result ); // 根据 & 符号拆分
		
		$temp = array (); // 临时存放拆分的数组
		$myArray = array (); // 待签名的数组
		                    // 循环构造key、value数组
		for($i = 0; $i < count ( $Arr ); $i ++) {
			$temp = explode ( '=', $Arr [$i], 2 );
			$myArray [$temp [0]] = $temp [1];
		}
		
		$sign = $myArray ['sign']; // 支付宝返回签名
		$myArray = para_filter ( $myArray ); // 拆分完毕后的数组
		
		$sort_array = arg_sort ( $myArray ); // 排序数组
		$this->mysign = build_mysign ( $sort_array, $this->_key, $this->sign_type ); // 构造本地参数签名，用于对比支付宝请求的签名
		
		if ($this->mysign == $sign) 		// 判断签名是否正确
		{
			return getDataForXML ( $myArray ['res_data'], '/direct_trade_create_res/request_token' ); // 返回token
		} else {
			echo ('签名不正确'); // 当判断出签名不正确，请不要验签通过
			return '签名不正确';
		}
	}
	
	/**
	 * PHP Crul库 模拟Post提交至支付宝网关
	 * 如果使用Crul 你需要改一改你的php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
	 * 返回 $data
	 */
	function post($gateway_url) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $gateway_url ); // 配置网关地址
		curl_setopt ( $ch, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_POST, 1 ); // 设置post提交
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $this->req_data ); // post传输数据
		$data = curl_exec ( $ch );
		curl_close ( $ch );
		return $data;
	}
}
