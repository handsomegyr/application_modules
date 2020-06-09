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
    protected $activity_id = 2;

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
        // http://wxcrm.eintone.com/qyweixin/api/provider/install3rdapp?provider_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=xx&suite_id=xx
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

            // 2 获取预授权码（pre_auth_code）
            $suite_access_token = $this->agentConfig['access_token'];
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
        // http://wxcrm.eintone.com/qyweixin/api/provider/install3rdappcallback?auth_code=queryauthcode@@@YGWj2XtzxxwQQfpIW6VGOJvDALlLfFwSy9anTBQs0sEMXsXZMrTRVPbhXpmqh265vpKz_YIgj2bJ-WJR2oGABw&expires_in=3600&state=xx
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
            $state = empty($_SESSION['state']) ? "" : $_SESSION['state'];
            if ($state != $this->state) {
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
        // http://wxcrm.eintone.com/qyweixin/api/provider/authorizecallback?provider_appid=wxca8519f703c07d32
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
            $AESInfo['EncodingAESKey'] = $encodingAESKey;
            $AESInfo['verify_token'] = $verifyToken;
            $AESInfo['receiveId'] = $receiveId;
            $this->requestLogDatas['aes_info'] = $AESInfo;
            if (empty($verifyToken)) {
                throw new \Exception('application verify_token is null. config:' . \json_encode($this->providerConfig));
            }

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
                $this->authorizerConfig = $this->qyService->getAppConfig4Authorizer();
                if (empty($this->authorizerConfig)) {
                    return "success";
                    // throw new \Exception("provider_appid:{$this->provider_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
                }
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
                // 获取第三方应用suite_access_token
                $suite_secret = $this->authorizerConfig['appsecret'];
                $suiteToken = $this->objQyProvider->getSuiteToken($SuiteId, $suite_secret, $SuiteTicket);

                // 更新suite_access_token
                $this->modelQyweixinAuthorizer->updateSuiteAccessToken($this->authorizerConfig['_id'], $suiteToken['component_access_token'], $suiteToken['expires_in'], $SuiteTicket);
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
                 */
                $AuthCode = isset($datas['AuthCode']) ? trim($datas['AuthCode']) : ''; // 授权的auth_code,最长为512字节。用于获取企业的永久授权码。5分钟内有效
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
                 */
                $AuthCorpId = isset($datas['AuthCorpId']) ? trim($datas['AuthCorpId']) : ''; // 授权方的corpid
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
}
