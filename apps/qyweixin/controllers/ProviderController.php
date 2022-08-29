<?php

namespace App\Qyweixin\Controllers;

/**
 * 企业授权应用
 * 企业微信的系统管理员可以授权安装第三方应用，安装后企业微信后台会将授权凭证、授权信息等推送给服务商后台。
 * 授权可以有两种发起方式：
 *
 * 从服务商网站发起
 * 从企业微信应用市场发起
 * 以上两种授权发起方式并不冲突，服务商可以同时支持。
 * https://work.weixin.qq.com/api/doc/90001/90143/90597
 */
class ProviderController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 1;
    
    /**
     *
     * @var \App\Qyweixin\Models\Auth\Corp
     */
    private $modelQyweixinAuthCorp;

    /**
     *
     * @var \App\Qyweixin\Models\Agent\Agent
     */
    private $modelQyweixinAgent;

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
     * @var \App\Qyweixin\Models\Provider\ProviderLoginBindTracking
     */
    private $modelQyweixinProviderLoginBindTracking;

    /**
     *
     * @var \App\Qyweixin\Models\Authorize\AuthorizeLog
     */
    private $modelQyweixinAuthorizeLog;

    // lock key
    private $lock_key_prefix = 'qyweixin_component_';

    private $trackingKey = "公众号授权给第三方服务商流程";

    /** @var  \Qyweixin\Service */
    private $objQyProvider;

    private $provider_appid;

    private $providerConfig;

    private $authorizer_appid;

    private $authorizerConfig;

    private $agentid = 0;

    /**
     * @var \App\Qyweixin\Services\QyService
     */
    private $qyService;

    // 请求日志信息
    private $requestLogDatas = array();

    // 是否加解密
    private $isNeedDecryptAndEncrypt = TRUE;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->isNeedDecryptAndEncrypt = true;

        $this->modelQyweixinAuthCorp = new \App\Qyweixin\Models\Auth\Corp();
        $this->modelQyweixinAgent = new \App\Qyweixin\Models\Agent\Agent();
        $this->modelQyweixinProvider = new \App\Qyweixin\Models\Provider\Provider();
        $this->modelQyweixinAuthorizer = new \App\Qyweixin\Models\Authorize\Authorizer();
        $this->modelQyweixinProviderLoginBindTracking = new \App\Qyweixin\Models\Provider\ProviderLoginBindTracking();
        $this->modelQyweixinAuthorizeLog = new \App\Qyweixin\Models\Authorize\AuthorizeLog();
    }

    /**
     * 从服务商网站发起
     * 系统管理员在第三方服务商网站找到适用的应用后，可在服务商网站发起授权请求。
     * 此方式下第三方服务商需构造授权链接，引导用户进入授权页面完成授权过程，并取得临时授权码。
     * 流程如图示。
     *
     *
     * 注：
     *
     * 获取预授权码
     * 预授权码是应用实现授权托管的安全凭证，见获取预授权码。
     * 引导用户进入授权页
     * 第三方服务商在自己的网站中放置“企业微信应用授权”的入口，引导企业微信管理员进入应用授权页。授权页网址为:
     * https://open.work.weixin.qq.com/3rdapp/install?suite_id=SUITE_ID&pre_auth_code=PRE_AUTH_CODE&redirect_uri=REDIRECT_URI&state=STATE
     * 跳转链接中，第三方服务商需提供suite_id、预授权码、授权完成回调URI和state参数。
     * 其中redirect_uri是授权完成后的回调网址，redirect_uri需要经过一次urlencode作为参数；state可填a-zA-Z0-9的参数值（不超过128个字节），用于第三方自行校验session，防止跨域攻击。
     */
    public function install3rdappAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/provider/install3rdapp?provider_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=xx&suite_id=xx
        // http://www.myapplicationmodule.com/qyweixin/api/provider/install3rdapp?provider_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=xx&suite_id=xx
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            // 初始化
            $this->doInitializeLogic();

            $state = isset($_GET['state']) ? (trim($_GET['state'])) : \uniqid();
            $suite_id = isset($_GET['suite_id']) ? (trim($_GET['suite_id'])) : '';
            $redirect = isset($_GET['redirect']) ? (trim($_GET['redirect'])) : '';
            if (empty($redirect)) {
                return abort(500, "回调地址未定义");
            }
            if (empty($this->authorizerConfig)) {
                return abort(500, "suite_id未定义");
            }

            // 2 获取预授权码（pre_auth_code）
            $suite_access_token = $this->authorizerConfig['suite_access_token'];
            $preAuthCodeInfo = $this->objQyProvider->getPreAuthCode($suite_access_token);
            $pre_auth_code = $preAuthCodeInfo['pre_auth_code'];

            // 设置授权配置
            // 该接口可对某次授权进行配置。可支持测试模式（应用未发布时）。
            $session_info = array();
            // $session_info['appid'] = array();
            $session_info['auth_type'] = 1; // 1 测试授权
            $this->objQyProvider->setSessionInfo($suite_access_token, $pre_auth_code, $session_info);

            // 3 引入用户进入授权页
            // 存储跳转地址
            $_SESSION['redirect'] = $redirect;
            $_SESSION['state'] = $state;
            $_SESSION['provider_appid'] = $this->provider_appid;
            $_SESSION['suite_id'] = $suite_id;

            $moduleName = 'qyweixin';
            $controllerName = $this->controllerName;
            $scheme = $this->getRequest()->getScheme();
            $redirectUri = $scheme . '://';
            $redirectUri .= $_SERVER["HTTP_HOST"];
            $redirectUri .= '/' . $moduleName;
            $redirectUri .= '/' . $controllerName;
            $redirectUri .= '/install3rdappcallback';
            // 授权处理
            $redirectUri = $this->objQyProvider->getThirdappInstallUrl($suite_id, $pre_auth_code, $redirectUri, $state, false);
            header("location:{$redirectUri}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 授权成功，返回临时授权码
     * 用户确认授权后，会进入回调URI(即redirect_uri)，并在URI参数中带上临时授权码、过期时间以及state参数。第三方服务商据此获得临时授权码。回调地址为：
     * redirect_uri?auth_code=xxx&expires_in=600&state=xx
     * 临时授权码10分钟后会失效，第三方服务商需尽快使用临时授权码换取永久授权码及授权信息。
     * 每个企业授权的每个应用的永久授权码、授权信息都是唯一的，第三方服务商需妥善保管。后续可以通过永久授权码获取企业access_token，进而调用企业微信相关API为授权企业提供服务。
     */
    public function install3rdappcallbackAction()
    {
        // http://www.myapplicationmodule.com/qyweixin/api/provider/install3rdappcallback?auth_code=queryauthcode@@@YGWj2XtzxxwQQfpIW6VGOJvDALlLfFwSy9anTBQs0sEMXsXZMrTRVPbhXpmqh265vpKz_YIgj2bJ-WJR2oGABw&expires_in=3600&state=xx
        try {
            $provider_appid = empty($_SESSION['provider_appid']) ? "" : $_SESSION['provider_appid'];
            if (empty($provider_appid)) {
                throw new \Exception("provider_appid未定义");
            }
            $_GET['provider_appid'] = $provider_appid;

            $suite_id = empty($_SESSION['suite_id']) ? "" : $_SESSION['suite_id'];
            if (empty($suite_id)) {
                throw new \Exception("suite_id未定义");
            }
            $_GET['suite_id'] = $suite_id;

            // 初始化
            $this->doInitializeLogic();

            $state = isset($_GET['state']) ? (trim($_GET['state'])) : '';
            $expires_in = isset($_GET['expires_in']) ? trim($_GET['expires_in']) : '';
            $auth_code = isset($_GET['auth_code']) ? trim($_GET['auth_code']) : '';
            if (empty($auth_code)) {
                return abort(500, "auth_code不能为空");
            }
            $oldstate = empty($_SESSION['state']) ? "" : $_SESSION['state'];
            if ($state != $oldstate) {
                return abort(500, "state发生了改变");
            }
            $redirect = empty($_SESSION['redirect']) ? "" : $_SESSION['redirect'];
            if (empty($redirect)) {
                return abort(500, "回调地址未定义");
            }

            // 使用授权码换取公众号的接口调用凭据和授权信息
            $suite_access_token = $this->authorizerConfig['suite_access_token'];
            $permanentCodeInfo = $this->objQyProvider->getPermanentCode($suite_access_token, $auth_code);

            // 更新accesstoken
            $memo = [
                'suite_access_token' => $suite_access_token,
                'auth_code' => $auth_code,
                'auth_expires_in' => $expires_in,
                'getPermanentCode' => $permanentCodeInfo
            ];
            $this->modelQyweixinAuthorizer->createAndUpdateAuthorizer($this->provider_appid, $this->authorizer_appid, $permanentCodeInfo['access_token'], $permanentCodeInfo['access_token'], $permanentCodeInfo['expires_in'], $permanentCodeInfo['permanent_code'], $permanentCodeInfo, $memo);

            $this->modelQyweixinProviderLoginBindTracking->record($this->provider_appid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $this->authorizer_appid);
            // 生成对应agent记录？？？
            header("location:{$redirect}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }
    
    /**
     * 指令回调URL
     * 概述
     * 在发生授权、通讯录变更、ticket变化等事件时，企业微信服务器会向应用的“指令回调URL”推送相应的事件消息。消息结构体将使用创建应用时的EncodingAESKey进行加密（特别注意, 在第三方回调事件中使用加解密算法，receiveid的内容为suiteid），请参考接收消息解析数据包。
     *
     * 本章节的回调事件，服务商在收到推送后都必须直接返回字符串 “success”，若返回值不是 “success”，企业微信会把返回内容当作错误信息。
     *
     * 各个事件皆假设指令回调URL设置为：https://127.0.0.1/suite/receive
     *
     * 收到的数据包中ToUserName为产生事件的SuiteId，AgentID为空
     *
     * 各个事件的xml包仅是接收的数据包中的Encrypt参数解密后的内容说明
     * https://work.weixin.qq.com/api/doc/90001/90143/90613
     */
    public function authorizecallbackAction()
    {
        // 指令回调URL http://www.myapplicationmodule.com/qyweixin/api/provider/authorizecallback?provider_appid=wxca8519f703c07d32&suite_id=ww4be715bb538715d3&agentid=
        // 通用开发参数 系统事件接收URL https://doublec.intonecc.com/qyweixin/api/provider/authorizecallback?provider_appid=ww975831ac45517bff&log_type=syslog
        try {
            /**
             * ==================================================================================
             * ====================================以下逻辑请勿修改===============================
             * ==================================================================================
             */
            // 授权事件接收URL
            $this->requestLogDatas['log_type'] = 'authorizelog';

            // 初始化
            $this->doInitializeLogic();

            $onlyRevieve = false;
            $AESInfo = array();
            $AESInfo['timestamp'] = isset($_GET['timestamp']) ? trim(strtolower($_GET['timestamp'])) : '';
            $AESInfo['nonce'] = isset($_GET['nonce']) ? $_GET['nonce'] : '';
            $AESInfo['encrypt_type'] = isset($_GET['encrypt_type']) ? $_GET['encrypt_type'] : '';
            $AESInfo['msg_signature'] = isset($_GET['msg_signature']) ? $_GET['msg_signature'] : '';
            $AESInfo['api'] = 'authorizecallback';

            $encodingAESKey = isset($this->providerConfig['EncodingAESKey']) ? $this->providerConfig['EncodingAESKey'] : '';
            $verifyToken = isset($this->providerConfig['verify_token']) ? $this->providerConfig['verify_token'] : '';
            $receiveId = $this->provider_appid;
            if (!empty($this->authorizerConfig)) {
                $encodingAESKey = isset($this->authorizerConfig['EncodingAESKey']) ? $this->authorizerConfig['EncodingAESKey'] : '';
                $verifyToken = isset($this->authorizerConfig['verify_token']) ? $this->authorizerConfig['verify_token'] : '';
                $receiveId = $this->authorizer_appid;
            }
            
            $AESInfo['EncodingAESKey'] = $encodingAESKey;
            $AESInfo['verify_token'] = $verifyToken;
            $AESInfo['receiveId'] = $receiveId;
            $this->requestLogDatas['aes_info'] = $AESInfo;
            if (empty($verifyToken)) {
                throw new \Exception('application verify_token is null. config:' . \App\Common\Utils\Helper::myJsonEncode($this->providerConfig));
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
                $objQyWeixin = new \Qyweixin\Client($this->providerConfig['appid'], $this->providerConfig['appsecret']);
                if (!empty($this->providerConfig['access_token'])) {
                    $objQyWeixin->setAccessToken($this->providerConfig['access_token']);
                }
                $ret4CheckSignature = $objQyWeixin->checkSignature($verifyToken, $encodingAESKey);
                if (empty($ret4CheckSignature)) {
                    $debug = \App\Common\Utils\Helper::myJsonEncode($this->requestLogDatas);
                    throw new \Exception('签名错误' . $debug);
                } else {
                    $AESInfo['replyEchoStr'] = $ret4CheckSignature['replyEchoStr'];
                    $this->requestLogDatas['aes_info'] = $AESInfo;
                    return $ret4CheckSignature['replyEchoStr'];
                }
            } elseif ($this->request->isPost()) {
                // 支持Http Post请求接收业务数据
                // 签名正确，将接受到的xml转化为数组数据并记录数据
                $datas = $this->getDataFromWeixinServer();
                foreach ($datas as $dtkey => $dtvalue) {
                    $this->requestLogDatas[$dtkey] = $dtvalue;
                }
                $this->requestLogDatas['response'] = 'success';

                $SuiteId = isset($datas['SuiteId']) ? trim($datas['SuiteId']) : '';
                $InfoType = isset($datas['InfoType']) ? trim($datas['InfoType']) : '';
                $CreateTime = isset($datas['CreateTime']) ? ($datas['CreateTime']) : time();

                // 关于重试的消息排重
                $uniqueKey = $SuiteId . "-" . $CreateTime . "-" . $InfoType;
                $this->requestLogDatas['lock_uniqueKey'] = $uniqueKey;
                if (!empty($uniqueKey)) {
                    $objLock = new \iLock(md5($uniqueKey));
                    if ($objLock->lock()) {
                        return "success";
                    }
                }

                // 授权方ID
                $this->authorizer_appid = $SuiteId;
                if (!empty($this->authorizer_appid)) {
                    $this->qyService = new \App\Qyweixin\Services\QyService($this->authorizer_appid, $this->provider_appid, 0);
                    $this->authorizerConfig = $this->qyService->getAppConfig4Authorizer();
                    $this->objQyProvider = $this->qyService->getQyweixinProvider();
                }

                /**
                 * ==================================================================================
                 * ====================================以上逻辑请勿修改===================================
                 * ==================================================================================
                 */

                if ($InfoType == 'suite_ticket') { // 推送suite_ticket
                    /**
                     * 推送suite_ticket
                     * 企业微信服务器会定时（每十分钟）推送ticket。ticket会实时变更，并用于后续接口的调用。
                     *
                     * 若开发者想立即获得ticket推送值，可登录服务商平台，在第三方应用详情-回调配置，手动刷新ticket推送。
                     *
                     * 请求方式：POST（HTTPS）
                     * 请求地址：https://127.0.0.1/suite/receive?msg_signature=3a7b08bb8e6dbce3c9671d6fdb69d15066227608&timestamp=1403610513&nonce=380320359
                     *
                     * 请求包体：
                     *
                     * <xml>
                     * <SuiteId><![CDATA[ww4asffe99e54c0fxxxx]]></SuiteId>
                     * <InfoType> <![CDATA[suite_ticket]]></InfoType>
                     * <TimeStamp>1403610513</TimeStamp>
                     * <SuiteTicket><![CDATA[asdfasfdasdfasdf]]></SuiteTicket>
                     * </xml>
                     * 参数说明：
                     *
                     * 参数 说明
                     * SuiteId 第三方应用的SuiteId
                     * InfoType suite_ticket
                     * TimeStamp 时间戳
                     * SuiteTicket Ticket内容，最长为512字节
                     */
                    $SuiteTicket = isset($datas['SuiteTicket']) ? trim($datas['SuiteTicket']) : ''; // Ticket内容

                    //https://developer.work.weixin.qq.com/document/path/90600#14939
                    // https://developer.work.weixin.qq.com/document/path/95434
                    // a. 应用代开发模版id即为suite_id。企业微信后台也会定期向应用代开发模版回调url推送suite_ticket
                    // b. 可通过获取第三方应用凭证接口获取suite_access_token。
                    // c. suite_access_token可用于获取企业永久授权码
                    // d. 可调用获取企业授权信息（注意：此种情况接口会多返回is_customized_app字段，且值为true，表示是代开发模版授权）

                    // 获取第三方应用suite_access_token
                    if (!empty($this->authorizerConfig)) {
                        $suite_secret = $this->authorizerConfig['appsecret'];
                        $suiteToken = $this->objQyProvider->getSuiteToken($SuiteId, $suite_secret, $SuiteTicket);

                        // 更新suite_access_token
                        $this->modelQyweixinAuthorizer->updateSuiteAccessToken($this->authorizerConfig['id'], $suiteToken['suite_access_token'], $suiteToken['expires_in'], $SuiteTicket);
                    }
                } elseif ($InfoType == 'create_auth') { // 授权成功通知
                    /**
                     * 授权通知事件
                     * 授权成功通知
                     * 从企业微信应用市场发起授权时，企业微信后台会推送授权成功通知。
                     *
                     * 从第三方服务商网站发起的应用授权流程，由于授权完成时会跳转第三方服务商管理后台，因此不会通过此接口向第三方服务商推送授权成功通知。
                     *
                     * 请求方式：POST（HTTPS）
                     * 请求地址：https://127.0.0.1/suite/receive?msg_signature=3a7b08bb8e6dbce3c9671d6fdb69d15066227608&timestamp=1403610513&nonce=380320359
                     *
                     * 请求包体：
                     *
                     * <xml>
                     * <SuiteId><![CDATA[ww4asffe9xxx4c0f4c]]></ SuiteId>
                     * <AuthCode><![CDATA[AUTHCODE]]></AuthCode>
                     * <InfoType><![CDATA[create_auth]]></InfoType>
                     * <TimeStamp>1403610513</TimeStamp>
                     * <State><![CDATA[123]]></State>
                     * </xml>
                     * 服务商的响应必须在1000ms内完成，以保证用户安装应用的体验。建议在接收到此事件时，先记录下AuthCode，并立即回应企业微信，之后再做相关业务的处理。
                     *
                     * 参数说明：
                     *
                     * 参数 说明
                     * SuiteId 第三方应用的SuiteId
                     * AuthCode 授权的auth_code,最长为512字节。用于获取企业的永久授权码。5分钟内有效
                     * InfoType create_auth
                     * TimeStamp 时间戳
                     * State	构造授权链接指定的state参数
                     */
                    $AuthCode = isset($datas['AuthCode']) ? trim($datas['AuthCode']) : ''; // 授权的auth_code,最长为512字节。用于获取企业的永久授权码。5分钟内有效
                    $State = isset($datas['State']) ? trim($datas['State']) : ''; // 构造授权链接指定的state参数
                    //https://developer.work.weixin.qq.com/document/path/95434
                    // （1）企业管理员扫代开发模版授权码时，授权完成后会推送授权成功通知到应用代开发模版回调url。
                    // （2）收到回调后，开发者通过获取企业永久授权码接口获取到的permanent_code，即为代开发应用的secret。
                    //（注意：此种情况获取企业永久授权码接口会多返回is_customized_app字段，且值为true，表示是代开发模版授权，另外接口不返回access_token字段）。
                    $this->getPermanentCode($AuthCode, $datas);
                } elseif ($InfoType == 'change_auth') { // 变更授权通知
                    /**
                     * 变更授权通知
                     * 当授权方（即授权企业）在企业微信管理端的授权管理中，修改了对应用的授权后，企业微信服务器推送变更授权通知。
                     * 服务商接收到变更通知之后，需自行调用获取企业授权信息进行授权内容变更比对。
                     *
                     * 请求方式：POST（HTTPS）
                     * 请求地址：https://127.0.0.1/suite/receive?msg_signature=3a7b08bb8e6dbce3c9671d6fdb69d15066227608&timestamp=1403610513&nonce=380320359
                     *
                     * 请求包体：
                     *
                     * <xml>
                     * <SuiteId><![CDATA[ww4asffe99exxx0f4c]]></SuiteId>
                     * <InfoType><![CDATA[change_auth]]></InfoType>
                     * <TimeStamp>1403610513</TimeStamp>
                     * <AuthCorpId><![CDATA[wxf8b4f85f3a794e77]]></AuthCorpId>
                     * <State><![CDATA[abc]]></State>
                     * </xml>
                     * 服务商的响应必须在1000ms内完成，以保证用户变更授权的体验。建议在接收到此事件时，立即回应企业微信，之后再做相关业务的处理。
                     *
                     * 参数说明：
                     *
                     * 参数 说明
                     * SuiteId 第三方应用的SuiteId
                     * InfoType change_auth
                     * TimeStamp 时间戳
                     * AuthCorpId 授权方的corpid
                     * State	构造授权链接指定的state参数
                     */
                    $AuthCorpId = isset($datas['AuthCorpId']) ? trim($datas['AuthCorpId']) : ''; // 授权方的corpid
                    $State = isset($datas['State']) ? trim($datas['State']) : ''; // 构造授权链接指定的state参数
                    // https://developer.work.weixin.qq.com/document/path/90642#%E5%8F%98%E6%9B%B4%E6%8E%88%E6%9D%83%E9%80%9A%E7%9F%A5

                    // 获取企业授权信息
                    if (!empty($this->authorizerConfig)) {
                        $suite_access_token = $this->authorizerConfig['suite_access_token'];
                        $permanent_code = $this->authorizerConfig['permanent_code'];
                        $authInfo = $this->objQyProvider->getAuthInfo($suite_access_token, $AuthCorpId, $permanent_code);
                        // {
                        //     "errcode":0 ,
                        //     "errmsg":"ok" ,
                        //     "auth_corp_info": 
                        //     {
                        //         "corpid": "xxxx",
                        //         "corp_name": "name",
                        //         "corp_type": "verified",
                        //         "corp_square_logo_url": "yyyyy",
                        //         "corp_user_max": 50,
                        //         "corp_agent_max": 30,
                        //         "corp_full_name":"full_name",
                        //         "verified_end_time":1431775834,
                        //         "subject_type": 1，
                        //         "corp_wxqrcode": "zzzzz",
                        //         "corp_scale": "1-50人",
                        //         "corp_industry": "IT服务",
                        //         "corp_sub_industry": "计算机软件/硬件/信息服务"
                        //         "location":"广东省广州市"
                        //     },
                        //     "auth_info":
                        //     {
                        //         "agent" :
                        //         [
                        //             {
                        //                 "agentid":1,
                        //                 "name":"NAME",
                        //                 "round_logo_url":"xxxxxx",
                        //                 "square_logo_url":"yyyyyy",
                        //                 "appid":1,
                        //                 "privilege":
                        //                 {
                        //                     "level":1,
                        //                     "allow_party":[1,2,3],
                        //                     "allow_user":["zhansan","lisi"],
                        //                     "allow_tag":[1,2,3],
                        //                     "extra_party":[4,5,6],
                        //                     "extra_user":["wangwu"],
                        //                     "extra_tag":[4,5,6]
                        //                 }
                        //             },
                        //             {
                        //                 "agentid":2,
                        //                 "name":"NAME2",
                        //                 "round_logo_url":"xxxxxx",
                        //                 "square_logo_url":"yyyyyy",
                        //                 "appid":5
                        //             }
                        //         ]
                        //     }
                        // }

                        if (!isset($authInfo['expires_in'])) {
                            $authInfo['expires_in'] = -7200;
                        }
                        if (!isset($authInfo['access_token'])) {
                            $authInfo['access_token'] = "";
                        }
                        $memo = [
                            'event_datas' => $datas,
                            'suite_access_token' => $suite_access_token,
                            'permanent_code' => $permanent_code,
                            'getAuthInfo' => $authInfo
                        ];
                        // 创建授权方企业
                        $this->modelQyweixinAuthCorp->createAndUpdateAuthCorpInfo($this->provider_appid, $this->authorizer_appid, $authInfo['access_token'], $authInfo['expires_in'], $authInfo['auth_corp_info'], $memo);
                        // 创建agent
                        $this->modelQyweixinAgent->createAndUpdateAuthAgentInfo($this->provider_appid, $this->authorizer_appid, $permanent_code, $authInfo['access_token'], $authInfo['expires_in'], $authInfo['auth_info']['agent'][0], $memo);
                    }
                } elseif ($InfoType == 'cancel_auth') { // 取消授权通知
                    /**
                     * 取消授权通知
                     * 当授权方（即授权企业）在企业微信管理端的授权管理中，取消了对应用的授权托管后，企业微信后台会推送取消授权通知。
                     * 请求方式：POST（HTTPS）
                     * 请求地址：https://127.0.0.1/suite/receive?msg_signature=3a7b08bb8e6dbce3c9671d6fdb69d15066227608&timestamp=1403610513&nonce=380320359
                     *
                     * 请求包体：
                     *
                     * <xml>
                     * <SuiteId><![CDATA[ww4asffe99e54cxxxx]]></ SuiteId>
                     * <InfoType><![CDATA[cancel_auth]]></InfoType>
                     * <TimeStamp>1403610513</TimeStamp>
                     * <AuthCorpId><![CDATA[wxf8b4f85fxx794xxx]]></AuthCorpId>
                     * </xml>
                     * 服务商的响应必须在1000ms内完成，以保证用户取消授权的体验。建议在接收到此事件时，立即回应企业微信，之后再做相关业务的处理。注意，服务商收到取消授权事件后，应当确保删除该企业所有相关的数据。
                     *
                     * 参数说明：
                     *
                     * 参数 说明
                     * SuiteId 第三方应用的SuiteId
                     * InfoType cancel_auth
                     * TimeStamp 时间戳
                     * AuthCorpId 授权方企业的corpid
                     */
                    $AuthCorpId = isset($datas['AuthCorpId']) ? trim($datas['AuthCorpId']) : ''; // 授权方企业的corpid
                } elseif ($InfoType == 'reset_permanent_code') {
                    // 重置永久授权码通知
                    // 最后更新：2022/06/23
                    // 在服务商管理端的代开发应用详情页，点击“重新获取secret”会触发该事件的回调，服务商收到回调事件后，可使用AuthCode通过获取企业永久授权码接口获取代开发应用最新的secret（即permanent_code字段）。

                    // 请求方式：POST（HTTPS）
                    // 请求地址：https://127.0.0.1/suite/receive?msg_signature=3a7b08bb8e6dbce3c9671d6fdb69d15066227608&timestamp=1403610513&nonce=380320359

                    // 请求包体：

                    // <xml>
                    // 	<SuiteId><![CDATA[dk4asffe9xxx4c0f4c]]></SuiteId>
                    // 	<AuthCode><![CDATA[AUTHCODE]]></AuthCode>
                    // 	<InfoType><![CDATA[reset_permanent_code]]></InfoType>
                    // 	<TimeStamp>1403610513</TimeStamp>
                    // </xml>
                    // 服务商的响应必须在1000ms内完成，以保证用户安装应用的体验。建议在接收到此事件时，先记录下AuthCode，并立即回应企业微信，之后再做相关业务的处理。
                    // 参数说明：

                    // 参数	说明
                    // SuiteId	代开发自建应用的SuiteId
                    // AuthCode	临时授权码,最长为512字节。用于获取企业永久授权码。10分钟内有效
                    // InfoType	reset_permanent_code
                    // TimeStamp	时间戳
                    $AuthCode = isset($datas['AuthCode']) ? trim($datas['AuthCode']) : ''; // 临时授权码

                    //https://developer.work.weixin.qq.com/document/path/95434
                    // 服务商可在服务商管理端的代开发应用详情页点击“重新获取”(见下图)来重置应用secret。
                    // 点击“重新获取”后，企业微信后台会回调重置永久授权码通知。
                    // 开发者收到回调事件后，可使用AuthCode通过获取企业永久授权码接口获取代开发应用最新的secret（即permanent_code字段）。
                    $this->getPermanentCode($AuthCode, $datas);
                }

                /**
                 * ==================================================================================
                 * ====================================以下逻辑请勿修改===================================
                 * ==================================================================================
                 */
                if (empty($response)) {
                    $response = "success";
                }

                $this->requestLogDatas['response'] = $response;

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
            return "";
        }
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        // 第三方服务商运用ID
        $this->provider_appid = isset($_GET['provider_appid']) ? trim($_GET['provider_appid']) : "";
        // 第3方应用
        $this->authorizer_appid = isset($_GET['suite_id']) ? trim($_GET['suite_id']) : "";

        // 创建service
        $this->qyService = new \App\Qyweixin\Services\QyService($this->authorizer_appid, $this->provider_appid, $this->agentid);
        $this->providerConfig = $this->qyService->getAppConfig4Provider();
        if (empty($this->providerConfig)) {
            throw new \Exception("provider_appid:{$this->provider_appid}所对应的记录不存在");
        }

        // 授权方ID
        if (!empty($this->authorizer_appid)) {
            $this->authorizerConfig = $this->qyService->getAppConfig4Authorizer();
            if (empty($this->authorizerConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
            }
        }
        $this->objQyProvider = $this->qyService->getQyweixinProvider();
    }

    protected function getDataFromWeixinServer()
    {
        $postStr = file_get_contents('php://input');
        $datas = $this->revieve($postStr);
        // 需要解密
        if ($this->isNeedDecryptAndEncrypt) {
            $this->requestLogDatas['aes_info']['receiveId'] = $datas['ToUserName'];
            if (empty($this->requestLogDatas['aes_info']['EncodingAESKey'])) {
                throw new \Exception('application EncodingAESKey is null');
            }

            $decryptMsg = "";
            $pc = new \Weixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($this->requestLogDatas['aes_info']['verify_token'], $this->requestLogDatas['aes_info']['EncodingAESKey'], $this->requestLogDatas['aes_info']['receiveId']);
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

            if ($this->requestLogDatas['log_type'] == 'authorizelog') { // 授权事件接收URL
                $this->modelQyweixinAuthorizeLog->record($this->requestLogDatas);
            }
        }
    }

    protected function getPermanentCode($AuthCode, $datas)
    {
        if (!empty($this->authorizerConfig)) {
            // 使用授权码换取公众号的接口调用凭据和授权信息
            $suite_access_token = $this->authorizerConfig['suite_access_token'];
            $permanentCodeInfo = $this->objQyProvider->getPermanentCode($suite_access_token, $AuthCode);
            // "permanent_code": "kt4M0Hacrr5iVBw48GVj0L6imrzTkwlDlzNpyR_vP_E", 
            // "auth_corp_info": {
            //     "corpid": "wpr7jrBgAArXPRgLB8LPaxpZvRIFkyeg", 
            //     "corp_name": "潮玩地", 
            //     "corp_type": "verified", 
            //     "corp_round_logo_url": "http://p.qpic.cn/pic_wework/3746516520/3731dc24dd2bf466b68b64e4ac7a51d39e8df80ba0d3a5ab/0", 
            //     "corp_square_logo_url": "https://p.qlogo.cn/bizmail/F0fukINIv59Z968ulh4AQT1AhWOTQ3ibicOeDWqmH8AX5f5qjZ8v7Ghg/0", 
            //     "corp_user_max": 500, 
            //     "corp_wxqrcode": "https://wework.qpic.cn/wwpic/669589_0W5HILq7SqiJema_1661520574/0", 
            //     "corp_full_name": "潮玩地", 
            //     "subject_type": 1, 
            //     "verified_end_time": 1691223831, 
            //     "corp_scale": "51-100人", 
            //     "corp_industry": "批发/零售", 
            //     "corp_sub_industry": "零售", 
            //     "location": ""
            // }, 
            // "auth_info": {
            //     "agent": [
            //         {
            //             "agentid": 1000017, 
            //             "name": "企微助手", 
            //             "square_logo_url": "https://wework.qpic.cn/wwpic/809268_NV47ah_dT3OIIyZ_1661491101/0", 
            //             "privilege": {
            //                 "level": 0, 
            //                 "allow_party": [ ], 
            //                 "allow_user": [ ], 
            //                 "allow_tag": [ ], 
            //                 "extra_party": [ ], 
            //                 "extra_user": [ ], 
            //                 "extra_tag": [ ]
            //             }, 
            //             "is_customized_app": true
            //         }
            //     ]
            // }, 
            // "auth_user_info": {
            //     "userid": "wor7jrBgAAMJ4S40H0s56lp6O9sntFpw", 
            //     "name": "郭永荣", 
            //     "avatar": "http://wework.qpic.cn/bizmail/3781P7vBiadFNmvYJic4sy6n3uDrPfwveaScRhTExN7NBtP5cPp99MJA/0", 
            //     "open_userid": "wor7jrBgAAMJ4S40H0s56lp6O9sntFpw"
            // }
            // 更新accesstoken
            if (!isset($permanentCodeInfo['expires_in'])) {
                $permanentCodeInfo['expires_in'] = -7200;
            }
            if (!isset($permanentCodeInfo['access_token'])) {
                $permanentCodeInfo['access_token'] = "";
            }
            $memo = [
                'event_datas' => $datas,
                'suite_access_token' => $suite_access_token,
                'auth_code' => $AuthCode,
                'auth_expires_in' => $permanentCodeInfo['expires_in'],
                'getPermanentCode' => $permanentCodeInfo
            ];
            $this->modelQyweixinAuthorizer->createAndUpdateAuthorizer($this->provider_appid, $this->authorizer_appid, $permanentCodeInfo['access_token'], $permanentCodeInfo['access_token'], $permanentCodeInfo['expires_in'], $permanentCodeInfo['permanent_code'], $permanentCodeInfo, $memo);
            // 创建授权方企业
            $this->modelQyweixinAuthCorp->createAndUpdateAuthCorpInfo($this->provider_appid, $this->authorizer_appid, $permanentCodeInfo['access_token'], $permanentCodeInfo['expires_in'], $permanentCodeInfo['auth_corp_info'], $memo);
            // 创建agent
            $this->modelQyweixinAgent->createAndUpdateAuthAgentInfo($this->provider_appid, $this->authorizer_appid, $permanentCodeInfo['permanent_code'], $permanentCodeInfo['access_token'], $permanentCodeInfo['expires_in'], $permanentCodeInfo['auth_info']['agent'][0], $memo);
        }
    }
}
