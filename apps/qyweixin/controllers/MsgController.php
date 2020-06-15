<?php

namespace App\Qyweixin\Controllers;

/**
         * 消息推送
         * 企业微信消息与事件接收
         */
class MsgController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 2;
    /**
         *
         * @var \App\Qyweixin\Models\User\User
         */
    private $modelQyweixinUser;
    /**
         *
         * @var \App\Qyweixin\Models\Provider\Provider
         */
    private $modelQyweixinProvider;
    /**
     *
     * @var \App\Qyweixin\Models\Authorize\Authorizer
     */
    private $modelQyweixinAuthorizer;

    /**
     *
     * @var \App\Qyweixin\Models\Msg\Log
     */
    private $modelQyweixinMsgLog;
    /**
     *
     * @var \App\Qyweixin\Models\ReplyMsg\ReplyMsg
     */
    private $modelQyweixinReplyMsg;

    /**
     *
     * @var \App\Qyweixin\Models\Keyword\Keyword
     */
    private $modelQyweixinKeyword;

    /**
     *
     * @var \App\Qyweixin\Models\Keyword\KeywordToReplyMsg
     */
    private $modelQyweixinKeywordToReplyMsg;

    /**
     *
     * @var \App\Qyweixin\Models\Keyword\KeywordToAgentMsg
     */
    private $modelQyweixinKeywordToAgentMsg;

    /**
     *
     * @var \App\Qyweixin\Models\Keyword\Word
     */
    private $modelQyweixinWord;

    // lock key
    private $lock_key_prefix = 'qyweixin_qy_';

    private $trackingKey = "公众号授权给第三方服务商流程";

    /** @var  \Weixin\Component */
    private $objQyWeixinProvider;

    /** @var  \Qyweixin\Client */
    private $objQyWeixin;

    private $provider_appid;

    private $providerConfig;

    private $authorizer_appid;

    private $authorizerConfig;

    private $agentid;

    private $agentConfig;

    /**
     *
     * @var \App\Components\Weixinopen\Services\QyService
     */
    private $qyweixinService;

    // 请求日志信息
    private $requestLogDatas = array();

    // 是否加解密
    private $isNeedDecryptAndEncrypt = TRUE;

    public function initialize()
    {
        $this->isNeedDecryptAndEncrypt = true;

        $this->modelQyweixinUser = new \App\Qyweixin\Models\User\User();
        $this->modelQyweixinProvider = new \App\Qyweixin\Models\Provider\Provider();
        $this->modelQyweixinAuthorizer = new \App\Qyweixin\Models\Authorize\Authorizer();
        $this->modelQyweixinMsgLog = new \App\Qyweixin\Models\Msg\Log();
        $this->modelQyweixinReplyMsg = new \App\Qyweixin\Models\ReplyMsg\ReplyMsg();
        $this->modelQyweixinKeyword = new \App\Qyweixin\Models\Keyword\Keyword();
        $this->modelQyweixinWord = new \App\Qyweixin\Models\Keyword\Word();
        $this->modelQyweixinKeywordToReplyMsg = new \App\Qyweixin\Models\Keyword\KeywordToReplyMsg();
        $this->modelQyweixinKeywordToAgentMsg = new \App\Qyweixin\Models\Keyword\KeywordToAgentMsg();
    }

    /**
     *
     * @return boolean
     */
    public function callbackAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/msg/callback?provider_appid=qy_ww975831ac45517bff&authorizer_appid=ww975831ac45517bff&agentid=999999
        // http://www.applicationmodule.com/qyweixin/api/msg/callback?provider_appid=qy_ww975831ac45517bff&authorizer_appid=ww975831ac45517bff&agentid=999999
        try {
            /**
             * ==================================================================================
             * ====================================以下逻辑请勿修改===============================
             * ==================================================================================
             */
            // 消息与事件接收URL
            $this->requestLogDatas['log_type'] = 'qymsglog';

            // 初始化
            $this->doInitializeLogic();

            $this->requestLogDatas['provider_appid'] = $this->provider_appid;
            $this->requestLogDatas['authorizer_appid'] = $this->authorizer_appid;

            $onlyRevieve = false;
            $AESInfo = array();
            $AESInfo['api'] = 'callback';
            $AESInfo['provider_appid'] = $this->provider_appid;
            $AESInfo['authorizer_appid'] = $this->authorizer_appid;
            $AESInfo['request_agent'] = $this->agentid;
            $AESInfo['msg_signature'] = isset($_GET['msg_signature']) ? $_GET['msg_signature'] : '';
            $AESInfo['timestamp'] = isset($_GET['timestamp']) ? trim(strtolower($_GET['timestamp'])) : '';
            $AESInfo['nonce'] = isset($_GET['nonce']) ? $_GET['nonce'] : '';
            $AESInfo['echostr'] = isset($_GET['echostr']) ? $_GET['echostr'] : '';

            // 如果是第3方服务商的话
            if (!empty($this->providerConfig)) {
                $verifyToken = isset($this->providerConfig['verify_token']) ? $this->providerConfig['verify_token'] : '';
                $encodingAESKey = isset($this->providerConfig['EncodingAESKey']) ? $this->providerConfig['EncodingAESKey'] : '';
                $receiveId = $this->provider_appid;
                $errorConfig = $this->providerConfig;
            } else {
                if (!empty($this->agentConfig)) {
                    $verifyToken = isset($this->agentConfig['verify_token']) ? $this->agentConfig['verify_token'] : '';
                    $encodingAESKey = isset($this->agentConfig['EncodingAESKey']) ? $this->agentConfig['EncodingAESKey'] : '';
                    $receiveId = $this->authorizer_appid;
                    $errorConfig = $this->agentConfig;
                } else {
                    $verifyToken = isset($this->authorizerConfig['verify_token']) ? $this->authorizerConfig['verify_token'] : '';
                    $encodingAESKey = isset($this->authorizerConfig['EncodingAESKey']) ? $this->authorizerConfig['EncodingAESKey'] : '';
                    $receiveId = $this->authorizer_appid;
                    $errorConfig = $this->authorizerConfig;
                }
            }
            $AESInfo['EncodingAESKey'] = $encodingAESKey;
            $AESInfo['verify_token'] = $verifyToken;
            $AESInfo['receiveId'] = $receiveId;
            $this->requestLogDatas['aes_info'] = $AESInfo;

            if (empty($verifyToken)) {
                throw new \Exception('application verify_token is null. config:' . \json_encode($errorConfig));
            }

            // get执行的代码
            if ($this->request->isGet()) {
                // 3.1 支持Http Get请求验证URL有效性
                // 假设企业的接收消息的URL设置为http://api.3dept.com。
                // 企业管理员在保存回调配置信息时，企业微信会发送一条验证消息到填写的URL，请求内容如下：
                // 请求方式：GET
                // 请求地址：http://api.3dept.com/?msg_signature=ASDFQWEXZCVAQFASDFASDFSS&timestamp=13500001234&nonce=123412323&echostr=ENCRYPT_STR
                // 参数说明：
                // 参数 类型 说明
                // msg_signature String 企业微信加密签名，msg_signature计算结合了企业填写的token、请求中的timestamp、nonce、加密的消息体。签名计算方法参考 消息体签名检验
                // timestamp Integer 时间戳。与nonce结合使用，用于防止请求重放攻击。
                // nonce String 随机数。与timestamp结合使用，用于防止请求重放攻击。
                // echostr String 加密的字符串。需要解密得到消息内容明文，解密后有random、msg_len、msg、receiveid四个字段，其中msg即为消息内容明文
                // 回调服务需要作出正确的响应才能通过URL验证，具体操作如下：
                // 对收到的请求，解析上述的各个参数值（参数值需要做Urldecode处理）
                // 根据已有的token，结合第1步获取的参数timestamp, nonce, echostr重新计算签名，然后与参数msg_signature检查是否一致，确认调用者的合法性。计算方法参考：消息体签名检验
                // 解密echostr参数得到消息内容（即msg字段）
                // 在1秒内响应GET请求，响应内容为上一步得到的明文消息内容（不能加引号，不能带bom头，不能带换行符）
                // 步骤2~3可以直接使用验证URL函数一步到位。
                // 你可以访问 接口调试工具 （接口类型：建立连接，接口列表：测试回调模式）进行调试
                // 合法性校验
                $ret4CheckSignature = $this->objQyWeixin->checkSignature($verifyToken, $encodingAESKey);
                if (empty($ret4CheckSignature)) {
                    $debug = \json_encode($this->requestLogDatas);
                    throw new \Exception('签名错误' . $debug);
                } else {
                    return $ret4CheckSignature['replyEchoStr'];
                }
            } elseif ($this->request->isPost()) {
                // 3.2 支持Http Post请求接收业务数据
                // 假设企业的接收消息的URL设置为http://api.3dept.com。
                // 当用户触发回调行为时，企业微信会发送回调消息到填写的URL，请求内容如下：
                // 请求方式：POST
                // 请求地址 ：http://api.3dept.com/?msg_signature=ASDFQWEXZCVAQFASDFASDFSS&timestamp=13500001234&nonce=123412323
                // 接收数据格式 ：
                // <xml>
                // <ToUserName><![CDATA[toUser]]></ToUserName>
                // <AgentID><![CDATA[toAgentID]]></AgentID>
                // <Encrypt><![CDATA[msg_encrypt]]></Encrypt>
                // </xml>
                // 参数说明：
                // 参数 类型 说明
                // msg_signature String 企业微信加密签名，msg_signature结合了企业填写的token、请求中的timestamp、nonce参数、加密的消息体
                // timestamp Integer 时间戳。与nonce结合使用，用于防止请求重放攻击。
                // nonce String 随机数。与timestamp结合使用，用于防止请求重放攻击。
                // ToUserName String 企业微信的CorpID，当为第三方应用回调事件时，CorpID的内容为suiteid
                // AgentID String 接收的应用id，可在应用的设置页面获取。仅应用相关的回调会带该字段。
                // Encrypt String 消息结构体加密后的字符串
                // 企业收到消息后，需要作如下处理：
                // 对msg_signature进行校验
                // 解密Encrypt，得到明文的消息结构体（消息结构体后面章节会详说）
                // 如果需要被动回复消息，构造被动响应包
                // 正确响应本次请求
                // · 企业微信服务器在五秒内收不到响应会断掉连接，并且重新发起请求，总共重试三次
                // · 当接收成功后，http头部返回200表示接收ok，其他错误码企业微信后台会一律当做失败并发起重试
                // 步骤1~2可以直接使用解密函数一步到位。
                // 步骤3其实包含加密被动回复消息、生成新签名、构造被动响应包三个步骤，可以直接使用加密函数一步到位。
                // 步骤4中，不同的业务回调要求返回不同内容。比如回复空串，或者特定字符串（如success），以及上一步构造的加密被动回复消息。具体要求在各个回调业务文档会有说明。
                // 被动响应包的数据格式：
                // <xml>
                // <Encrypt><![CDATA[msg_encrypt]]></Encrypt>
                // <MsgSignature><![CDATA[msg_signature]]></MsgSignature>
                // <TimeStamp>timestamp</TimeStamp>
                // <Nonce><![CDATA[nonce]]></Nonce>
                // </xml>
                // 参数说明
                // 参数 是否必须 说明
                // Encrypt 是 经过加密的消息结构体
                // MsgSignature 是 消息签名
                // TimeStamp 是 时间戳
                // Nonce 是 随机数，由企业自行生成
                // post执行的代码
                // 签名正确，将接受到的xml转化为数组数据并记录数据
                $datas = $this->getDataFromWeixinServer();
                foreach ($datas as $dtkey => $dtvalue) {
                    $this->requestLogDatas[$dtkey] = $dtvalue;
                }
                $this->requestLogDatas['response'] = 'success';

                // 开始处理相关的业务逻辑
                $AgentID = isset($datas['AgentID']) ? trim($datas['AgentID']) : '0';
                $this->requestLogDatas['AgentID'] = $AgentID;

                $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
                $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
                $content = isset($datas['Content']) ? trim($datas['Content']) : '';
                $MsgId = isset($datas['MsgId']) ? trim($datas['MsgId']) : '';
                $CreateTime = isset($datas['CreateTime']) ? ($datas['CreateTime']) : time();

                // 关于重试的消息排重，有msgid的消息推荐使用msgid排重。事件类型消息推荐使用FromUserName + CreateTime 排重。
                if (!empty($MsgId)) {
                    $uniqueKey = $MsgId . "-" . $this->provider_appid . "-" . $this->authorizer_appid . "-" . $AgentID;
                } else {
                    $uniqueKey = $FromUserName . "-" . $CreateTime . "-" . $this->provider_appid . "-" . $this->authorizer_appid . "-" . $AgentID;
                }
                $this->requestLogDatas['lock_uniqueKey'] = $uniqueKey;
                if (!empty($uniqueKey)) {
                    $objLock = new \iLock(md5($uniqueKey), false);
                    if ($objLock->lock()) {
                        return "success";
                    }
                }
                // 如果有AgentId的话那么重新获取接口对象
                if (!empty($AgentID) && ($AgentID != $this->agentid)) {
                    // 创建service
                    $this->qyweixinService = new \App\Qyweixin\Services\QyService($this->authorizer_appid, $this->provider_appid, $AgentID);
                    $this->objQyWeixin = $this->qyweixinService->getQyWeixinObject();
                }
                // // 获取微信用户的个人信息
                // if (!empty($this->authorizerConfig['access_token'])) {
                $this->modelQyweixinUser->setQyweixinInstance($this->objQyWeixin);
                $this->modelQyweixinUser->updateUserInfoByAction($FromUserName, $this->authorizer_appid, $this->provider_appid);
                // }
                // 设定来源和目标用户的openid
                $this->objQyWeixin->setFromAndTo($FromUserName, $ToUserName);

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

                // $e = new \Exception("Post请求" . \json_encode($datas));
                // $this->modelErrorLog->log($this->activity_id, $e, $this->now);
                // return '';

                /**
                 * ==================================================================================
                 * ====================================以下逻辑请勿修改===================================
                 * ==================================================================================
                 */
                if ($onlyRevieve) {
                    return "";
                }

                if ($content == 'debug') {
                    $response = $this->objQyWeixin->getReplyManager()->replyText($this->debugVar($datas));
                }

                if (empty($response)) {
                    $response = $this->answer($FromUserName, $ToUserName, $content, $this->authorizer_appid, $this->provider_appid, $AgentID);
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
            }
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
        // http://www.applicationmodule.com/qyweixin/api/provider/test-decrypt-msg?appid=wxca8519f703c07d32
        try {
            // 初始化
            $this->doInitializeLogic();

            // <xml>
            // <AppId><![CDATA[wx7d9829b9bb066fe5]]></AppId>
            // <Encrypt><![CDATA[zVOkgBq+VNnmXwyDs9AIUymWt2P2cemjBrDROoeIu39Bb3FpqJ7+bTcwZtQL7sGfoZZHJ2DdGD7NKON3KpQfYorrm2bQAadlabSXHpgaZytcCPWvOLBLce2viKU0mBP7LTD45ASZ08evyuhSxU3WmNsi+WooxSRv6LjqnSyfg0qJbpfDTTBqOUok1Y11snMofp8YsHBfgh06zRdQjXw5au0z92dv4sdZVEwN2Fl83AlqrfbaLcvZbNSdY2/yKN4fZGMlOhF571h/AC6E/4IpBfCbKjfurd5ZYzBjmELRnR7fXuI8CsShV+ygRK2ResIqL+n20RbXOOOm3JNtZDrPilZggAvEL68NBLDaAvwLHuAMq+/9gR/vf9OhN3mCIcvnsJy/mMdnDebzPMJFcdmOw5ZSNgSrEnwgnfLRfBzyXCPYKQMJtrkOAE4orlhLUXo2CGHYvHoMwhz95PzXorIvsA==]]></Encrypt>
            // </xml>
            $verifyToken = isset($this->providerConfig['verify_token']) ? $this->providerConfig['verify_token'] : '';
            $encodingAesKey = isset($this->providerConfig['EncodingAESKey']) ? $this->providerConfig['EncodingAESKey'] : '';
            $AppId = $this->provider_appid;

            // die($encodingAesKey.strlen($encodingAesKey));
            $pc = new \Qyweixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($verifyToken, $encodingAesKey, $AppId);

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
        // http://www.applicationmodule.com/qyweixin/api/provider/test-get-user?provider_appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&FromUserName=o8IA5v7Dwz8tk_EcVsRITf7fA9Fk
        try {
            // 初始化
            $this->doInitializeLogic();

            $FromUserName = isset($_GET['FromUserName']) ? trim($_GET['FromUserName']) : '';

            $this->modelQyweixinUser->setQyweixinInstance($this->objQyWeixin);
            $ret = $this->modelQyweixinUser->updateUserInfoByAction($FromUserName, $this->authorizer_appid, $this->provider_appid);

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
        // http://www.applicationmodule.com/qyweixin/api/provider/test-keyword?provider_appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=xxx

        // http://www.applicationmodule.com/qyweixin/api/provider/test-keyword?provider_appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试文本1
        // http://www.applicationmodule.com/qyweixin/api/provider/test-keyword?provider_appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试图片1
        // http://www.applicationmodule.com/qyweixin/api/provider/test-keyword?provider_appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试语音1
        // http://www.applicationmodule.com/qyweixin/api/provider/test-keyword?provider_appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试视频1
        // http://www.applicationmodule.com/qyweixin/api/provider/test-keyword?provider_appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试音乐1
        // http://www.applicationmodule.com/qyweixin/api/provider/test-keyword?provider_appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试单图文1
        // http://www.applicationmodule.com/qyweixin/api/provider/test-keyword?provider_appid=wxca8519f703c07d32&authorizer_appid=wxe735383666834fc9&keyword=测试多图文1
        try {
            // 初始化
            $this->doInitializeLogic();
            $agentid = isset($_GET['agentid']) ? trim($_GET['agentid']) : '0';
            $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

            // 设定来源和目标用户的openid
            $this->objQyWeixin->setFromAndTo("FromUserName", "ToUserName");

            $response = $this->answer("FromUserName", "ToUserName", $keyword, $this->authorizer_appid, $this->provider_appid, $agentid);

            return $this->result("OK", $response);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        // 第三方服务商运用ID
        $this->provider_appid = isset($_GET['provider_appid']) ? trim($_GET['provider_appid']) : "";
        $this->authorizer_appid = isset($_GET['authorizer_appid']) ? trim($_GET['authorizer_appid']) : "";
        $this->agentid = isset($_GET['agentid']) ? intval($_GET['agentid']) : 0;

        // 创建service
        $this->qyweixinService = new \App\Qyweixin\Services\QyService($this->authorizer_appid, $this->provider_appid, $this->agentid);

        if (!empty($this->provider_appid)) {
            $this->providerConfig = $this->qyweixinService->getAppConfig4Provider();
            if (empty($this->providerConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}所对应的记录不存在");
            }
            $this->objQyWeixinProvider = $this->qyweixinService->getQyweixinProvider();
        }

        // 授权方ID
        if (!empty($this->authorizer_appid)) {
            $this->authorizerConfig = $this->qyweixinService->getAppConfig4Authorizer();
            if (empty($this->authorizerConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
            }
            $this->objQyWeixin = $this->qyweixinService->getQyWeixinObject();
        }

        // 应用ID
        if (!empty($this->agentid)) {
            $this->agentConfig = $this->qyweixinService->getAccessToken4Agent();
            if (empty($this->agentConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}和authorizer_appid:{$this->authorizer_appid}和agentid:{$this->agentid}所对应的记录不存在");
            }
            $this->objQyWeixin = $this->qyweixinService->getQyWeixinObject();
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
            $pc = new \Qyweixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($this->requestLogDatas['aes_info']['verify_token'], $this->requestLogDatas['aes_info']['EncodingAESKey'], $this->requestLogDatas['aes_info']['receiveId']);
            $errCode = $pc->decryptMsg($this->requestLogDatas['aes_info']['msg_signature'], $this->requestLogDatas['aes_info']['timestamp'], $this->requestLogDatas['aes_info']['nonce'], $postStr, $decryptMsg);
            if (empty($errCode)) {
                $datas = $this->revieve($decryptMsg);
                $this->requestLogDatas['aes_info']['decryptMsg'] = $decryptMsg;
            } else {
                throw new \Exception('application EncodingAESKey is failure in decryptMsg, appid:' . $this->requestLogDatas['aes_info']['receiveId']);
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
                $pc = new \Qyweixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($this->requestLogDatas['aes_info']['verify_token'], $this->requestLogDatas['aes_info']['EncodingAESKey'], $this->requestLogDatas['aes_info']['receiveId']);
                $errCode = $pc->encryptMsg($response, $timeStamp, $nonce, $encryptMsg);

                if (empty($errCode)) {
                    $response = $encryptMsg;
                } else {
                    throw new \Exception('application EncodingAESKey is failure in encryptMsg, appid:' . $this->requestLogDatas['aes_info']['receiveId']);
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
        $AgentID = isset($datas['AgentID']) ? trim($datas['AgentID']) : '';
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

        /**
         * 消息格式
         * 文本消息
         * 图片消息
         * 语音消息
         * 视频消息
         * 位置消息
         * 链接消息
         * 开启接收消息模式后，企业成员在企业微信应用里发送消息时，企业微信会将消息同步到企业应用的后台。
         * 如何接收消息已经在使用接收消息说明，本小节是对普通消息结构体的说明。
         * 消息类型支持：文本、图片、语音、视频、位置以及链接信息。
         *
         * 注：以下出现的xml包仅是接收的消息包中的Encrypt参数解密后的内容说明
         */
        // 文本逻辑开始
        elseif ($MsgType == 'text') { // 接收普通消息----文本消息

            /**
             * 文本消息
             * 消息示例：
             *
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1348831860</CreateTime>
             * <MsgType><![CDATA[text]]></MsgType>
             * <Content><![CDATA[this is a test]]></Content>
             * <MsgId>1234567890123456</MsgId>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：text
             * Content 文本消息内容
             * MsgId 消息id，64位整型
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $content = isset($datas['Content']) ? strtolower(trim($datas['Content'])) : '';

            // if ($content == "客服测试") {
            // $response = $this->objQyWeixin->getMsgManager()
            // ->getReplySender()
            // ->replyCustomerService();
            // }
        } // 文本逻辑结束

        // 图片逻辑开始
        elseif ($MsgType == 'image') { // 接收普通消息----图片消息
            /**
             * 图片消息
             * 消息示例：
             *
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1348831860</CreateTime>
             * <MsgType><![CDATA[image]]></MsgType>
             * <PicUrl><![CDATA[this is a url]]></PicUrl>
             * <MediaId><![CDATA[media_id]]></MediaId>
             * <MsgId>1234567890123456</MsgId>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：image
             * PicUrl 图片链接
             * MediaId 图片媒体文件id，可以调用获取媒体文件接口拉取，仅三天内有效
             * MsgId 消息id，64位整型
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $PicUrl = isset($datas['PicUrl']) ? trim($datas['PicUrl']) : '';
            $MediaId = isset($datas['MediaId']) ? trim($datas['MediaId']) : '';

            // 使用闭包，提高相应速度
            // $content = '默认图片回复';
        } // 图片逻辑结束

        // 语音逻辑开始
        elseif ($MsgType == 'voice') { // 接收普通消息----语音消息 或者接收语音识别结果
            /**
             * 语音消息
             * 消息示例：
             *
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1357290913</CreateTime>
             * <MsgType><![CDATA[voice]]></MsgType>
             * <MediaId><![CDATA[media_id]]></MediaId>
             * <Format><![CDATA[Format]]></Format>
             * <MsgId>1234567890123456</MsgId>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：voice
             * MediaId 语音媒体文件id，可以调用获取媒体文件接口拉取数据，仅三天内有效
             * Format 语音格式，如amr，speex等
             * MsgId 消息id，64位整型
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $MediaId = isset($datas['MediaId']) ? trim($datas['MediaId']) : '';
            $Format = isset($datas['Format']) ? trim($datas['Format']) : '';

            // $content = '默认语音回复';
        } // 语音逻辑结束

        // 视频逻辑开始
        elseif ($MsgType == 'video') { // 接收普通消息----视频消息
            /**
             * 视频消息
             * 消息示例：
             *
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1357290913</CreateTime>
             * <MsgType><![CDATA[video]]></MsgType>
             * <MediaId><![CDATA[media_id]]></MediaId>
             * <ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
             * <MsgId>1234567890123456</MsgId>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：video
             * MediaId 视频媒体文件id，可以调用获取媒体文件接口拉取数据，仅三天内有效
             * ThumbMediaId 视频消息缩略图的媒体id，可以调用获取媒体文件接口拉取数据，仅三天内有效
             * MsgId 消息id，64位整型
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $MediaId = isset($datas['MediaId']) ? trim($datas['MediaId']) : '';
            $ThumbMediaId = isset($datas['ThumbMediaId']) ? trim($datas['ThumbMediaId']) : '';
        } // 视频逻辑结束

        // 地理位置逻辑开始
        elseif ($MsgType == 'location') { // 接收普通消息----地理位置消息
            /**
             * 位置消息
             * 消息示例：
             *
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1351776360</CreateTime>
             * <MsgType><![CDATA[location]]></MsgType>
             * <Location_X>23.134</Location_X>
             * <Location_Y>113.358</Location_Y>
             * <Scale>20</Scale>
             * <Label><![CDATA[位置信息]]></Label>
             * <MsgId>1234567890123456</MsgId>
             * <AgentID>1</AgentID>
             * <AppType><![CDATA[wxwork]]></AppType>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为： location
             * Location_X 地理位置纬度
             * Location_Y 地理位置经度
             * Scale 地图缩放大小
             * Label 地理位置信息
             * MsgId 消息id，64位整型
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             * AppType app类型，在企业微信固定返回wxwork，在微信不返回该字段
             */
            $Location_X = isset($datas['Location_X']) ? trim($datas['Location_X']) : 0;
            $Location_Y = isset($datas['Location_Y']) ? trim($datas['Location_Y']) : 0;
            $Scale = isset($datas['Scale']) ? trim($datas['Scale']) : 0;
            $Label = isset($datas['Label']) ? trim($datas['Label']) : '';
            $AppType = isset($datas['AppType']) ? trim($datas['AppType']) : '';

            // $articles = $this->shopLocation($Location_X, $Location_Y);
            // if (! empty($articles)) {
            // $response = $this->objQyWeixin->getMsgManager()
            // ->getReplySender()
            // ->replyGraphText($articles);
            // }
        } // 地理位置逻辑结束

        // 链接逻辑开始
        elseif ($MsgType == 'link') { // 接收普通消息----链接消息
            /**
             * 链接消息
             * 消息示例：
             *
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[fromUser]]></FromUserName>
             * <CreateTime>1348831860</CreateTime>
             * <MsgType><![CDATA[link]]></MsgType>
             * <Title><![CDATA[this is a title！]]></Title>
             * <Description><![CDATA[this is a description！]]></Description>
             * <Url><![CDATA[URL]]></Url>
             * <PicUrl><![CDATA[this is a url]]></PicUrl>
             * <MsgId>1234567890123456</MsgId>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：link
             * Title 标题
             * Description 描述
             * Url 链接跳转的url
             * PicUrl 封面缩略图的url
             * MsgId 消息id，64位整型
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             * </xml>
             */
            $Title = isset($datas['Title']) ? trim($datas['Title']) : '';
            $Description = isset($datas['Description']) ? trim($datas['Description']) : '';
            $Url = isset($datas['Url']) ? trim($datas['Url']) : '';
            $PicUrl = isset($datas['PicUrl']) ? trim($datas['PicUrl']) : '';
        } // 链接逻辑结束

        // 不同项目特定的业务逻辑结束

        $datas['content_process'] = $content;
        $datas['response'] = $response;
        return $datas;
    }

    /**
     * 事件格式
     * 成员关注及取消关注事件
     * 进入应用
     * 上报地理位置
     * 异步任务完成事件推送
     * 通讯录变更事件
     * 新增成员事件
     * 更新成员事件
     * 删除成员事件
     * 新增部门事件
     * 更新部门事件
     * 删除部门事件
     * 标签成员变更事件
     * 菜单事件
     * 点击菜单拉取消息的事件推送
     * 点击菜单跳转链接的事件推送
     * 扫码推事件的事件推送
     * 扫码推事件且弹出“消息接收中”提示框的事件推送
     * 弹出系统拍照发图的事件推送
     * 弹出拍照或者相册发图的事件推送
     * 弹出微信相册发图器的事件推送
     * 弹出地理位置选择器的事件推送
     * 审批状态通知事件
     * 任务卡片事件推送
     * 开启接收消息模式后，可以配置接收事件消息。
     * 当企业成员通过企业微信APP或微工作台（原企业号）触发进入应用、上报地理位置、点击菜单等事件时，企业微信会将这些事件消息发送给企业后台。
     * 如何接收消息已经在使用接收消息说明，本小节是对事件消息结构体的说明。
     *
     * 注：以下出现的xml包仅是接收的消息包中的Encrypt参数解密后的内容说明
     * 通讯录变更事件
     * 当企业通过通讯录助手开通通讯录权限后，成员的变更会通知给企业。变更的事件，将推送到企业微信管理端通讯录助手中的‘接收事件服务器’。由通讯录同步助手调用接口触发的变更事件不回调通讯录同步助手本身。管理员在管理端更改组织架构或者成员信息以及企业微信的成员在客户端变更自己的个人信息将推送给通讯录同步助手。第三方通讯录变更事件参见第三方回调协议
     * 菜单事件
     * 成员点击自定义菜单后，企业微信会把点击事件推送给应用。
     * 点击菜单弹出子菜单，不会产生上报。
     * 企业微信iPhone1.2.2/Android1.2.2版本开始支持菜单事件，旧版本企业微信成员点击后将没有回应，应用不能正常接收到事件推送。
     * 自定义菜单可以在管理后台的应用设置界面配置。
     *
     * @param array $datas            
     * @return array
     */
    protected function handleRequestAndGetResponseByEvent(array $datas)
    {
        $AgentID = isset($datas['AgentID']) ? trim($datas['AgentID']) : '';
        $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
        $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
        $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
        $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
        $EventKey = isset($datas['EventKey']) ? trim($datas['EventKey']) : '';
        $CreateTime = isset($datas['CreateTime']) ? ($datas['CreateTime']) : time();
        $ChangeType = isset($datas['ChangeType']) ? trim($datas['ChangeType']) : '';
        $content = "";
        $response = '';

        // 不同项目特定的业务逻辑开始
        // 接收事件推送
        if ($Event == 'subscribe' || $Event == 'unsubscribe') { // 成员关注及取消关注事件
            /**
             * 成员关注及取消关注事件
             * 小程序在管理端开启接收消息配置后，也可收到关注/取消关注事件
             * 本事件触发时机为：
             *
             * 成员已经加入企业，管理员添加成员到应用可见范围(或移除可见范围)时
             * 成员已经在应用可见范围，成员加入(或退出)企业时
             * 请求示例：
             *
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[UserID]]></FromUserName>
             * <CreateTime>1348831860</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[subscribe]]></Event>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型，subscribe(关注)、unsubscribe(取消关注)
             * EventKey 事件KEY值，此事件该值为空
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            if (empty($content)) {
                $content = '首访回复';
            }
        } elseif ($Event == 'enter_agent') { // 进入应用事件
            /**
             * 进入应用
             * 本事件在成员进入企业企业微信应用时触发
             *
             * 请求示例：
             *
             * <xml><ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>1408091189</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[enter_agent]]></Event>
             * <EventKey><![CDATA[]]></EventKey>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：enter_agent
             * EventKey 事件KEY值，此事件该值为空
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $response = '';
        } elseif ($Event == 'LOCATION') { // 上报地理位置事件

            /**
             * 上报地理位置
             * 成员同意上报地理位置后，每次在进入应用会话时都会上报一次地理位置。
             * 企业可以在管理端修改应用是否需要获取地理位置权限。
             *
             * 请求示例：
             *
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[LOCATION]]></Event>
             * <Latitude>23.104</Latitude>
             * <Longitude>113.320</Longitude>
             * <Precision>65.000</Precision>
             * <AgentID>1</AgentID>
             * <AppType><![CDATA[wxwork]]></AppType>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：LOCATION
             * Latitude 地理位置纬度
             * Longitude 地理位置经度
             * Precision 地理位置精度
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             * AppType app类型，在企业微信固定返回wxwork，在微信不返回该字段
             */
            $Latitude = isset($datas['Latitude']) ? floatval($datas['Latitude']) : 0;
            $Longitude = isset($datas['Longitude']) ? floatval($datas['Longitude']) : 0;
            $Precision = isset($datas['Precision']) ? floatval($datas['Precision']) : 0;
            $AppType = isset($datas['AppType']) ? floatval($datas['AppType']) : '';
            $onlyRevieve = true;
            $response = "success";
        } elseif ($Event == 'click') { // 自定义菜单事件推送-点击菜单拉取消息的事件推送

            /**
             * 点击菜单拉取消息的事件推送
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[click]]></Event>
             * <EventKey><![CDATA[EVENTKEY]]></EventKey>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：click
             * EventKey 事件KEY值，与自定义菜单接口中KEY值对应
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $content = $EventKey;
        } elseif ($Event == 'view') { // 自定义菜单事件推送-点击菜单跳转链接的事件推送

            /**
             * 点击菜单跳转链接的事件推送
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[view]]></Event>
             * <EventKey><![CDATA[www.qq.com]]></EventKey>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：view
             * EventKey 事件KEY值，设置的跳转URL
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $content = $EventKey;
        } elseif ($Event == 'scancode_push') { // 自定义菜单事件推送 -扫码推事件的事件推送

            /**
             * 扫码推事件的事件推送
             * <xml><ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>1408090502</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[scancode_push]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <ScanCodeInfo><ScanType><![CDATA[qrcode]]></ScanType>
             * <ScanResult><![CDATA[1]]></ScanResult>
             * </ScanCodeInfo>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：scancode_push
             * EventKey 事件KEY值，与自定义菜单接口中KEY值对应
             * ScanCodeInfo 扫描信息
             * ScanType 扫描类型，一般是qrcode
             * ScanResult 扫描结果，即二维码对应的字符串信息
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $ScanType = isset($datas['ScanCodeInfo']['ScanType']) ? trim($datas['ScanCodeInfo']['ScanType']) : "";
            $ScanResult = isset($datas['ScanCodeInfo']['ScanResult']) ? trim($datas['ScanCodeInfo']['ScanResult']) : "";
            $content = $EventKey;
        } elseif ($Event == 'scancode_waitmsg') { // 自定义菜单事件推送 -扫码推事件且弹出“消息接收中”提示框的事件推送

            /**
             * 扫码推事件且弹出“消息接收中”提示框的事件推送
             * <xml><ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>1408090606</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[scancode_waitmsg]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <ScanCodeInfo><ScanType><![CDATA[qrcode]]></ScanType>
             * <ScanResult><![CDATA[2]]></ScanResult>
             * </ScanCodeInfo>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：scancode_waitmsg
             * EventKey 事件KEY值，与自定义菜单接口中KEY值对应
             * ScanCodeInfo 扫描信息
             * ScanType 扫描类型，一般是qrcode
             * ScanResult 扫描结果，即二维码对应的字符串信息
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $ScanType = isset($datas['ScanCodeInfo']['ScanType']) ? trim($datas['ScanCodeInfo']['ScanType']) : "";
            $ScanResult = isset($datas['ScanCodeInfo']['ScanResult']) ? trim($datas['ScanCodeInfo']['ScanResult']) : "";
            $content = $EventKey;
        } elseif ($Event == 'pic_sysphoto') { // 自定义菜单事件推送 -弹出系统拍照发图的事件推送

            /**
             * 弹出系统拍照发图的事件推送
             * <xml><ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>1408090651</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[pic_sysphoto]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <SendPicsInfo><Count>1</Count>
             * <PicList><item><PicMd5Sum><![CDATA[1b5f7c23b5bf75682a53e7b6d163e185]]></PicMd5Sum>
             * </item>
             * </PicList>
             * </SendPicsInfo>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：pic_sysphoto
             * EventKey 事件KEY值，与自定义菜单接口中KEY值对应
             * SendPicsInfo 发送的图片信息
             * Count 发送的图片数量
             * PicList 图片列表
             * PicMd5Sum 图片的MD5值，开发者若需要，可用于验证接收到图片
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $Count = isset($datas['SendPicsInfo']['Count']) ? trim($datas['SendPicsInfo']['Count']) : 0;
            $PicList = isset($datas['SendPicsInfo']['PicList']) ? trim($datas['SendPicsInfo']['PicList']) : "";
            $content = $EventKey;
        } elseif ($Event == 'pic_photo_or_album') { // 自定义菜单事件推送 -弹出拍照或者相册发图的事件推送

            /**
             * 弹出拍照或者相册发图的事件推送
             * <xml><ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>1408090816</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[pic_photo_or_album]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <SendPicsInfo><Count>1</Count>
             * <PicList><item><PicMd5Sum><![CDATA[5a75aaca956d97be686719218f275c6b]]></PicMd5Sum>
             * </item>
             * </PicList>
             * </SendPicsInfo>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：pic_photo_or_album
             * EventKey 事件KEY值，与自定义菜单接口中KEY值对应
             * SendPicsInfo 发送的图片信息
             * Count 发送的图片数量
             * PicList 图片列表
             * PicMd5Sum 图片的MD5值，开发者若需要，可用于验证接收到图片
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $Count = isset($datas['SendPicsInfo']['Count']) ? trim($datas['SendPicsInfo']['Count']) : 0;
            $PicList = isset($datas['SendPicsInfo']['PicList']) ? trim($datas['SendPicsInfo']['PicList']) : "";
            $content = $EventKey;
        } elseif ($Event == 'pic_weixin') { // 自定义菜单事件推送 -弹出微信相册发图器的事件推送

            /**
             * 弹出微信相册发图器的事件推送
             * <xml><ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>1408090816</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[pic_weixin]]></Event>
             * <EventKey><![CDATA[6]]></EventKey>
             * <SendPicsInfo><Count>1</Count>
             * <PicList><item><PicMd5Sum><![CDATA[5a75aaca956d97be686719218f275c6b]]></PicMd5Sum>
             * </item>
             * </PicList>
             * </SendPicsInfo>
             * <AgentID>1</AgentID>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：pic_weixin
             * EventKey 事件KEY值，与自定义菜单接口中KEY值对应
             * SendPicsInfo 发送的图片信息
             * Count 发送的图片数量
             * PicList 图片列表
             * PicMd5Sum 图片的MD5值，开发者若需要，可用于验证接收到图片
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             */
            $Count = isset($datas['SendPicsInfo']['Count']) ? trim($datas['SendPicsInfo']['Count']) : 0;
            $PicList = isset($datas['SendPicsInfo']['PicList']) ? trim($datas['SendPicsInfo']['PicList']) : "";
            $content = $EventKey;
        } elseif ($Event == 'location_select') { // 自定义菜单事件推送 -弹出地理位置选择器的事件推送

            /**
             * 弹出地理位置选择器的事件推送
             * 请求示例：
             *
             * <xml><ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
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
             * <AgentID>1</AgentID>
             * <AppType><![CDATA[wxwork]]></AppType>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：location_select
             * EventKey 事件KEY值，与自定义菜单接口中KEY值对应
             * SendLocationInfo 发送的位置信息
             * Location_X X坐标信息
             * Location_Y Y坐标信息
             * Scale 精度，可理解为精度或者比例尺、越精细的话 scale越高
             * Label 地理位置的字符串信息
             * Poiname POI的名字，可能为空
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             * AppType app类型，在企业微信固定返回wxwork，在微信不返回该字段
             */
            $Location_X = isset($datas['SendLocationInfo']['Location_X']) ? trim($datas['SendLocationInfo']['Location_X']) : 0;
            $Location_Y = isset($datas['SendLocationInfo']['Location_Y']) ? trim($datas['SendLocationInfo']['Location_Y']) : 0;
            $Scale = isset($datas['SendLocationInfo']['Scale']) ? trim($datas['SendLocationInfo']['Scale']) : 0;
            $Label = isset($datas['SendLocationInfo']['Label']) ? trim($datas['SendLocationInfo']['Label']) : "";
            $Poiname = isset($datas['SendLocationInfo']['Poiname']) ? trim($datas['SendLocationInfo']['Poiname']) : "";
            $AppType = isset($datas['AppType']) ? trim($datas['AppType']) : "";

            $content = $EventKey;

            // $articles = $this->shopLocation($Location_X, $Location_Y);
            // if (! empty($articles)) {
            // $response = $this->objQyWeixin->getMsgManager()
            // ->getReplySender()
            // ->replyGraphText($articles);
            // }
        } elseif ($Event == 'taskcard_click') { // 任务卡片事件推送
            /**
             * 任务卡片事件推送
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>123456789</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[taskcard_click]]></Event>
             * <EventKey><![CDATA[key111]]></EventKey>
             * <TaskId><![CDATA[taskid111]]></TaskId >
             * <AgentId>1</AgentId>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：taskcard_click，点击任务卡片按钮
             * EventKey 与发送任务卡片消息时指定的按钮btn:key值相同
             * TaskId 与发送任务卡片消息时指定的task_id相同
             * AgentId 企业应用的id，整型。可在应用的设置页面查看
             */
            $TaskId = isset($datas['TaskId']) ? $datas['TaskId'] : '';
            $content = $EventKey;
        } elseif ($Event == 'change_contact') { // 通讯录回调通知
            if ($ChangeType == 'create_user') { // 成员变更通知-新增成员事件
                /**
                 * 新增成员事件
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_contact]]></Event>
                 * <ChangeType>create_user</ChangeType>
                 * <UserID><![CDATA[zhangsan]]></UserID>
                 * <Name><![CDATA[张三]]></Name>
                 * <Department><![CDATA[1,2,3]]></Department>
                 * <IsLeaderInDept><![CDATA[1,0,0]]></IsLeaderInDept>
                 * <Position><![CDATA[产品经理]]></Position>
                 * <Mobile>13800000000</Mobile>
                 * <Gender>1</Gender>
                 * <Email><![CDATA[zhangsan@gzdev.com]]></Email>
                 * <Status>1</Status>
                 * <Avatar><![CDATA[http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/0]]></Avatar>
                 * <Alias><![CDATA[zhangsan]]></Alias>
                 * <Telephone><![CDATA[020-123456]]></Telephone>
                 * <Address><![CDATA[广州市]]></Address>
                 * <ExtAttr>
                 * <Item>
                 * <Name><![CDATA[爱好]]></Name>
                 * <Type>0</Type>
                 * <Text>
                 * <Value><![CDATA[旅游]]></Value>
                 * </Text>
                 * </Item>
                 * <Item>
                 * <Name><![CDATA[卡号]]></Name>
                 * <Type>1</Type>
                 * <Web>
                 * <Title><![CDATA[企业微信]]></Title>
                 * <Url><![CDATA[https://work.weixin.qq.com]]></Url>
                 * </Web>
                 * </Item>
                 * </ExtAttr>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_contact
                 * ChangeType 此时固定为create_user
                 * UserID 成员UserID
                 * Name 成员名称
                 * Department 成员部门列表，仅返回该应用有查看权限的部门id
                 * IsLeaderInDept 表示所在部门是否为上级，0-否，1-是，顺序与Department字段的部门逐一对应
                 * Mobile 手机号码
                 * Position 职位信息。长度为0~64个字节
                 * Gender 性别，1表示男性，2表示女性
                 * Email 邮箱
                 * Status 激活状态：1=已激活 2=已禁用 4=未激活 已激活代表已激活企业微信或已关注微工作台（原企业号）。
                 * Avatar 头像url。注：如果要获取小图将url最后的”/0”改成”/100”即可。
                 * Alias 成员别名
                 * Telephone 座机
                 * Address 地址
                 * ExtAttr 扩展属性
                 * Type 扩展属性类型: 0-本文 1-网页
                 * Text 文本属性类型，扩展属性类型为0时填写
                 * Value 文本属性内容
                 * Web 网页类型属性，扩展属性类型为1时填写
                 * Title 网页的展示标题
                 * Url 网页的url
                 * 说明： 由通讯录同步助手通过api发起的新增成员触发的事件不回调给通讯录同步助手应用。
                 */
            } elseif ($ChangeType == 'update_user') { // 成员变更通知-更新成员事件
                /**
                 * 更新成员事件
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_contact]]></Event>
                 * <ChangeType>update_user</ChangeType>
                 * <UserID><![CDATA[zhangsan]]></UserID>
                 * <NewUserID><![CDATA[zhangsan001]]></NewUserID>
                 * <Name><![CDATA[张三]]></Name>
                 * <Department><![CDATA[1,2,3]]></Department>
                 * <IsLeaderInDept><![CDATA[1,0,0]]></IsLeaderInDept>
                 * <Position><![CDATA[产品经理]]></Position>
                 * <Mobile>13800000000</Mobile>
                 * <Gender>1</Gender>
                 * <Email><![CDATA[zhangsan@gzdev.com]]></Email>
                 * <Status>1</Status>
                 * <Avatar><![CDATA[http://wx.qlogo.cn/mmopen/ajNVdqHZLLA3WJ6DSZUfiakYe37PKnQhBIeOQBO4czqrnZDS79FH5Wm5m4X69TBicnHFlhiafvDwklOpZeXYQQ2icg/0]]></Avatar>
                 * <Alias><![CDATA[zhangsan]]></Alias>
                 * <Telephone><![CDATA[020-123456]]></Telephone>
                 * <Address><![CDATA[广州市]]></Address>
                 * <ExtAttr>
                 * <Item>
                 * <Name><![CDATA[爱好]]></Name>
                 * <Type>0</Type>
                 * <Text>
                 * <Value><![CDATA[旅游]]></Value>
                 * </Text>
                 * </Item>
                 * <Item>
                 * <Name><![CDATA[卡号]]></Name>
                 * <Type>1</Type>
                 * <Web>
                 * <Title><![CDATA[企业微信]]></Title>
                 * <Url><![CDATA[https://work.weixin.qq.com]]></Url>
                 * </Web>
                 * </Item>
                 * </ExtAttr>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_contact
                 * ChangeType 此时固定为update_user
                 * UserID 变更信息的成员UserID
                 * NewUserID 新的UserID，变更时推送（userid由系统生成时可更改一次）
                 * Name 成员名称，变更时推送
                 * Department 成员部门列表，变更时推送，仅返回该应用有查看权限的部门id
                 * IsLeaderInDept 表示所在部门是否为上级，0-否，1-是，顺序与Department字段的部门逐一对应
                 * Mobile 手机号码，变更时推送
                 * Position 职位信息。长度为0~64个字节，变更时推送
                 * Gender 性别，变更时推送。1表示男性，2表示女性
                 * Email 邮箱，变更时推送
                 * Status 激活状态：1=激活或关注， 2=禁用， 4=未激活（重新启用未激活用户或者退出企业并且取消关注时触发）
                 * Avatar 头像url。注：如果要获取小图将url最后的”/0”改成”/100”即可。变更时推送
                 * Alias 成员别名，变更时推送
                 * Telephone 座机，变更时推送
                 * Address 地址
                 * ExtAttr 扩展属性，变更时推送
                 * Type 扩展属性类型: 0-本文 1-网页
                 * Text 文本属性类型，扩展属性类型为0时填写
                 * Value 文本属性内容
                 * Web 网页类型属性，扩展属性类型为1时填写
                 * Title 网页的展示标题
                 * Url 网页的url
                 * 说明： 由通讯录同步助手通过api发起的更新成员触发的事件不回调给通讯录同步助手应用。
                 */
            } elseif ($ChangeType == 'delete_user') { // 成员变更通知-删除成员事件
                /**
                 * 删除成员事件
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_contact]]></Event>
                 * <ChangeType>delete_user</ChangeType>
                 * <UserID><![CDATA[zhangsan]]></UserID>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_contact
                 * ChangeType 此时固定为delete_user
                 * UserID 变更信息的成员UserID
                 * 说明： 由通讯录同步助手通过api发起的删除成员触发的事件不回调给通讯录同步助手应用。
                 */
            } elseif ($ChangeType == 'create_party') { // 部门变更通知-新增部门事件
                /**
                 * 新增部门事件
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_contact]]></Event>
                 * <ChangeType>create_party</ChangeType>
                 * <Id>2</Id>
                 * <Name><![CDATA[张三]]></Name>
                 * <ParentId><![CDATA[1]]></ParentId>
                 * <Order>1</Order>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_contact
                 * ChangeType 此时固定为create_party
                 * Id 部门Id
                 * Name 部门名称
                 * ParentId 父部门id
                 * Order 部门排序
                 * 说明： 由通讯录同步助手通过api发起的新增部门触发的事件不回调给通讯录同步助手应用。
                 */
            } elseif ($ChangeType == 'update_party') { // 部门变更通知-更新部门事件
                /**
                 * 更新部门事件
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_contact]]></Event>
                 * <ChangeType>update_party</ChangeType>
                 * <Id>2</Id>
                 * <Name><![CDATA[张三]]></Name>
                 * <ParentId><![CDATA[1]]></ParentId>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_contact
                 * ChangeType 此时固定为update_party
                 * Id 部门Id
                 * Name 部门名称，仅当该字段发生变更时传递
                 * ParentId 父部门id，仅当该字段发生变更时传递
                 * 说明： 由通讯录同步助手通过api发起的更新部门触发的事件不回调给通讯录同步助手应用。
                 */
            } elseif ($ChangeType == 'delete_party') { // 部门变更通知-删除部门事件
                /**
                 * 删除部门事件
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_contact]]></Event>
                 * <ChangeType>delete_party</ChangeType>
                 * <Id>2</Id>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_contact
                 * ChangeType 此时固定为delete_party
                 * Id 部门Id
                 * 说明： 由通讯录同步助手通过api发起的删除部门触发的事件不回调给通讯录同步助手应用。
                 */
            } elseif ($ChangeType == 'update_tag') { // 标签变更通知-标签成员变更事件
                /**
                 * 标签变更通知
                 * 标签成员变更事件
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_contact]]></Event>
                 * <ChangeType><![CDATA[update_tag]]></ChangeType>
                 * <TagId>1</TagId>
                 * <AddUserItems><![CDATA[zhangsan,lisi]]></AddUserItems>
                 * <DelUserItems><![CDATA[zhangsan1,lisi1]]></DelUserItems>
                 * <AddPartyItems><![CDATA[1,2]]></AddPartyItems>
                 * <DelPartyItems><![CDATA[3,4]]></DelPartyItems>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_contact
                 * ChangeType 固定为update_tag
                 * TagId 标签Id
                 * AddUserItems 标签中新增的成员userid列表，用逗号分隔
                 * DelUserItems 标签中删除的成员userid列表，用逗号分隔
                 * AddPartyItems 标签中新增的部门id列表，用逗号分隔
                 * DelPartyItems 标签中删除的部门id列表，用逗号分隔
                 * 说明： 由通讯录同步助手通过api发起的标签变更触发的事件不回调给通讯录同步助手应用。
                 * 备注：标签成员变更事件与成员变更事件的时序不保证先后，如果需要严格依赖标签成员变更事件维护标签成员，可在收到标签成员变更事件时调用获取标签成员接口，以此获取标签成员。
                 */
                $response = "success";
            }
        } elseif ($Event == 'batch_job_result') { // 异步任务完成通知

            /**
             * 异步任务完成通知
             * 本事件是成员在使用异步任务接口时，用于接收任务执行完毕的结果通知。
             *
             * 请求示例：
             *
             * <xml><ToUserName><![CDATA[wx28dbb14e3720FAKE]]></ToUserName>
             * <FromUserName><![CDATA[FromUser]]></FromUserName>
             * <CreateTime>1425284517</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[batch_job_result]]></Event>
             * <BatchJob><JobId><![CDATA[S0MrnndvRG5fadSlLwiBqiDDbM143UqTmKP3152FZk4]]></JobId>
             * <JobType><![CDATA[sync_user]]></JobType>
             * <ErrCode>0</ErrCode>
             * <ErrMsg><![CDATA[ok]]></ErrMsg>
             * </BatchJob>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 成员UserID
             * CreateTime 消息创建时间（整型）
             * MsgType 消息类型，此时固定为：event
             * Event 事件类型：batch_job_result
             * JobId 异步任务id，最大长度为64字符
             * JobType 操作类型，字符串，目前分别有：sync_user(增量更新成员)、 replace_user(全量覆盖成员）、invite_user(邀请成员关注）、replace_party(全量覆盖部门)
             * ErrCode 返回码
             * ErrMsg 对返回码的文本描述内容
             */
            $response = "success";
        } elseif ($Event == 'change_external_contact') { // 接收客户变更事件
            /**
             * 事件格式
             * 添加企业客户事件
             * 编辑企业客户事件
             * 外部联系人免验证添加成员事件
             * 删除企业客户事件
             * 删除跟进成员事件
             * 客户群变更事件
             */
            if ($ChangeType == 'add_external_contact') { // 接收客户变更事件-添加企业客户事件
                /**
                 * 添加企业客户事件
                 * 配置了客户联系功能的成员添加外部联系人时，回调该事件
                 *
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_external_contact]]></Event>
                 * <ChangeType><![CDATA[add_external_contact]]></ChangeType>
                 * <UserID><![CDATA[zhangsan]]></UserID>
                 * <ExternalUserID><![CDATA[woAJ2GCAAAXtWyujaWJHDDGi0mAAAA]]></ExternalUserID>
                 * <State><![CDATA[teststate]]></State>
                 * <WelcomeCode><![CDATA[WELCOMECODE]]></WelcomeCode>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_external_contact
                 * ChangeType 此时固定为add_external_contact
                 * UserID 企业服务人员的UserID
                 * ExternalUserID 外部联系人的userid，注意不是企业成员的帐号
                 * State 添加此用户的「联系我」方式配置的state参数，可用于识别添加此用户的渠道
                 * WelcomeCode 欢迎语code，可用于发送欢迎语
                 * 企业可以根据ExternalUserID调用“获取客户详情”读取详情。
                 * 企业可以通过配置客户联系「联系我」方式接口来指定State参数，当有客户通过这个联系方式添加企业成员时会回调此参数。
                 * 注意:如果外部联系人和成员已经开始聊天或已通过「外部联系人免验证添加成员事件」得到的welcomecode发送欢迎语，则不会继续返回welcomecode。
                 */
            } elseif ($ChangeType == 'edit_external_contact') { // 接收客户变更事件-编辑企业客户事件
                /**
                 * 编辑企业客户事件
                 * 配置了客户联系功能的成员修改外部联系人的备注、手机号或标签时时，回调该事件
                 *
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_external_contact]]></Event>
                 * <ChangeType><![CDATA[edit_external_contact]]></ChangeType>
                 * <UserID><![CDATA[zhangsan]]></UserID>
                 * <ExternalUserID><![CDATA[woAJ2GCAAAXtWyujaWJHDDGi0mAAAA]]></ExternalUserID>
                 * <State><![CDATA[teststate]]></State>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_external_contact
                 * ChangeType 此时固定为edit_external_contact
                 * UserID 企业服务人员的UserID
                 * ExternalUserID 外部联系人的userid，注意不是企业成员的帐号
                 * State 添加此用户的「联系我」方式配置的state参数，可用于识别添加此用户的渠道
                 */
            } elseif ($ChangeType == 'add_half_external_contact') { // 接收客户变更事件-外部联系人免验证添加成员事件
                /**
                 * 外部联系人免验证添加成员事件
                 * 外部联系人添加了配置了客户联系功能且开启了免验证的成员时（此时成员尚未确认添加对方为好友），回调该事件
                 *
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_external_contact]]></Event>
                 * <ChangeType><![CDATA[add_half_external_contact]]></ChangeType>
                 * <UserID><![CDATA[zhangsan]]></UserID>
                 * <ExternalUserID><![CDATA[woAJ2GCAAAXtWyujaWJHDDGi0mACAAAA]]></ExternalUserID>
                 * <State><![CDATA[teststate]]></State>
                 * <WelcomeCode><![CDATA[WELCOMECODE]]></WelcomeCode>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_external_contact
                 * ChangeType 此时固定为add_half_external_contact
                 * UserID 企业服务人员的UserID
                 * ExternalUserID 外部联系人的userid，注意不是企业成员的帐号
                 * State 添加此用户的「联系我」方式配置的state参数，可用于识别添加此用户的渠道
                 * WelcomeCode 欢迎语code，可用于发送欢迎语
                 * 企业可以根据ExternalUserID调用“获取客户详情”读取详情。
                 * 企业可以通过配置客户联系「联系我」方式接口来指定State参数，当有客户通过这个联系方式添加企业成员时会回调此参数。
                 * 注意:如果外部联系人和成员已经开始聊天，则不会返回welcomecode。
                 */
            } elseif ($ChangeType == 'del_external_contact') { // 接收客户变更事件-删除企业客户事件
                /**
                 * 删除企业客户事件
                 * 配置了客户联系功能的成员删除外部联系人时，回调该事件
                 *
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_external_contact]]></Event>
                 * <ChangeType><![CDATA[del_external_contact]]></ChangeType>
                 * <UserID><![CDATA[zhangsan]]></UserID>
                 * <ExternalUserID><![CDATA[woAJ2GCAAAXtWyujaWJHDDGi0mACAAAA]]></ExternalUserID>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_external_contact
                 * ChangeType 此时固定为del_external_contact
                 * UserID 企业服务人员的UserID
                 * ExternalUserID 外部联系人的userid，注意不是企业成员的帐号
                 */
            } elseif ($ChangeType == 'del_follow_user') { // 接收客户变更事件-删除跟进成员事件
                /**
                 * 删除跟进成员事件
                 * 配置了客户联系功能的成员被外部联系人删除时，回调该事件
                 *
                 * 请求示例：
                 *
                 * <xml>
                 * <ToUserName><![CDATA[toUser]]></ToUserName>
                 * <FromUserName><![CDATA[sys]]></FromUserName>
                 * <CreateTime>1403610513</CreateTime>
                 * <MsgType><![CDATA[event]]></MsgType>
                 * <Event><![CDATA[change_external_contact]]></Event>
                 * <ChangeType><![CDATA[del_follow_user]]></ChangeType>
                 * <UserID><![CDATA[zhangsan]]></UserID>
                 * <ExternalUserID><![CDATA[woAJ2GCAAAXtWyujaWJHDDGi0mACHAAA]]></ExternalUserID>
                 * </xml>
                 * 参数说明：
                 *
                 * 参数 说明
                 * ToUserName 企业微信CorpID
                 * FromUserName 此事件该值固定为sys，表示该消息由系统生成
                 * CreateTime 消息创建时间 （整型）
                 * MsgType 消息的类型，此时固定为event
                 * Event 事件的类型，此时固定为change_external_contact
                 * ChangeType 此时固定为del_follow_user
                 * UserID 企业服务人员的UserID
                 * ExternalUserID 外部联系人的userid，注意不是企业成员的帐号
                 */
            }
            $response = "success";
        } elseif ($Event == 'change_external_chat') { // 客户群变更事件
            /**
             * 客户群变更事件
             * 客户群被修改后（群名变更，群成员增加或移除），回调该事件。收到该事件后，企业需要再调用获取客户群详情接口，以获取最新的群详情。
             *
             * 请求示例：
             *
             * <xml>
             * <ToUserName><![CDATA[toUser]]></ToUserName>
             * <FromUserName><![CDATA[sys]]></FromUserName>
             * <CreateTime>1403610513</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[change_external_chat]]></Event>
             * <ChatId><![CDATA[CHAT_ID]]></ChatId>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 企业微信CorpID
             * FromUserName 此事件该值固定为sys，表示该消息由系统生成
             * CreateTime 消息创建时间 （unix时间戳）
             * MsgType 消息的类型，此时固定为event
             * Event 事件的类型，此时固定为 change_external_chat
             * ChatId 群ID
             */
            $response = "success";
        } elseif ($Event == 'open_approval_change') { // 审批状态通知事件

            /**
             * 审批状态通知事件
             * 本事件触发时机为：
             * 1.自建/第三方应用调用审批流程引擎发起申请之后，审批状态发生变化时
             * 2.自建/第三方应用调用审批流程引擎发起申请之后，在“审批中”状态，有任意审批人进行审批操作时
             *
             * 请求示例：
             *
             * <xml>
             * <ToUserName>wwddddccc7775555aaa</ToUserName>
             * <FromUserName>sys</FromUserName>
             * <CreateTime>1527838022</CreateTime>
             * <MsgType>event</MsgType>
             * <Event>open_approval_change</Event>
             * <AgentID>1</AgentID>
             * <ApprovalInfo>
             * <ThirdNo>201806010001</ThirdNo>
             * <OpenSpName>付款</OpenSpName>
             * <OpenTemplateId>1234567890</OpenTemplateId>
             * <OpenSpStatus>1</OpenSpStatus>
             * <ApplyTime>1527837645</ApplyTime>
             * <ApplyUserName>xiaoming</ApplyUserName>
             * <ApplyUserId>1</ApplyUserId>
             * <ApplyUserParty>产品部</ApplyUserParty>
             * <ApplyUserImage>http://www.qq.com/xxx.png</ApplyUserImage>
             * <ApprovalNodes>
             * <ApprovalNode>
             * <NodeStatus>1</NodeStatus>
             * <NodeAttr>1</NodeAttr>
             * <NodeType>1</NodeType>
             * <Items>
             * <Item>
             * <ItemName>xiaohong</ItemName>
             * <ItemUserId>2</ItemUserId>
             * <ItemImage>http://www.qq.com/xxx.png</ItemImage>
             * <ItemStatus>1</ItemStatus>
             * <ItemSpeech></ItemSpeech>
             * <ItemOpTime>0</ItemOpTime>
             * </Item>
             * </Items>
             * </ApprovalNode>
             * </ApprovalNodes>
             * <NotifyNodes>
             * <NotifyNode>
             * <ItemName>xiaogang</ItemName>
             * <ItemUserId>3</ItemUserId>
             * <ItemImage>http://www.qq.com/xxx.png</ItemImage>
             * </NotifyNode>
             * </NotifyNodes>
             * <approverstep>0</approverstep>
             * </ApprovalInfo>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * ToUserName 接收方企业Corpid
             * FromUserName 发送方：企业微信
             * CreateTime 消息发送时间
             * MsgType 消息类型
             * Event 事件名称：open_approval_change
             * AgentID 企业应用的id，整型。可在应用的设置页面查看
             * ApprovalInfo 审批信息
             * ThirdNo 审批单编号，由开发者在发起申请时自定义
             * OpenSpName 审批模板名称
             * OpenTemplateId 审批模板id
             * OpenSpStatus 申请单当前审批状态：1-审批中；2-已通过；3-已驳回；4-已取消
             * ApplyTime 提交申请时间
             * ApplyUserName 提交者姓名
             * ApplyUserId 提交者userid
             * ApplyUserParty 提交者所在部门
             * ApplyUserImage 提交者头像
             * ApprovalNodes 审批流程信息
             * ApprovalNode 审批流程信息，可以有多个审批节点
             * NodeStatus 节点审批操作状态：1-审批中；2-已同意；3-已驳回；4-已转审
             * NodeAttr 审批节点属性：1-或签；2-会签
             * NodeType 审批节点类型：1-固定成员；2-标签；3-上级
             * Items 审批节点信息，当节点为标签或上级时，一个节点可能有多个分支
             * Item 审批节点分支，当节点为标签或上级时，一个节点可能有多个分支
             * ItemName 分支审批人姓名
             * ItemUserId 分支审批人userid
             * ItemImage 分支审批人头像
             * ItemStatus 分支审批审批操作状态：1-审批中；2-已同意；3-已驳回；4-已转审
             * ItemSpeech 分支审批人审批意见
             * ItemOpTime 分支审批人操作时间
             * NotifyNodes 抄送信息，可能有多个抄送人
             * NotifyNode 抄送人信息
             * ItemName 抄送人姓名
             * ItemUserId 抄送人userid
             * ItemImage 抄送人头像
             * approverstep 当前审批节点：0-第一个审批节点；1-第二个审批节点…以此类推
             */
            $ApprovalInfo = isset($datas['ApprovalInfo']) ? trim($datas['ApprovalInfo']) : array();
            $response = "success";
        } elseif ($Event == 'sys_approval_change') { // 审批申请状态变化回调通知
            /**
             * 2.事件格式
             * 当指定类型的审批申请发生状态变化时，企业微信将向回调地址发送相应的通知事件。
             * 状态变化包括但不限于：催办、撤销、同意、驳回、转审、添加备注等情况。
             *
             * 示例：
             *
             * <xml>
             * <ToUserName><![CDATA[ww1cSD21f1e9c0caaa]]></ToUserName>
             * <FromUserName><![CDATA[sys]]></FromUserName>
             * <CreateTime>1571732272</CreateTime>
             * <MsgType><![CDATA[event]]></MsgType>
             * <Event><![CDATA[sys_approval_change]]></Event>
             * <AgentID>3010040</AgentID>
             * <ApprovalInfo>
             * <SpNo>201910220003</SpNo>
             * <SpName><![CDATA[示例模板]]></SpName>
             * <SpStatus>1</SpStatus>
             * <TemplateId><![CDATA[3TkaH5KFbrG9heEQWLJjhgpFwmqAFB4dLEnapaB7aaa]]></TemplateId>
             * <ApplyTime>1571728713</ApplyTime>
             * <Applyer>
             * <UserId><![CDATA[WuJunJie]]></UserId>
             * <Party><![CDATA[1]]></Party>
             * </Applyer>
             * <SpRecord>
             * <SpStatus>1</SpStatus>
             * <ApproverAttr>2</ApproverAttr>
             * <Details>
             * <Approver>
             * <UserId><![CDATA[WangXiaoMing]]></UserId>
             * </Approver>
             * <Speech><![CDATA[]]></Speech>
             * <SpStatus>1</SpStatus>
             * <SpTime>0</SpTime>
             * </Details>
             * <Details>
             * <Approver>
             * <UserId><![CDATA[XiaoGangHuang]]></UserId>
             * </Approver>
             * <Speech><![CDATA[]]></Speech>
             * <SpStatus>1</SpStatus>
             * <SpTime>0</SpTime>
             * </Details>
             * </SpRecord>
             * <SpRecord>
             * <SpStatus>1</SpStatus>
             * <ApproverAttr>1</ApproverAttr>
             * <Details>
             * <Approver>
             * <UserId><![CDATA[XiaoHongLiu]]></UserId>
             * </Approver>
             * <Speech><![CDATA[]]></Speech>
             * <SpStatus>1</SpStatus>
             * <SpTime>0</SpTime>
             * </Details>
             * </SpRecord>
             * <Notifyer>
             * <UserId><![CDATA[ChengLiang]]></UserId>
             * </Notifyer>
             * <Comments>
             * <CommentUserInfo>
             * <UserId><![CDATA[LiuZhi]]></UserId>
             * </CommentUserInfo>
             * <CommentTime>1571732272</CommentTime>
             * <CommentContent><![CDATA[这是一个备注]]></CommentContent>
             * <CommentId><![CDATA[6750538708562308220]]></CommentId>
             * </Comments>
             * <StatuChangeEvent>10</StatuChangeEvent>
             * </ApprovalInfo>
             * </xml>
             * 参数说明：
             *
             * 参数 说明
             * SpNo 审批编号
             * SpName 审批申请类型名称（审批模板名称）
             * SpStatus 申请单状态：1-审批中；2-已通过；3-已驳回；4-已撤销；6-通过后撤销；7-已删除；10-已支付
             * TemplateId 审批模板id。可在“获取审批申请详情”、“审批状态变化回调通知”中获得，也可在审批模板的模板编辑页面链接中获得。
             * ApplyTime 审批申请提交时间,Unix时间戳
             * Applyer 申请人信息
             * └ UserId 申请人userid
             * └ Party 申请人所在部门pid
             * SpRecord 审批流程信息，可能有多个审批节点。
             * └ SpStatus 审批节点状态：1-审批中；2-已同意；3-已驳回；4-已转审
             * └ ApproverAttr 节点审批方式：1-或签；2-会签
             * └ Details 审批节点详情。当节点为标签或上级时，一个节点可能有多个分支
             * └ └ Approvor 分支审批人
             * └ └ └ UserId 分支审批人userid
             * └ └ Speech 审批意见字段
             * └ └ SpStatus 分支审批人审批状态：1-审批中；2-已同意；3-已驳回；4-已转审
             * └ └ SpTime 节点分支审批人审批操作时间，0为尚未操作
             * └ └ MediaId 节点分支审批人审批意见附件，media_id具体使用请参考：文档-获取临时素材
             * Notifyer 抄送信息，可能有多个抄送节点
             * └ UserId 节点抄送人userid
             * Comments 审批申请备注信息，可能有多个备注节点
             * └ CommentUserInfo 备注人信息
             * └ └ UserId 备注人userid
             * └ CommentTime 备注提交时间
             * └ CommentContent 备注文本内容
             * └ CommentId 备注id
             * └ └ MediaId 节点分支审批人审批意见附件，media_id具体使用请参考：文档-获取临时素材
             * StatuChangeEvent 审批申请状态变化类型：1-提单；2-同意；3-驳回；4-转审；5-催办；6-撤销；8-通过后撤销；10-添加备注
             */
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
     * @param string $provider_appid            
     * @param string $agentid            
     * @return boolean
     */
    protected function answer($FromUserName, $ToUserName, $content, $authorizer_appid, $provider_appid, $agentid)
    {
        $agentid = intval($agentid);
        $match = $this->modelQyweixinKeyword->matchKeyWord($content, $authorizer_appid, $provider_appid, $agentid, false);
        if (empty($match)) {
            $this->modelQyweixinWord->record($content, $authorizer_appid, $provider_appid, $agentid);
            $match = $this->modelQyweixinKeyword->matchKeyWord('默认回复', $authorizer_appid, $provider_appid, $agentid, false);
        }

        $match['reply_msg_ids'] = $this->modelQyweixinKeywordToReplyMsg->getReplyMsgIdsByKeywordId($match['id']);
        $match['agent_msg_ids'] = $this->modelQyweixinKeywordToAgentMsg->getAgentMsgIdsByKeywordId($match['id']);
        // $e = new \Exception("Post请求" . \json_encode($match));
        // $this->modelErrorLog->log($this->activity_id, $e, $this->now);
        // return '';
        if (!empty($match['agent_msg_ids'])) {
            $this->qyweixinService->answerAgentMsgs($FromUserName, $ToUserName, $match);
        }
        // $this->qyweixinService->answerTemplateMsgs($FromUserName, $ToUserName, $match);
        return $this->qyweixinService->answerReplyMsgs($FromUserName, $ToUserName, $match);
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

            if ($this->requestLogDatas['log_type'] == 'qymsglog') { // 消息与事件接收URL
                $this->modelQyweixinMsgLog->record($this->requestLogDatas);
            }
        }
    }
}
