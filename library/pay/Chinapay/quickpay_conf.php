<?php

/*
 * @file    quickpay_service.inc.php
 * @author  fengmin(felix021@gmail.com)
 * @date    2011-08-22
 * @version $Revision$
 *
 */

class quickpay_conf
{

    public $VERIFY_HTTPS_CERT = false;

    public $timezone        = "Asia/Shanghai"; //时区
    public $sign_method     = "md5"; //摘要算法，目前仅支持md5 (2011-08-22)
    public $security_key;   //商户密钥 
	public $pay_params;     //支付请求预定义字段
	
	public $front_pay_url;
	public $back_pay_url;
	public $query_url;
	/**
	 * 实例化信息
	 * @param string $security_key
	 * @param string $merId
	 * @param string $merAbbr
	 * @param string $hj   环境（cs,ysx,xs）对应（测试环境，预上线环境，线上环境）
	 */
	function quickpay_conf($security_key,$merId,$merAbbr,$hj='xs'){
    //public $security_key    = "0SAUIRHVE7IORTTU389V570E894VNOWI8Y6"; //商户密钥
     $this->security_key    = $security_key; //商户密钥      测试环境
     //$this->security_key    = "88888888"; //商户密钥      测试环境

    //支付请求预定义字段
     $this->pay_params  = array(
        'version'       => '1.0.0',
        'charset'       => 'UTF-8', //UTF-8, GBK等
    	'merId'         => $merId, //商户填写     测试环境
     	//'merId'         => '100000000611221',
        'acqCode'       => '',  //收单机构填写
        'merCode'       => '',  //收单机构填写
        'merAbbr'       => $merAbbr,
     	//'merAbbr'       => 	'国泰广告'
    );
     switch ($hj) {
     	case 'cs':
	     	 //* 测试环境
		     $this->front_pay_url   = "http://58.246.226.99/UpopWeb/api/Pay.action";
		     $this->back_pay_url    = "http://58.246.226.99/UpopWeb/api/BSPay.action";
		     $this->query_url       = "http://58.246.226.99/UpopWeb/api/Query.action";
		    //*/;
     	break;
     	case 'ysx':
     		//* 预上线环境
		     $this->front_pay_url   = "https://www.epay.lxdns.com/UpopWeb/api/Pay.action";
		     $this->back_pay_url    = "https://www.epay.lxdns.com/UpopWeb/api/BSPay.action";
		     $this->query_url       = "https://www.epay.lxdns.com/UpopWeb/api/Query.action";
		    //*;
     	break;
     	case 'xs':
     	default:
     		//* 线上环境
		     $this->front_pay_url   = "https://unionpaysecure.com/api/Pay.action";
		     $this->back_pay_url    = "https://besvr.unionpaysecure.com/api/BSPay.action";
		     $this->query_url       = "https://query.unionpaysecure.com/api/Query.action";
		    //*;
     	break;
     }
   
	}
    
    
    public $FRONT_PAY = 1;
    public $BACK_PAY  = 2;
    public $RESPONSE  = 3;
    public $QUERY     = 4;

    public $CONSUME                = "01";
    public $CONSUME_VOID           = "31";
    public $PRE_AUTH               = "02";
    public $PRE_AUTH_VOID          = "32";
    public $PRE_AUTH_COMPLETE      = "03";
    public $PRE_AUTH_VOID_COMPLETE = "33";
    public $REFUND                 = "04";
    public $REGISTRATION           = "71";

    public $CURRENCY_CNY      = "156";

    //支付请求可为空字段（但必须填写）
    public $pay_params_empty = array(
        "origQid"           => "",
        "acqCode"           => "",
        "merCode"           => "",
        "commodityUrl"      => "",
        "commodityName"     => "",
        "commodityUnitPrice"=> "",
        "commodityQuantity" => "",
        "commodityDiscount" => "",
        "transferFee"       => "",
        "customerName"      => "",
        "defaultPayType"    => "",
        "defaultBankNumber" => "",
        "transTimeout"      => "",
        "merReserved"       => "",
    );

    //支付请求必填字段检查
    public $pay_params_check = array(
        "version",
        "charset",
        "transType",
        "origQid",
        "merId",
        "merAbbr",
        "acqCode",
        "merCode",
        "commodityUrl",
        "commodityName",
        "commodityUnitPrice",
        "commodityQuantity",
        "commodityDiscount",
        "transferFee",
        "orderNumber",
        "orderAmount",
        "orderCurrency",
        "orderTime",
        "customerIp",
        "customerName",
        "defaultPayType",
        "defaultBankNumber",
        "transTimeout",
        "frontEndUrl",
        "backEndUrl",
        "merReserved",
    );

    //查询请求必填字段检查
    public $query_params_check = array(
        "version",
        "charset",
        "transType",
        "merId",
        "orderNumber",
        "orderTime",
        "merReserved",
    );

    //商户保留域可能包含的字段
    public $mer_params_reserved = array(
    //  NEW NAME            OLD NAME
        "cardNumber",       "pan",
        "cardPasswd",       "password",
        "credentialType",   "idType",
        "cardCvn2",         "cvn",
        "cardExpire",       "expire",
        "credentialNumber", "idNo",
        "credentialName",   "name",
        "phoneNumber",      "mobile",
        "merAbstract",

        //tdb only
        "orderTimeoutDate",
        "origOrderNumber",
        "origOrderTime",
    );

    public $notify_param_check = array(
        "version",
        "charset",
        "transType",
        "respCode",
        "respMsg",
        "respTime",
        "merId",
        "merAbbr",
        "orderNumber",
        "traceNumber",
        "traceTime",
        "qid",
        "orderAmount",
        "orderCurrency",
        "settleAmount",
        "settleCurrency",
        "settleDate",
        "exchangeRate",
        "exchangeDate",
        "cupReserved",
        "signMethod",
        "signature",
    );

    public $sign_ignore_params = array(
        "bank",
    );
}

?>
