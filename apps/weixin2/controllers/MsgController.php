<?php

namespace App\Weixin2\Controllers;

/**
 * 消息推送
 * 企业微信消息与事件接收
 */
class MsgController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 1;

    private $modelWeixinopenUser;

    private $modelWeixinopenAuthorizer;

    private $modelWeixinopenMsgLog;

    private $modelWeixinopenReplyMsg;

    /**
     * @var \App\Weixin2\Models\Keyword\Keyword
     */
    private $modelWeixinopenKeyword;

    private $modelWeixinopenKeywordToReplyMsg;

    private $modelWeixinopenKeywordToCustomMsg;

    private $modelWeixinopenKeywordToTemplateMsg;

    /**
     * @var \App\Weixin2\Models\Keyword\Word
     */
    private $modelWeixinopenWord;

    private $modelWeixinopenQrcode;

    /**
     * @var \App\Weixin2\Models\Qrcode\EventLog
     */
    private $modelWeixinopenQrcodeEventLog;

    /** @var  \Weixin\Component */
    private $objWeixinComponent;

    /** @var  \Weixin\Client */
    private $objWeixin;

    private $component_appid;

    private $componentConfig;

    private $authorizer_appid;

    private $authorizerConfig;

    private $weixinopenService;

    // 请求日志信息
    private $requestLogDatas = array();

    // 是否加解密
    private $isNeedDecryptAndEncrypt = TRUE;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->isNeedDecryptAndEncrypt = true;

        $this->modelWeixinopenUser = new \App\Weixin2\Models\User\User();
        $this->modelWeixinopenAuthorizer = new \App\Weixin2\Models\Authorize\Authorizer();
        $this->modelWeixinopenMsgLog = new \App\Weixin2\Models\Msg\Log();
        $this->modelWeixinopenReplyMsg = new \App\Weixin2\Models\ReplyMsg\ReplyMsg();
        $this->modelWeixinopenKeyword = new \App\Weixin2\Models\Keyword\Keyword();
        $this->modelWeixinopenWord = new \App\Weixin2\Models\Keyword\Word();
        $this->modelWeixinopenKeywordToReplyMsg = new \App\Weixin2\Models\Keyword\KeywordToReplyMsg();
        $this->modelWeixinopenKeywordToCustomMsg = new \App\Weixin2\Models\Keyword\KeywordToCustomMsg();
        $this->modelWeixinopenKeywordToTemplateMsg = new \App\Weixin2\Models\Keyword\KeywordToTemplateMsg();
        $this->modelWeixinopenQrcode = new \App\Weixin2\Models\Qrcode\Qrcode();
        $this->modelWeixinopenQrcodeEventLog = new \App\Weixin2\Models\Qrcode\EventLog();
    }

    /**
     * 消息与事件接收URL
     * 处理微信的回调数据
     *
     *
     * * 公众号消息与事件接收URL
     * http://www.myapplicationmodule.com.com/weixinopen/api/msg/$APPID$/callback?appid=wxca8519f703c07d32
     * 处理微信的回调数据
     *
     * 1、在接收已授权公众号消息和事件的URL中，增加2个参数（此前已有2个参数，为时间戳 timestamp，随机数nonce），分别是encrypt_type（加密类型，为aes）和msg_signature（消息体签名，用于验证消息体的正确性）
     * 2、postdata中的XML体，将使用第三方平台申请时的接收消息的加密symmetric_key（也称为EncodingAESKey）来进行加密。
     *
     *
     * @return boolean
     */
    public function callbackAction()
    {
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/$APPID$/callback?appid=wxca8519f703c07d32&authorizer_appid=xxx
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/xxxxxxx/callback?appid=wxca8519f703c07d32&authorizer_appid=xxx
        try {
            /**
             * ==================================================================================
             * ====================================以下逻辑请勿修改===============================
             * ==================================================================================
             */
            // $_GET['authorizer_appid'] = $authorizer_appid;
            $authorizer_appid = $_GET['authorizer_appid'];

            // 消息与事件接收URL
            $this->requestLogDatas['log_type'] = 'msglog';

            // 初始化
            $this->doInitializeLogic();

            $component_appid = $this->component_appid;
            $this->requestLogDatas['component_appid'] = $component_appid;
            $this->requestLogDatas['authorizer_appid'] = $authorizer_appid;

            $onlyRevieve = false;

            $AESInfo = array();
            $AESInfo['api'] = 'callback';
            $AESInfo['component_appid'] = $component_appid;
            $AESInfo['authorizer_appid'] = $authorizer_appid;
            $AESInfo['timestamp'] = isset($_GET['timestamp']) ? trim(strtolower($_GET['timestamp'])) : '';
            $AESInfo['nonce'] = isset($_GET['nonce']) ? $_GET['nonce'] : '';
            $AESInfo['encrypt_type'] = isset($_GET['encrypt_type']) ? $_GET['encrypt_type'] : '';
            $AESInfo['msg_signature'] = isset($_GET['msg_signature']) ? $_GET['msg_signature'] : '';

            // 如果是微信开放平台的话
            if (!empty($this->componentConfig)) {
                $verifyToken = isset($this->componentConfig['verify_token']) ? $this->componentConfig['verify_token'] : '';
                $encodingAESKey = isset($this->componentConfig['EncodingAESKey']) ? $this->componentConfig['EncodingAESKey'] : '';
                $receiveId = $this->component_appid;
                $errorConfig = $this->componentConfig;
            } else {
                $verifyToken = isset($this->authorizerConfig['verify_token']) ? $this->authorizerConfig['verify_token'] : '';
                $encodingAESKey = isset($this->authorizerConfig['EncodingAESKey']) ? $this->authorizerConfig['EncodingAESKey'] : '';
                $receiveId = $this->authorizer_appid;
                $errorConfig = $this->authorizerConfig;
            }
            $AESInfo['EncodingAESKey'] = $encodingAESKey;
            $AESInfo['verify_token'] = $verifyToken;
            $AESInfo['receiveId'] = $receiveId;
            $this->requestLogDatas['aes_info'] = $AESInfo;

            if (empty($verifyToken)) {
                throw new \Exception('application verify_token is null. config:' . \json_encode($errorConfig));
            }

            // 合法性校验
            if (!$this->objWeixin->checkSignature($verifyToken)) {
                $debug = $this->debugVar($_GET, $this->objWeixin->getSignature());
                throw new \Exception('签名错误' . $debug);
            }

            // 签名正确，将接受到的xml转化为数组数据并记录数据
            $datas = $this->getDataFromWeixinServer();
            foreach ($datas as $dtkey => $dtvalue) {
                $this->requestLogDatas[$dtkey] = $dtvalue;
            }
            $this->requestLogDatas['response'] = 'success';

            // 如果是微信开放平台的话
            if (!empty($this->componentConfig)) {
                // 全网发布自动校验
                $verifyComponentRet = $this->verifyComponent($datas);
                if ($verifyComponentRet['is_success']) {
                    return $verifyComponentRet['response'];
                }
            }

            // 开始处理相关的业务逻辑
            $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
            $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
            $content = isset($datas['Content']) ? trim($datas['Content']) : '';
            $__TIME_STAMP__ = time();
            $__SIGN_KEY__ = $this->modelWeixinopenAuthorizer->getSignKey($FromUserName, $this->authorizerConfig['secretKey'], $__TIME_STAMP__);
            $MsgId = isset($datas['MsgId']) ? trim($datas['MsgId']) : '';
            $CreateTime = isset($datas['CreateTime']) ? ($datas['CreateTime']) : time();

            // 关于重试的消息排重，有msgid的消息推荐使用msgid排重。事件类型消息推荐使用FromUserName + CreateTime 排重。
            if (!empty($MsgId)) {
                $uniqueKey = $MsgId . "-" . $this->component_appid . "-" . $this->authorizer_appid;
            } else {
                $uniqueKey = $FromUserName . "-" . $CreateTime . "-" . $this->component_appid . "-" . $this->authorizer_appid;
            }
            $this->requestLogDatas['lock_uniqueKey'] = $uniqueKey;
            if (!empty($uniqueKey)) {
                $objLock = new \iLock(md5($uniqueKey));
                if ($objLock->lock()) {
                    return "success";
                }
            }

            // 获取微信用户的个人信息
            if (!empty($this->authorizerConfig['access_token'])) {
                $this->modelWeixinopenUser->setWeixinInstance($this->objWeixin);
                $this->modelWeixinopenUser->updateUserInfoByAction($FromUserName, $this->authorizer_appid, $this->component_appid);
            }
            // 设定来源和目标用户的openid
            $this->objWeixin->setFromAndTo($FromUserName, $ToUserName);

            /**
             * ==================================================================================
             * ====================================以上逻辑请勿修改===================================
             * ==================================================================================
             */

            // 业务逻辑开始
            // $response = "success";
            $datas = $this->handleRequestAndGetResponseByMsgType($datas);

            $content = $datas['content_process'];
            $response = $datas['response'];
            // 业务逻辑结束

            /**
             * ==================================================================================
             * ====================================以下逻辑请勿修改===================================
             * ==================================================================================
             */
            if ($onlyRevieve) {
                return "";
            }

            if ($content == 'debug') {
                $response = $this->objWeixin->getMsgManager()
                    ->getReplySender()
                    ->replyText($this->debugVar($datas));
            }

            if (empty($response)) {
                // $response = followUrl($this->answer($content), array(
                // 'FromUserName' => $FromUserName,
                // 'timestamp' => $__TIME_STAMP__,
                // 'signkey' => $__SIGN_KEY__
                // ));
                $response = $this->answer($FromUserName, $ToUserName, $content, $authorizer_appid, $component_appid);
            }

            // 输出响应结果
            $this->requestLogDatas['response'] = $response;
            $response = $this->responseToWeixinServer($response);
            $this->requestLogDatas['encrypt_response'] = $response;

            /**
             * ==================================================================================
             * ====================================以上逻辑请勿修改===================================
             * ==================================================================================
             */
            // 输出响应结果
            return $response;
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            // 如果脚本执行中发现异常，则记录返回的异常信息
            $this->requestLogDatas['response'] = $e->getFile() . $e->getLine() . $e->getMessage() . $e->getTraceAsString();
            return "success";
        }
    }

    /**
     * 测试用1
     */
    public function testDecryptMsgAction()
    {
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/test-decrypt-msg?appid=wxca8519f703c07d32
        try {
            // 初始化
            $this->doInitializeLogic();

            // <xml>
            // <AppId><![CDATA[wx7d9829b9bb066fe5]]></AppId>
            // <Encrypt><![CDATA[zVOkgBq+VNnmXwyDs9AIUymWt2P2cemjBrDROoeIu39Bb3FpqJ7+bTcwZtQL7sGfoZZHJ2DdGD7NKON3KpQfYorrm2bQAadlabSXHpgaZytcCPWvOLBLce2viKU0mBP7LTD45ASZ08evyuhSxU3WmNsi+WooxSRv6LjqnSyfg0qJbpfDTTBqOUok1Y11snMofp8YsHBfgh06zRdQjXw5au0z92dv4sdZVEwN2Fl83AlqrfbaLcvZbNSdY2/yKN4fZGMlOhF571h/AC6E/4IpBfCbKjfurd5ZYzBjmELRnR7fXuI8CsShV+ygRK2ResIqL+n20RbXOOOm3JNtZDrPilZggAvEL68NBLDaAvwLHuAMq+/9gR/vf9OhN3mCIcvnsJy/mMdnDebzPMJFcdmOw5ZSNgSrEnwgnfLRfBzyXCPYKQMJtrkOAE4orlhLUXo2CGHYvHoMwhz95PzXorIvsA==]]></Encrypt>
            // </xml>
            $verifyToken = isset($this->componentConfig['verify_token']) ? $this->componentConfig['verify_token'] : '';
            $encodingAesKey = isset($this->componentConfig['EncodingAESKey']) ? $this->componentConfig['EncodingAESKey'] : '';
            $AppId = $this->component_appid;

            // die($encodingAesKey.strlen($encodingAesKey));
            $pc = new \Weixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($verifyToken, $encodingAesKey, $AppId);

            // <xml><ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName><Encrypt><![CDATA[thU8Mz/2q8z+eeZ2HuOGzqAwZrMQWJdIkbr+vY+6a+vCO+22HSyHLWiEZv8TmaqkYKubJbIOpGhLvC2YBYJ94G/G5dsE17xhfGUkV70NMMc/8zL2jay6WAUCNHWovZ1V/SFlgi32AYJ69vICullSE5JCEH4TavYOk42KTfQSO4BYtAuF3zeFIoT3kc13wuqSSx/MN5YZTuNH2QV43Z2WQkQogOwisJP1GRDuFtB2o1bHfD9CxMOAKnRPGEDC6vwCMbEJgf8EVWcoNXRhDJ77eTmTg6pVi3rGXVWCJH7wC2oHz2jy/+MI2UDeSKuz8D/oJPT2RUEN+NErJS26slWXYbE1sTILcwQ+Yz7Su5Ec804/7Fs166UIShMgLzvMXk76PkG6xNsl4uGqapppq1+qVIUTQ8uggzfGbQsjDaZefQg=]]></Encrypt></xml>
            $verifyToken = "";
            $msg_sign = "57e26293c0a6645c7d3d2c4473cf3c385d86fa5c";
            $timeStamp = "1471846164";
            $nonce = "668506706";
            $from_xml = "<xml><ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName><Encrypt><![CDATA[thU8Mz/2q8z+eeZ2HuOGzqAwZrMQWJdIkbr+vY+6a+vCO+22HSyHLWiEZv8TmaqkYKubJbIOpGhLvC2YBYJ94G/G5dsE17xhfGUkV70NMMc/8zL2jay6WAUCNHWovZ1V/SFlgi32AYJ69vICullSE5JCEH4TavYOk42KTfQSO4BYtAuF3zeFIoT3kc13wuqSSx/MN5YZTuNH2QV43Z2WQkQogOwisJP1GRDuFtB2o1bHfD9CxMOAKnRPGEDC6vwCMbEJgf8EVWcoNXRhDJ77eTmTg6pVi3rGXVWCJH7wC2oHz2jy/+MI2UDeSKuz8D/oJPT2RUEN+NErJS26slWXYbE1sTILcwQ+Yz7Su5Ec804/7Fs166UIShMgLzvMXk76PkG6xNsl4uGqapppq1+qVIUTQ8uggzfGbQsjDaZefQg=]]></Encrypt></xml>";
            $msg = "";
            $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
            if ($errCode == 0) {
                // <xml><ToUserName><![CDATA[gh_abc8231997cb]]></ToUserName>
                // <FromUserName><![CDATA[o4ELSvz-B4_DThF0Vpfrverk3IpY]]></FromUserName>
                // <CreateTime>1471846164</CreateTime>
                // <MsgType><![CDATA[text]]></MsgType>
                // <Content><![CDATA[Highly]]></Content>
                // <MsgId>6321531139539682503</MsgId>
                // </xml>
                return $this->result("解密后:", array(
                    'msg' => $msg
                ));
            } else {
                return $this->result("errcode:", array(
                    'errorcode' => $errCode
                ));
            }
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 测试用2
     */
    public function testGetUserAction()
    {
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/test-get-user?appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&FromUserName=o8IA5v7Dwz8tk_EcVsRITf7fA9Fk
        try {
            // 初始化
            $this->doInitializeLogic();

            $FromUserName = isset($_GET['FromUserName']) ? trim($_GET['FromUserName']) : '';

            $this->modelWeixinopenUser->setWeixinInstance($this->objWeixin);
            $ret = $this->modelWeixinopenUser->updateUserInfoByAction($FromUserName, $this->authorizer_appid, $this->component_appid);

            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 测试用3
     */
    public function testKeywordAction()
    {
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/test-keyword?appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=xxx

        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/test-keyword?appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试文本1
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/test-keyword?appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试图片1
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/test-keyword?appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试语音1
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/test-keyword?appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试视频1
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/test-keyword?appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试音乐1
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/test-keyword?appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试单图文1
        // http://www.myapplicationmodule.com.com/weixinopen/api/msg/test-keyword?appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试多图文1
        try {
            // 初始化
            $this->doInitializeLogic();

            $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

            // 设定来源和目标用户的openid
            $this->objWeixin->setFromAndTo("FromUserName", "ToUserName");

            // 为回复的Model装载weixin对象
            $this->modelWeixinopenReplyMsg->setWeixinInstance($this->objWeixin);

            $response = $this->answer("FromUserName", "ToUserName", $keyword, $this->authorizer_appid, $this->component_appid);

            return $this->result("OK", $response);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    protected function verifyComponent($datas)
    {
        $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
        $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
        $content = isset($datas['Content']) ? trim($datas['Content']) : '';
        $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
        $Event = isset($datas['Event']) ? trim($datas['Event']) : '';

        // 自动化测试的专用测试公众号的信息如下：
        // （1）appid： wx570bc396a51b8ff8
        // （2）Username： gh_3c884a361561
        // 自动化测试的专用测试小程序的信息如下：
        // （1）appid：wxd101a85aa106f53e
        // （2）Username： gh_8dad206e9538
        if (in_array($ToUserName, array(
            'gh_3c884a361561',
            'gh_8dad206e9538'
        ))) {
            // 1、模拟粉丝触发专用测试公众号的事件，并推送事件消息到专用测试公众号，第三方平台方开发者需要提取推送XML信息中的event值，并在5秒内立即返回按照下述要求组装的文本消息给粉丝。
            // 1）微信推送给第三方平台方： 事件XML内容（与普通公众号接收到的信息是一样的）
            // 2）服务方开发者在5秒内回应文本消息并最终触达到粉丝：文本消息的XML中Content字段的内容必须组装为：event + “from_callback”（假定event为LOCATION，则Content为: LOCATIONfrom_callback）
            // 接收事件推送
            if ($MsgType == 'event') {
                $msg = "{$Event}from_callback";
            }

            // 2、模拟粉丝发送文本消息给专用测试公众号，第三方平台方需根据文本消息的内容进行相应的响应：
            // 1）微信模推送给第三方平台方：文本消息，其中Content字段的内容固定为：TESTCOMPONENT_MSG_TYPE_TEXT
            // 2）第三方平台方立马回应文本消息并最终触达粉丝：Content必须固定为：TESTCOMPONENT_MSG_TYPE_TEXT_callback
            elseif ($content == 'TESTCOMPONENT_MSG_TYPE_TEXT') {
                $msg = "{$content}_callback";
            }

            // 3、模拟粉丝发送文本消息给专用测试公众号，第三方平台方需在5秒内返回空串表明暂时不回复，然后再立即使用客服消息接口发送消息回复粉丝
            // 1）微信模推送给第三方平台方：文本消息，其中Content字段的内容固定为： QUERY_AUTH_CODE:$query_auth_code$（query_auth_code会在专用测试公众号自动授权给第三方平台方时，由微信后台推送给开发者）
            // 2）第三方平台方拿到$query_auth_code$的值后，通过接口文档页中的“使用授权码换取公众号的授权信息”API，将$query_auth_code$的值赋值给API所需的参数authorization_code。然后，调用发送客服消息api回复文本消息给粉丝，其中文本消息的content字段设为：$query_auth_code$_from_api（其中$query_auth_code$需要替换成推送过来的query_auth_code）
            elseif (preg_match('/QUERY_AUTH_CODE:/', $content)) {
                $query_auth_code = str_replace('QUERY_AUTH_CODE:', '', $content);
                $authorizer = $this->objWeixinComponent->apiQueryAuth($query_auth_code);
                $authorizer_info = empty($authorizer['authorization_info']) ? [] : $authorizer['authorization_info'];
                $authorizer_access_token = empty($authorizer_info['authorizer_access_token']) ? '' : $authorizer_info['authorizer_access_token'];
                $msg = $query_auth_code . '_from_api';

                $objWeixin = new \Weixin\Client();
                $objWeixin->setAccessToken($authorizer_access_token);
                $objWeixin->getMsgManager()
                    ->getCustomSender()
                    ->sendText($FromUserName, $msg);

                $msg = 'none';
            } else {
                return array(
                    'is_success' => false
                );
            }

            if ($msg != 'none') {
                $now = time();
                $response = "<xml>
                <ToUserName><![CDATA[{$FromUserName}]]></ToUserName>
                <FromUserName><![CDATA[{$ToUserName}]]></FromUserName>
                <CreateTime>{$now}</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[{$msg}]]></Content>
                </xml>";
                // 输出响应结果
                $response = $this->responseToWeixinServer($response);
            } else {
                $response = '';
            }
            $this->requestLogDatas['response'] = $response;
            // echo $response;
            return array(
                'is_success' => true,
                'response' => $response
            );
        } else {
            return array(
                'is_success' => false
            );
        }
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        // 第三方平台运用ID
        $this->component_appid = isset($_GET['appid']) ? trim($_GET['appid']) : "";
        $this->authorizer_appid = isset($_GET['authorizer_appid']) ? trim($_GET['authorizer_appid']) : "";
        // 创建service
        $this->weixinopenService = new \App\Weixin2\Services\WeixinService($this->authorizer_appid, $this->component_appid);
        if (!empty($this->component_appid)) {
            $this->componentConfig = $this->weixinopenService->getAppConfig4Component();
            if (empty($this->componentConfig)) {
                throw new \Exception("component_appid:{$this->component_appid}所对应的记录不存在");
            }
            $this->objWeixinComponent = $this->weixinopenService->getWeixinComponent();
        }

        // 授权方ID
        if (!empty($this->authorizer_appid)) {

            $this->authorizerConfig = $this->weixinopenService->getAppConfig4Authorizer();
            if (empty($this->authorizerConfig)) {
                throw new \Exception("component_appid:{$this->component_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
            }
            $this->objWeixin = $this->weixinopenService->getWeixinObject();
        }
    }

    protected function getDataFromWeixinServer()
    {
        $postStr = file_get_contents('php://input');
        $datas = $this->revieve($postStr);
        // 需要解密
        if ($this->isNeedDecryptAndEncrypt) {

            if (empty($this->requestLogDatas['aes_info']['EncodingAESKey'])) {
                throw new \Exception('application EncodingAESKey is null');
            }

            $decryptMsg = "";
            $pc = new \Weixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($this->requestLogDatas['aes_info']['verify_token'], $this->requestLogDatas['aes_info']['EncodingAESKey'],  $this->requestLogDatas['aes_info']['receiveId']);
            $errCode = $pc->decryptMsg($this->requestLogDatas['aes_info']['msg_signature'], $this->requestLogDatas['aes_info']['timestamp'], $this->requestLogDatas['aes_info']['nonce'], $postStr, $decryptMsg);
            if (empty($errCode)) {
                $datas = $this->revieve($decryptMsg);
                $this->requestLogDatas['aes_info']['decryptMsg'] = $decryptMsg;
            } else {
                throw new \Exception('application EncodingAESKey is failure in decryptMsg, appid:' .  $this->requestLogDatas['aes_info']['receiveId']);
            }
        }
        return $datas;
    }

    protected function responseToWeixinServer($response)
    {
        if ($response != "success") {
            // 需要加密
            if ($this->isNeedDecryptAndEncrypt) {
                $this->requestLogDatas['aes_info']['encryptMsg'] = $response;

                $encryptMsg = '';
                $timeStamp = time();
                $nonce = $this->requestLogDatas['aes_info']['nonce'];
                $pc = new \Weixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($this->requestLogDatas['aes_info']['verify_token'], $this->requestLogDatas['aes_info']['EncodingAESKey'], $this->requestLogDatas['aes_info']['receiveId']);
                $errCode = $pc->encryptMsg($response, $timeStamp, $nonce, $encryptMsg);

                if (empty($errCode)) {
                    $response = $encryptMsg;
                } else {
                    throw new \Exception('application EncodingAESKey is failure in encryptMsg, appid:' .  $this->requestLogDatas['aes_info']['receiveId']);
                }
            }
        }
        return $response;
    }

    /**
     * 处理请求返回响应
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140453
     *
     * @param array $datas            
     * @return array
     */
    protected function handleRequestAndGetResponseByMsgType(array $datas)
    {
        $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
        $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
        $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
        $response = isset($datas['response']) ? (trim($datas['response'])) : '';

        // 不同项目特定的业务逻辑开始
        // 事件逻辑开始
        if ($MsgType == 'event') { // 事件消息

            // 事件消息处理
            return $this->handleRequestAndGetResponseByEvent($datas);
        } // 事件逻辑结束

        // 文本逻辑开始
        elseif ($MsgType == 'text') { // 接收普通消息----文本消息

            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1348831860</CreateTime>
             * <MsgType><![CDATA[text]]></MsgType>
             * <Content><![CDATA[this is a test]]></Content>
             * <MsgId>1234567890123456</MsgId>
             * </xml>
             */
            $content = isset($datas['Content']) ? strtolower(trim($datas['Content'])) : '';

            if (isset($datas['bizmsgmenuid'])) {
                /**
                 * 发送菜单消息
                 * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140547
                 *
                 * {
                 * "touser": "OPENID"
                 * "msgtype": "msgmenu",
                 * "msgmenu": {
                 * "head_content": "您对本次服务是否满意呢? "
                 * "list": [
                 * {
                 * "id": "101",
                 * "content": "满意"
                 * },
                 * {
                 * "id": "102",
                 * "content": "不满意"
                 * }
                 * ],
                 * "tail_content": "欢迎再次光临"
                 * }
                 * }
                 * 按照上述例子，用户会看到这样的菜单消息：
                 *
                 * “您对本次服务是否满意呢？
                 *
                 * 满意
                 *
                 * 不满意”
                 *
                 * 其中，“满意”和“不满意”是可点击的，当用户点击后，微信会发送一条XML消息到开发者服务器，格式如下：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[ToUser]]></ToUserName>
                 * <FromUserName><![CDATA[FromUser]]></FromUserName>
                 * <CreateTime>1500000000</CreateTime>
                 * <MsgType><![CDATA[text]]></MsgType>
                 * <Content><![CDATA[满意]]></Content>
                 * <MsgId>1234567890123456</MsgId>
                 * <bizmsgmenuid>101</bizmsgmenuid>
                 * </xml>
                 * XML参数说明：
                 *
                 * 参数 说明
                 * ToUserName 开发者帐号
                 * FromUserName 接收方帐号（OpenID）
                 * CreateTime 消息创建时间戳
                 * MsgType Text
                 * Content 点击的菜单名
                 * MsgId 消息ID
                 * bizmsgmenuid 点击的菜单ID
                 */
                $bizmsgmenuid = isset($datas['bizmsgmenuid']) ? strtolower(trim($datas['bizmsgmenuid'])) : '';
            }

            // if ($content == "客服测试") {
            // $response = $this->objWeixin->getMsgManager()
            // ->getReplySender()
            // ->replyCustomerService();
            // }
        } // 文本逻辑结束

        // 图片逻辑开始
        elseif ($MsgType == 'image') { // 接收普通消息----图片消息
            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1348831860</CreateTime>
             * <MsgType><![CDATA[image]]></MsgType>
             * <PicUrl><![CDATA[this is a url]]></PicUrl>
             * <MediaId><![CDATA[media_id]]></MediaId>
             * <MsgId>1234567890123456</MsgId>
             * </xml>
             */
            // PicUrl 图片链接
            // MediaId 图片消息媒体id，可以调用多媒体文件下载接口拉取数据。
            $PicUrl = isset($datas['PicUrl']) ? trim($datas['PicUrl']) : '';
            $MediaId = isset($datas['MediaId']) ? trim($datas['MediaId']) : '';

            // 使用闭包，提高相应速度
            // $content = '默认图片回复';
        } // 图片逻辑结束

        // 语音逻辑开始
        elseif ($MsgType == 'voice') { // 接收普通消息----语音消息 或者接收语音识别结果
            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1357290913</CreateTime>
             * <MsgType><![CDATA[voice]]></MsgType>
             * <MediaId><![CDATA[media_id]]></MediaId>
             * <Format><![CDATA[Format]]></Format>
             * <MsgId>1234567890123456</MsgId>
             * </xml>
             */
            // MediaID 语音消息媒体id，可以调用多媒体文件下载接口拉取该媒体
            // Format 语音格式：amr
            // Recognition 语音识别结果，UTF8编码
            $MediaId = isset($datas['MediaId']) ? trim($datas['MediaId']) : '';
            $Format = isset($datas['Format']) ? trim($datas['Format']) : '';
            $Recognition = isset($datas['Recognition']) ? trim($datas['Recognition']) : '';

            // $content = '默认语音回复';
        } // 语音逻辑结束

        // 视频逻辑开始
        elseif ($MsgType == 'video') { // 接收普通消息----视频消息
            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1357290913</CreateTime>
             * <MsgType><![CDATA[video]]></MsgType>
             * <MediaId><![CDATA[media_id]]></MediaId>
             * <ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
             * <MsgId>1234567890123456</MsgId>
             * </xml>
             */
            // MediaId 视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
            // ThumbMediaId 视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
            $MediaId = isset($datas['MediaId']) ? trim($datas['MediaId']) : '';
            $ThumbMediaId = isset($datas['ThumbMediaId']) ? trim($datas['ThumbMediaId']) : '';
        } // 视频逻辑结束

        // 小视频逻辑开始
        elseif ($MsgType == 'shortvideo') { // 接收普通消息----小视频消息
            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1357290913</CreateTime>
             * <MsgType><![CDATA[shortvideo]]></MsgType>
             * <MediaId><![CDATA[media_id]]></MediaId>
             * <ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
             * <MsgId>1234567890123456</MsgId>
             * </xml>
             */
            // MediaId 视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
            // ThumbMediaId 视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
            $MediaId = isset($datas['MediaId']) ? trim($datas['MediaId']) : '';
            $ThumbMediaId = isset($datas['ThumbMediaId']) ? trim($datas['ThumbMediaId']) : '';
        } // 小视频逻辑结束

        // 地理位置逻辑开始
        elseif ($MsgType == 'location') { // 接收普通消息----地理位置消息
            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1351776360</CreateTime>
             * <MsgType><![CDATA[location]]></MsgType>
             * <Location_X>23.134521</Location_X>
             * <Location_Y>113.358803</Location_Y>
             * <Scale>20</Scale>
             * <Label><![CDATA[位置信息]]></Label>
             * <MsgId>1234567890123456</MsgId>
             * </xml>
             */
            // Location_X 地理位置维度
            // Location_Y 地理位置经度
            // Scale 地图缩放大小
            $Location_X = isset($datas['Location_X']) ? trim($datas['Location_X']) : 0;
            $Location_Y = isset($datas['Location_Y']) ? trim($datas['Location_Y']) : 0;
            $Scale = isset($datas['Scale']) ? trim($datas['Scale']) : 0;
            $Label = isset($datas['Label']) ? trim($datas['Label']) : '';

            // $articles = $this->shopLocation($Location_X, $Location_Y);
            // if (! empty($articles)) {
            // $response = $this->objWeixin->getMsgManager()
            // ->getReplySender()
            // ->replyGraphText($articles);
            // }
        } // 地理位置逻辑结束

        // 链接逻辑开始
        elseif ($MsgType == 'link') { // 接收普通消息----链接消息
            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1351776360</CreateTime>
             * <MsgType><![CDATA[link]]></MsgType>
             * <Title><![CDATA[公众平台官网链接]]></Title>
             * <Description><![CDATA[公众平台官网链接]]></Description>
             * <Url><![CDATA[url]]></Url>
             * <MsgId>1234567890123456</MsgId>
             * </xml>
             */
            // Title 消息标题
            // Description 消息描述
            // Url 消息链接
            $Title = isset($datas['Title']) ? trim($datas['Title']) : '';
            $Description = isset($datas['Description']) ? trim($datas['Description']) : '';
            $Url = isset($datas['Url']) ? trim($datas['Url']) : '';
        } // 链接逻辑结束

        // 不同项目特定的业务逻辑结束

        $datas['content_process'] = $content;
        $datas['response'] = $response;
        return $datas;
    }

    /**
     * 根据事件类型，处理请求返回响应
     * 接收事件推送 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140454
     * 微信认证事件推送 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1455785130
     * 获取用户地理位置 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140841
     * 群发接口和原创校验 事件推送群发结果 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1481187827_i0l21
     * 模板消息事件推送 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
     *
     * @param array $datas            
     * @return array
     */
    protected function handleRequestAndGetResponseByEvent(array $datas)
    {
        $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
        $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
        $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
        $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
        $EventKey = isset($datas['EventKey']) ? trim($datas['EventKey']) : '';
        $CreateTime = isset($datas['CreateTime']) ? ($datas['CreateTime']) : time();

        $content = "";
        $response = '';

        // 不同项目特定的业务逻辑开始

        // 接收事件推送

        // 关注/取消关注事件
        // 用户在关注与取消关注公众号时，微信会把这个事件推送到开发者填写的URL。方便开发者给用户下发欢迎消息或者做帐号的解绑。为保护用户数据隐私，开发者收到用户取消关注事件时需要删除该用户的所有信息。
        // 微信服务器在五秒内收不到响应会断掉连接，并且重新发起请求，总共重试三次。
        // 关于重试的消息排重，推荐使用FromUserName + CreateTime 排重。
        // 假如服务器无法保证在五秒内处理并回复，可以直接回复空串，微信服务器不会对此作任何处理，并且不会发起重试。

        if ($Event == 'subscribe') { // 关注事件
            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[subscribe]]></Event>
             * </xml>
             */

            // 扫描带参数二维码事件
            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[subscribe]]></Event>
             * <EventKey><![CDATA[qrscene_123123]]></EventKey>
             * <Ticket><![CDATA[TICKET]]></Ticket>
             * </xml>
             */
            // EventKey 事件KEY值，qrscene_为前缀，后面为二维码的参数值
            // Ticket 二维码的ticket，可用来换取二维码图片
            $Ticket = isset($datas['Ticket']) ? trim($datas['Ticket']) : '';

            if (!empty($Ticket) && !empty($EventKey)) { // 扫描带参数二维码事件 1. 用户未关注时，进行关注后的事件推送

                $scene_id = (str_ireplace('qrscene_', '', $EventKey));

                $this->modelWeixinopenQrcodeEventLog->record($this->authorizer_appid, $this->component_appid, $scene_id, $FromUserName, $ToUserName, $CreateTime, $MsgType, $Event, $EventKey, $Ticket);
                $this->modelWeixinopenQrcode->incSubscribeEventNum($this->authorizer_appid, $this->component_appid, $scene_id, 1);

                // 二维码场景管理
                if (!empty($scene_id)) {
                    // $content = "扫描二维码{$scene_id}";
                    $content = $scene_id;
                }
            }

            if (empty($content)) {
                $content = '首访回复';
            }
        } elseif ($Event == 'unsubscribe') { // 取消关注事件
            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[unsubscribe]]></Event>
             * </xml>
             */
            $response = '';
        } elseif ($Event == 'SCAN') { // 扫描带参数二维码事件 2. 用户已关注时的事件推送

            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[SCAN]]></Event>
             * <EventKey><![CDATA[SCENE_VALUE]]></EventKey>
             * <Ticket><![CDATA[TICKET]]></Ticket>
             * </xml>
             */

            // EventKey 事件KEY值，是一个32位无符号整数
            // Ticket 二维码的ticket，可用来换取二维码图片

            $Ticket = isset($datas['Ticket']) ? trim($datas['Ticket']) : '';

            $this->modelWeixinopenQrcodeEventLog->record($this->authorizer_appid, $this->component_appid, $EventKey, $FromUserName, $ToUserName, $CreateTime, $MsgType, $Event, $EventKey, $Ticket);
            $this->modelWeixinopenQrcode->incScanEventNum($this->authorizer_appid, $this->component_appid, $EventKey, 1);

            $onlyRevieve = true;
            // $content = "扫描二维码{$EventKey}";
            $content = $EventKey;
        } elseif ($Event == 'LOCATION') { // 上报地理位置事件

            // 用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，或在进入会话后每5秒上报一次地理位置，公众号可以在公众平台网站中修改以上设置。上报地理位置时，微信会将上报地理位置事件推送到开发者填写的URL。
            /**
             * 获取用户地理位置
             * 开通了上报地理位置接口的公众号，用户在关注后进入公众号会话时，会弹框让用户确认是否允许公众号使用其地理位置。
             * 弹框只在关注后出现一次，用户以后可以在公众号详情页面进行操作。
             *
             * 第三方在收到地理位置上报信息之后，只需要回复success表明收到即可，是不允许回复消息给粉丝的。
             *
             * 获取用户地理位置
             *
             * 用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，上报地理位置以推送XML数据包到开发者填写的URL来实现。
             *
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[LOCATION]]></Event>
             * <Latitude>23.137466</Latitude>
             * <Longitude>113.352425</Longitude>
             * <Precision>119.385040</Precision>
             * </xml>
             */
            // Latitude 地理位置纬度
            // Longitude 地理位置经度
            // Precision 地理位置精度
            $Latitude = isset($datas['Latitude']) ? floatval($datas['Latitude']) : 0;
            $Longitude = isset($datas['Longitude']) ? floatval($datas['Longitude']) : 0;
            $Precision = isset($datas['Precision']) ? floatval($datas['Precision']) : 0;
            $onlyRevieve = true;
            $response = "success";
        } elseif ($Event == 'CLICK') { // 自定义菜单事件推送-点击菜单拉取消息时的事件推送

            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[CLICK]]></Event>
             * <EventKey><![CDATA[EVENTKEY]]></EventKey>
             * </xml>
             */
            // EventKey 事件KEY值，与自定义菜单接口中KEY值对应
            $content = $EventKey;
        } elseif ($Event == 'VIEW') { // 自定义菜单事件推送-点击菜单跳转链接时的事件推送

            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[VIEW]]></Event>
             * <EventKey><![CDATA[www.qq.com]]></EventKey>
             * <MenuId>MENUID</MenuId>
             * </xml>
             */
            // EventKey 事件KEY值，设置的跳转URL
            // MenuID 指菜单ID，如果是个性化菜单，则可以通过这个字段，知道是哪个规则的菜单被点击了。
            $MenuID = isset($datas['MenuID']) ? $datas['MenuID'] : '';

            $content = $EventKey;
        } elseif ($Event == 'scancode_push') { // 自定义菜单事件推送 -scancode_push：扫码推事件的事件推送

            /**
             * <xml><ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
             * <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
             * <CreateTime>1408090502</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[scancode_push]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <ScanCodeInfo><ScanType><![CDATA[qrcode]]></ScanType>
             * <ScanResult><![CDATA[1]]></ScanResult>
             * </ScanCodeInfo>
             * </xml>
             */
            // ScanCodeInfo 扫描信息
            // ScanType 扫描类型，一般是qrcode
            // ScanResult 扫描结果，即二维码对应的字符串信息
            $ScanType = isset($datas['ScanCodeInfo']['ScanType']) ? trim($datas['ScanCodeInfo']['ScanType']) : "";
            $ScanResult = isset($datas['ScanCodeInfo']['ScanResult']) ? trim($datas['ScanCodeInfo']['ScanResult']) : "";
            $content = $EventKey;
        } elseif ($Event == 'scancode_waitmsg') { // 自定义菜单事件推送 -scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框的事件推送

            /**
             * <xml><ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
             * <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
             * <CreateTime>1408090606</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[scancode_waitmsg]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <ScanCodeInfo><ScanType><![CDATA[qrcode]]></ScanType>
             * <ScanResult><![CDATA[2]]></ScanResult>
             * </ScanCodeInfo>
             * </xml>
             */

            // ScanCodeInfo 扫描信息
            // ScanType 扫描类型，一般是qrcode
            // ScanResult 扫描结果，即二维码对应的字符串信息
            $ScanType = isset($datas['ScanCodeInfo']['ScanType']) ? trim($datas['ScanCodeInfo']['ScanType']) : "";
            $ScanResult = isset($datas['ScanCodeInfo']['ScanResult']) ? trim($datas['ScanCodeInfo']['ScanResult']) : "";
            $content = $EventKey;
        } elseif ($Event == 'pic_sysphoto') { // 自定义菜单事件推送 -pic_sysphoto：弹出系统拍照发图的事件推送

            /**
             * <xml><ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
             * <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
             * <CreateTime>1408090651</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[pic_sysphoto]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <SendPicsInfo><Count>1</Count>
             * <PicList><item><PicMd5Sum><![CDATA[1b5f7c23b5bf75682a53e7b6d163e185]]></PicMd5Sum>
             * </item>
             * </PicList>
             * </SendPicsInfo>
             * </xml>
             */

            // SendPicsInfo 发送的图片信息
            // Count 发送的图片数量
            // PicList 图片列表
            // PicMd5Sum 图片的MD5值，开发者若需要，可用于验证接收到图片
            $Count = isset($datas['SendPicsInfo']['Count']) ? trim($datas['SendPicsInfo']['Count']) : 0;
            $PicList = isset($datas['SendPicsInfo']['PicList']) ? trim($datas['SendPicsInfo']['PicList']) : "";
            $content = $EventKey;
        } elseif ($Event == 'pic_photo_or_album') { // 自定义菜单事件推送 -pic_photo_or_album：弹出拍照或者相册发图的事件推送

            /**
             * <xml><ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
             * <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
             * <CreateTime>1408090816</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[pic_photo_or_album]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <SendPicsInfo><Count>1</Count>
             * <PicList><item><PicMd5Sum><![CDATA[5a75aaca956d97be686719218f275c6b]]></PicMd5Sum>
             * </item>
             * </PicList>
             * </SendPicsInfo>
             * </xml>
             */

            // SendPicsInfo 发送的图片信息
            // Count 发送的图片数量
            // PicList 图片列表
            // PicMd5Sum 图片的MD5值，开发者若需要，可用于验证接收到图片
            $Count = isset($datas['SendPicsInfo']['Count']) ? trim($datas['SendPicsInfo']['Count']) : 0;
            $PicList = isset($datas['SendPicsInfo']['PicList']) ? trim($datas['SendPicsInfo']['PicList']) : "";
            $content = $EventKey;
        } elseif ($Event == 'pic_weixin') { // 自定义菜单事件推送 -pic_weixin：弹出微信相册发图器的事件推送

            /**
             * <xml><ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
             * <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
             * <CreateTime>1408090816</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[pic_weixin]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <SendPicsInfo>
             * <Count>1</Count>
             * <PicList>
             * <item>
             * <PicMd5Sum><![CDATA[5a75aaca956d97be686719218f275c6b]]></PicMd5Sum>
             * </item>
             * </PicList>
             * </SendPicsInfo>
             * </xml>
             */

            // SendPicsInfo 发送的图片信息
            // Count 发送的图片数量
            // PicList 图片列表
            // PicMd5Sum 图片的MD5值，开发者若需要，可用于验证接收到图片
            $Count = isset($datas['SendPicsInfo']['Count']) ? trim($datas['SendPicsInfo']['Count']) : 0;
            $PicList = isset($datas['SendPicsInfo']['PicList']) ? trim($datas['SendPicsInfo']['PicList']) : "";
            $content = $EventKey;
        } elseif ($Event == 'location_select') { // 自定义菜单事件推送 -location_select：弹出地理位置选择器的事件推送

            /**
             * <xml><ToUserName><![CDATA[gh_e136c6e50636]]></ToUserName>
             * <FromUserName><![CDATA[oMgHVjngRipVsoxg6TuX3vz6glDg]]></FromUserName>
             * <CreateTime>1408091189</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[location_select]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <SendLocationInfo><Location_X><![CDATA[23]]></Location_X>
             * <Location_Y><![CDATA[113]]></Location_Y>
             * <Scale><![CDATA[15]]></Scale>
             * <Label><![CDATA[ 广州市海珠区客村艺苑路 106号]]></Label>
             * <Poiname><![CDATA[]]></Poiname>
             * </SendLocationInfo>
             * </xml>
             */

            // SendLocationInfo 发送的位置信息
            // Location_X X坐标信息
            // Location_Y Y坐标信息
            // Scale 精度，可理解为精度或者比例尺、越精细的话 scale越高
            // Label 地理位置的字符串信息
            // Poiname 朋友圈POI的名字，可能为空
            $Location_X = isset($datas['SendLocationInfo']['Location_X']) ? trim($datas['SendLocationInfo']['Location_X']) : 0;
            $Location_Y = isset($datas['SendLocationInfo']['Location_Y']) ? trim($datas['SendLocationInfo']['Location_Y']) : 0;
            $Scale = isset($datas['SendLocationInfo']['Scale']) ? trim($datas['SendLocationInfo']['Scale']) : 0;
            $Label = isset($datas['SendLocationInfo']['Label']) ? trim($datas['SendLocationInfo']['Label']) : "";
            $Poiname = isset($datas['SendLocationInfo']['Poiname']) ? trim($datas['SendLocationInfo']['Poiname']) : "";

            $content = $EventKey;

            // $articles = $this->shopLocation($Location_X, $Location_Y);
            // if (! empty($articles)) {
            // $response = $this->objWeixin->getMsgManager()
            // ->getReplySender()
            // ->replyGraphText($articles);
            // }
        } elseif ($Event == 'view_miniprogram') { // 自定义菜单事件推送 -view_miniprogram：点击菜单跳转小程序的事件推送
            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[view_miniprogram]]></Event>
             * <EventKey><![CDATA[pages/index/index]]></EventKey>
             * <MenuId>MENUID</MenuId>
             * </xml>
             */
            $MenuID = isset($datas['MenuID']) ? $datas['MenuID'] : '';
            $content = $EventKey;
        } elseif ($Event == 'MASSSENDJOBFINISH') { // 事件推送群发结果 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1481187827_i0l21

            /**
             * <xml>
             * <ToUserName><![CDATA[gh_4d00ed8d6399]]></ToUserName>
             * <FromUserName><![CDATA[oV5CrjpxgaGXNHIQigzNlgLTnwic]]></FromUserName>
             * <CreateTime>1481013459</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[MASSSENDJOBFINISH]]></Event>
             * <MsgID>1000001625</MsgID>
             * <Status><![CDATA[err(30003)]]></Status>
             * <TotalCount>0</TotalCount>
             * <FilterCount>0</FilterCount>
             * <SentCount>0</SentCount>
             * <ErrorCount>0</ErrorCount>
             * <CopyrightCheckResult>
             * <Count>2</Count>
             * <ResultList>
             * <item>
             * <ArticleIdx>1</ArticleIdx>
             * <UserDeclareState>0</UserDeclareState>
             * <AuditState>2</AuditState>
             * <OriginalArticleUrl><![CDATA[Url_1]]></OriginalArticleUrl>
             * <OriginalArticleType>1</OriginalArticleType>
             * <CanReprint>1</CanReprint>
             * <NeedReplaceContent>1</NeedReplaceContent>
             * <NeedShowReprintSource>1</NeedShowReprintSource>
             * </item>
             * <item>
             * <ArticleIdx>2</ArticleIdx>
             * <UserDeclareState>0</UserDeclareState>
             * <AuditState>2</AuditState>
             * <OriginalArticleUrl><![CDATA[Url_2]]></OriginalArticleUrl>
             * <OriginalArticleType>1</OriginalArticleType>
             * <CanReprint>1</CanReprint>
             * <NeedReplaceContent>1</NeedReplaceContent>
             * <NeedShowReprintSource>1</NeedShowReprintSource>
             * </item>
             * </ResultList>
             * <CheckState>2</CheckState>
             * </CopyrightCheckResult>
             * </xml>
             */
            // Status 群发的结构，为“send success”或“send fail”或“err(num)”。但send success时，也有可能因用户拒收公众号的消息、系统错误等原因造成少量用户接收失败。err(num)是审核失败的具体原因，可能的情况如下：err(10001), //涉嫌广告 err(20001), //涉嫌政治 err(20004), //涉嫌社会 err(20002), //涉嫌色情 err(20006), //涉嫌违法犯罪 err(20008), //涉嫌欺诈 err(20013), //涉嫌版权 err(22000), //涉嫌互推(互相宣传) err(21000), //涉嫌其他
            // TotalCount group_id下粉丝数；或者openid_list中的粉丝数
            // FilterCount 过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数，原则上，FilterCount = SentCount + ErrorCount
            // SentCount 发送成功的粉丝数
            // ErrorCount 发送失败的粉丝数
            $MsgID = isset($datas['MsgID']) ? trim($datas['MsgID']) : '';
            $Status = isset($datas['Status']) ? trim($datas['Status']) : '';
            $TotalCount = isset($datas['TotalCount']) ? intval($datas['TotalCount']) : 0;
            $FilterCount = isset($datas['FilterCount']) ? intval($datas['FilterCount']) : 0;
            $SentCount = isset($datas['SentCount']) ? intval($datas['SentCount']) : 0;
            $ErrorCount = isset($datas['ErrorCount']) ? intval($datas['ErrorCount']) : 0;
            $CopyrightCheckResult = isset($datas['CopyrightCheckResult']) ? $datas['CopyrightCheckResult'] : array();

            $response = "success";
        } elseif ($Event == 'TEMPLATESENDJOBFINISH') { // 事件推送模版消息发送结果

            /**
             * 送达成功时 <Status><![CDATA[success]]></Status>
             * 送达由于用户拒收（用户设置拒绝接收公众号消息）而失败时 <Status><![CDATA[failed:user block]]></Status>
             * 送达由于其他原因失败时 <Status><![CDATA[failed: system failed]]></Status>
             */
            // Status 发送状态为成功
            $Status = isset($datas['Status']) ? trim($datas['Status']) : '';
            $response = "success";
        } elseif ($Event == 'qualification_verify_success') { // 微信认证事件推送 - 资质认证成功（此时立即获得接口权限）

            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1442401156</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[qualification_verify_success]]></Event>
             * <ExpiredTime>1442401156</ExpiredTime>
             * </xml>
             */
            // ExpiredTime 有效期 (整形)，指的是时间戳
            $ExpiredTime = isset($datas['ExpiredTime']) ? intval($datas['ExpiredTime']) : 0;
            $response = "success";
        } elseif ($Event == 'qualification_verify_fail') { // 微信认证事件推送 - 资质认证失败

            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1442401156</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[qualification_verify_fail]]></Event>
             * <FailTime>1442401122</FailTime>
             * <FailReason><![CDATA[by time]]></FailReason>
             * </xml>
             */
            // FailTime 失败发生时间 (整形)，时间戳
            // FailReason 认证失败的原因
            $FailTime = isset($datas['FailTime']) ? intval($datas['FailTime']) : 0;
            $FailReason = isset($datas['FailReason']) ? trim($datas['FailReason']) : '';
            $response = "success";
        } elseif ($Event == 'naming_verify_success') { // 微信认证事件推送 - 名称认证成功（即命名成功）

            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1442401093</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[naming_verify_success]]></Event>
             * <ExpiredTime>1442401093</ExpiredTime>
             * </xml>
             */

            // ExpiredTime 有效期 (整形)，指的是时间戳
            $ExpiredTime = isset($datas['ExpiredTime']) ? intval($datas['ExpiredTime']) : 0;
            $response = "success";
        } elseif ($Event == 'naming_verify_fail') { // 微信认证事件推送 - 名称认证失败（这时虽然客户端不打勾，但仍有接口权限）

            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1442401061</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[naming_verify_fail]]></Event>
             * <FailTime>1442401061</FailTime>
             * <FailReason><![CDATA[by time]]></FailReason>
             * </xml>
             */

            // FailTime 失败发生时间 (整形)，时间戳
            // FailReason 认证失败的原因
            $FailTime = isset($datas['FailTime']) ? intval($datas['FailTime']) : 0;
            $FailReason = isset($datas['FailReason']) ? trim($datas['FailReason']) : '';
            $response = "success";
        } elseif ($Event == 'annual_renew') { // 微信认证事件推送 - 年审通知

            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1442401004</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[annual_renew]]></Event>
             * <ExpiredTime>1442401004</ExpiredTime>
             * </xml>
             */

            // ExpiredTime 有效期 (整形)，指的是时间戳
            $ExpiredTime = isset($datas['ExpiredTime']) ? intval($datas['ExpiredTime']) : 0;
            $response = "success";
        } elseif ($Event == 'verify_expired') { // 微信认证事件推送 - 认证过期失效通知审通知

            /**
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1442400900</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[verify_expired]]></Event>
             * <ExpiredTime>1442400900</ExpiredTime>
             * </xml>
             */
            // ExpiredTime 有效期 (整形)，指的是时间戳
            $ExpiredTime = isset($datas['ExpiredTime']) ? intval($datas['ExpiredTime']) : 0;
            $response = "success";
        } elseif ($Event == 'user_pay_from_pay_cell') { // 买单事件推送

            /**
             * <CardId><![CDATA[po2VNuCuRo-8sxxxxxxxxxxx]]></CardId>
             * <UserCardCode><![CDATA[38050000000]]></UserCardCode>
             * <TransId><![CDATA[10022403432015000000000]]></TransId>
             * <LocationId>291710000</LocationId>
             * <Fee><![CDATA[10000]]></Fee>
             * <OriginalFee><![CDATA[10000]]> </OriginalFee>
             */
            // CardId 卡券ID。
            // UserCardCode 卡券Code码。
            // TransId 微信支付交易订单号（只有使用买单功能核销的卡券才会出现）
            // LocationName 门店名称，当前卡券核销的门店名称（只有通过卡券商户助手和买单核销时才会出现）
            // Fee 实付金额，单位为分
            // OriginalFee 应付金额，单位为分
            $CardId = isset($datas['CardId']) ? trim($datas['CardId']) : '';
            $UserCardCode = isset($datas['UserCardCode']) ? trim($datas['UserCardCode']) : '';
            $TransId = isset($datas['TransId']) ? trim($datas['TransId']) : '';
            $LocationId = isset($datas['LocationId']) ? trim($datas['LocationId']) : '';
            $Fee = isset($datas['Fee']) ? intval($datas['Fee']) : 0;
            $OriginalFee = isset($datas['OriginalFee']) ? trim($datas['OriginalFee']) : 0;
            $response = "success";
        } elseif ($Event == 'card_pass_check') { // 卡券 - 审核通过事件推送
            $response = "success";
        } elseif ($Event == 'card_not_pass_check') { // 卡券 - 审核不通过事件推送
            $response = "success";
        } elseif ($Event == 'user_del_card') { // 卡券 - 删除卡券事件推送
            $response = "success";
        } elseif ($Event == 'user_view_card') { // 卡券 - 进入卡事件推送 进入卡券界面推送事件
            $response = "success";
        } elseif ($Event == 'user_consume_card') { // 卡券 - 核销事件推送
            $response = "success";
        } elseif ($Event == 'user_get_card') { // 卡券 - 领取卡券事件推送

            // 礼品卡
            $is_gift_card = false;
            // 如果不是礼品卡的话
            if (empty($is_gift_card)) {
                $response = "success";
            } else {
                $response = "<xml>ok</xml>";
            }
        } elseif ($Event == 'giftcard_pay_done') { // 卡券 - 用户购买礼品卡付款成功
            $response = "<xml>ok</xml>";
        } elseif ($Event == 'giftcard_send_to_friend') { // 卡券 - 用户购买后赠送
            $response = "<xml>ok</xml>";
        } elseif ($Event == 'giftcard_user_accept') { // 卡券 - 用户领取礼品卡成功
            $response = "<xml>ok</xml>";
        } elseif ($Event == 'user_gifting_card') { // 卡券 - 转赠
            $response = "<xml>ok</xml>";
        } elseif ($Event == 'update_member_card') { // 卡券 - 更新会员卡事件推送
            $response = "success";
        } elseif ($Event == 'user_authorize_invoice') { // 卡券 - 收取授权完成事件推送
            $response = "success";
        } elseif ($Event == 'submit_membercard_user_info') { // 卡券 - 接收会员信息事件通知
            $response = "success";
        } else {
            $response = "";
        }
        // 不同项目特定的业务逻辑结束

        $datas['content_process'] = $content;
        $datas['response'] = $response;
        return $datas;
    }

    /**
     * 匹配文本并进行自动回复
     *
     * @param string $FromUserName            
     * @param string $ToUserName            
     * @param string $content            
     * @param string $authorizer_appid            
     * @param string $component_appid           
     * @return boolean
     */
    protected function answer($FromUserName, $ToUserName, $content, $authorizer_appid, $component_appid)
    {
        $match = $this->modelWeixinopenKeyword->matchKeyWord($content, $authorizer_appid, $component_appid, false);
        if (empty($match)) {
            $this->modelWeixinopenWord->record($content, $authorizer_appid, $component_appid);
            $match = $this->modelWeixinopenKeyword->matchKeyWord('默认回复', $authorizer_appid, $component_appid, false);
            if (empty($match)) {
                $match = $this->modelWeixinopenKeyword->matchKeyWord('默认回复', "", $component_appid, 0, false);
            }
        }
        $match['reply_msg_ids'] = $this->modelWeixinopenKeywordToReplyMsg->getReplyMsgIdsByKeywordId($match['_id']);
        $match['custom_msg_ids'] = $this->modelWeixinopenKeywordToCustomMsg->getCustomMsgIdsByKeywordId($match['_id']);
        $match['template_msg_ids'] = $this->modelWeixinopenKeywordToTemplateMsg->getTemplateMsgIdsByKeywordId($match['_id']);
        if (!empty($match['custom_msg_ids'])) {
            $this->weixinopenService->answerCustomMsgs($FromUserName, $ToUserName, $match);
        }
        if (!empty($match['template_msg_ids'])) {
            $this->weixinopenService->answerTemplateMsgs($FromUserName, $ToUserName, $match);
        }
        return $this->weixinopenService->answerReplyMsgs($FromUserName, $ToUserName, $match);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->doRecordMsgLog();
    }

    protected function doRecordMsgLog()
    {
        if (!empty($this->requestLogDatas)) {

            if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
                $this->requestLogDatas['interval'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            } else {
                $this->requestLogDatas['interval'] = 0;
            }
            $this->requestLogDatas['request_time'] = $this->now;
            $this->requestLogDatas['response_time'] = time();
            $postStr = file_get_contents('php://input');
            $this->requestLogDatas['request_xml'] = $postStr;
            $this->requestLogDatas['request_params'] = array_merge($_GET, $_POST);
            $this->requestLogDatas['is_aes'] = $this->isNeedDecryptAndEncrypt;

            if ($this->requestLogDatas['log_type'] == 'msglog') { // 消息与事件接收URL
                $this->modelWeixinopenMsgLog->record($this->requestLogDatas);
            }
        }
    }
}
