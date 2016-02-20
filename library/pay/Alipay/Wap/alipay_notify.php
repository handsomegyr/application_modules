<?php
/*
 *类名：alipay_notify
 *功能：付款过程中服务器通知类
 *详细：该页面是通知返回核心处理文件，不需要修改
 *版本：2.0
 *日期：2011-09-01
 *说明：
 *以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 *该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
*/

////////////////////注意/////////////////////////
//调试通知返回时，可查看或改写log日志的写入TXT里的数据，来检查通知返回是否正常
/////////////////////////////////////////////////

require_once("alipay_function.php");

class alipay_notify {
    var $gateway;           //网关地址
    var $_key;			  	//安全校验码
    var $partner;           //合作伙伴ID
    var $sign_type;         //签名方式 系统默认
    var $mysign;            //签名结果
    var $_input_charset;    //字符编码格式

    /**构造函数
	 * 从配置文件中初始化变量
	 * $partner 合作身份者ID
	 * $key 安全校验码
	 * $sign_type 签名类型
	 * $_input_charset 字符编码格式
     */
    function alipay_notify($partner,$key,$sign_type,$_input_charset) {
		$this->gateway = "http://wappaygw.alipay.com/service/rest.htm?";
		
        $this->partner          = $partner;
        $this->_key				= $key;
        $this->mysign           = "";
        $this->sign_type	    = $sign_type;
        $this->_input_charset   = $_input_charset;
    }

    /********************************************************************************/

    /**对notify_url的认证
	 *返回的验证结果：true/false
     */
    function notify_verify() {
		//判断POST来的数组是否为空
		if(empty($_POST)) {
			return false;
		}
		else {
			//此处为固定顺序，支付宝Notify返回消息通知比较特殊，这里不需要升序排列
			$notifyarray = array(
				"service"		=> $_POST['service'],
				"v"				=> $_POST['v'],
				"sec_id"		=> $_POST['sec_id'],
				"notify_data"	=> $_POST['notify_data']
			);

			$this->mysign = build_mysign($notifyarray,$this->_key,$this->sign_type);
			
			//记录日志（调试用）
			//log_result($this->mysign . ' ' . $_POST["sign"]);
	
			//判断veryfy_result是否为ture，生成的签名结果mysign与获得的签名结果sign是否一致
			//mysign与sign不等，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if ($this->mysign == $_POST["sign"]) 
			{
				return true;
			} 
			else 
			{
				return false;
			}
		}
    }

    /********************************************************************************/

    /**对return_url的认证
	 *return 验证结果：true/false
     */
    function return_verify() {
        //判断GET来的数组是否为空
		if(empty($_GET)) {
			return false;
		}
		else {
			$get          = para_filter($_GET);	    //对所有GET反馈回来的数据去空
			$sort_get     = arg_sort($get);		    //对所有GET反馈回来的数据排序
			$this->mysign = build_mysign($sort_get,$this->_key,$this->sign_type);    //生成签名结果
	
			if ($this->mysign == $_GET["sign"]) {            
				return true;
			}else {
				return false;
			}
		}
    }
}
