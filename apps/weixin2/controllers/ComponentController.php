<?php

namespace App\Weixin2\Controllers;

/**
 * 小程序或者公众号授权给第三方平台的技术实现流程
 * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=&lang=zh_CN
 */
class ComponentController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 2;

    /**
     * @var \App\Weixin2\Models\Component\Component
     */
    private $modelWeixinopenComponent;

    /**
     * @var \App\Weixin2\Models\Authorize\Authorizer
     */
    private $modelWeixinopenAuthorizer;

    /**
     * @var \App\Weixin2\Models\Component\ComponentLoginBindTracking
     */
    private $modelWeixinopenComponentLoginBindTracking;

    /**
     * @var \App\Weixin2\Models\Authorize\AuthorizeLog
     */
    private $modelWeixinopenAuthorizeLog;

    private $trackingKey = "公众号授权给第三方平台流程";

    /** @var  \Weixin\Component */
    private $objWeixinComponent;

    private $component_appid;

    private $componentConfig;

    private $authorizer_appid;

    // private $authorizerConfig;

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

        $this->modelWeixinopenComponent = new \App\Weixin2\Models\Component\Component();
        $this->modelWeixinopenAuthorizer = new \App\Weixin2\Models\Authorize\Authorizer();
        $this->modelWeixinopenComponentLoginBindTracking = new \App\Weixin2\Models\Component\ComponentLoginBindTracking();
        $this->modelWeixinopenAuthorizeLog = new \App\Weixin2\Models\Authorize\AuthorizeLog();
    }

    /**
     * 登录授权的发起
     * 当全网发布之后，需要做一个页面，上面加一个link，地址就是以下的地址，做法如http://weshopdemo.umaman.com/component/login.html
     * 方式一：授权注册页面扫码授权
     *
     * 授权页网址为：
     *
     * https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=xxxx&pre_auth_code=xxxxx&redirect_uri=xxxx&auth_type=xxx
     */
    public function loginAction()
    {
        // http://wxcrm.eintone.com/component/login.html
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/component/login?component_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F
        // http://wxcrm.eintone.com/weixinopen/api/component/login?component_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            // 初始化
            $this->doInitializeLogic();

            $auth_type = isset($_GET['auth_type']) ? (trim($_GET['auth_type'])) : '';
            $biz_appid = isset($_GET['biz_appid']) ? (trim($_GET['biz_appid'])) : '';
            $redirect = isset($_GET['redirect']) ? (trim($_GET['redirect'])) : '';
            if (empty($redirect)) {
                return abort(500, "回调地址未定义");
            }

            // 2 获取预授权码（pre_auth_code）
            $preAuthCodeInfo = $this->objWeixinComponent->apiCreatePreauthcode();
            $pre_auth_code = $preAuthCodeInfo['pre_auth_code'];

            // 3 引入用户进入授权页
            // 存储跳转地址
            $_SESSION['redirect'] = $redirect;
            $_SESSION['login_type'] = "login";
            $_SESSION['component_appid'] = $this->component_appid;

            $moduleName = 'weixin2';
            $controllerName = $this->controllerName;
            $scheme = $this->getRequest()->getScheme();
            $redirectUri = $scheme . '://';
            $redirectUri .= $_SERVER["HTTP_HOST"];
            $redirectUri .= '/' . $moduleName;
            $redirectUri .= '/' . $controllerName;
            $redirectUri .= '/logincallback';
            // 授权处理
            $redirectUri = $this->objWeixinComponent->getComponentLoginPage($pre_auth_code, $redirectUri, $auth_type, $biz_appid, false);
            header("location:{$redirectUri}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 登录授权的发起
     * 当全网发布之后，需要做一个页面，上面加一个link，地址就是以下的地址，做法如http://weshopdemo.umaman.com/component/bind.html
     * 方式二：点击移动端链接快速授权
     * 第三方平台方可以生成授权链接，将链接通过移动端直接发给授权管理员，管理员确认后即授权成功。
     * 授权链接为：
     *
     * https://mp.weixin.qq.com/safe/bindcomponent?action=bindcomponent&auth_type=3&no_scan=1&component_appid=xxxx&pre_auth_code=xxxxx&redirect_uri=xxxx&auth_type=xxx&biz_appid=xxxx#wechat_redirect
     * 注：auth_type、biz_appid两个字段互斥。
     */
    public function bindAction()
    {
        // http://wxcrm.eintone.com/component/bind.html
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/component/bind?component_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F
        // http://wxcrm.eintone.com/weixinopen/api/component/bind?component_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            // 初始化
            $this->doInitializeLogic();

            $auth_type = isset($_GET['auth_type']) ? (trim($_GET['auth_type'])) : '';
            $biz_appid = isset($_GET['biz_appid']) ? (trim($_GET['biz_appid'])) : '';
            $redirect = isset($_GET['redirect']) ? (trim($_GET['redirect'])) : '';
            if (empty($redirect)) {
                return abort(500, "回调地址未定义");
            }

            // 2 获取预授权码（pre_auth_code）
            $preAuthCodeInfo = $this->objWeixinComponent->apiCreatePreauthcode();
            $pre_auth_code = $preAuthCodeInfo['pre_auth_code'];

            // 3 引入用户进入授权页
            // 存储跳转地址
            $_SESSION['redirect'] = $redirect;
            $_SESSION['login_type'] = "bind";
            $_SESSION['component_appid'] = $this->component_appid;

            $moduleName = 'weixin2';
            $controllerName = $this->controllerName;
            $scheme = $this->getRequest()->getScheme();
            $redirectUri = $scheme . '://';
            $redirectUri .= $_SERVER["HTTP_HOST"];
            $redirectUri .= '/' . $moduleName;
            $redirectUri .= '/' . $controllerName;
            $redirectUri .= '/logincallback';

            // 授权处理
            $redirectUri = $this->objWeixinComponent->getBindcomponentUrl($pre_auth_code, $redirectUri, $auth_type, $biz_appid, false);
            header("location:{$redirectUri}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 步骤3：用户确认并同意登录授权给第三方平台方
     *
     * 用户进入第三方平台授权页后，需要确认并同意将自己的公众号或小程序授权给第三方平台方，完成授权流程。
     *
     * 步骤4：授权后回调URI，得到授权码（authorization_code）和过期时间
     *
     * 授权流程完成后，授权页会自动跳转进入回调URI，并在URL参数中返回授权码和过期时间(redirect_url?auth_code=xxx&expires_in=600)
     * 步骤5：利用授权码调用公众号或小程序的相关API
     *
     * 在得到授权码后，第三方平台方可以使用授权码换取授权公众号或小程序的接口调用凭据（authorizer_access_token，也简称为令牌），再通过该接口调用凭据，按照公众号开发者文档或小程序开发文档的说明，去调用公众号或小程序相关API。
     * （能调用哪些API，取决于用户将哪些权限集授权给了第三方平台方，也取决于公众号或小程序自身拥有哪些接口权限），使用JS SDK等能力。具体请见【公众号第三方平台的接口说明】
     *
     * 下面对各API和机制进行介绍（特别注意，所有API调用需要验证调用者IP地址。只有在第三方平台申请时填写的白名单IP地址列表内的IP地址，才能合法调用，其他一律拒绝）：
     */
    public function logincallbackAction()
    {
        // http://wxcrm.eintone.com/weixinopen/api/component/logincallback?auth_code=queryauthcode@@@YGWj2XtzxxwQQfpIW6VGOJvDALlLfFwSy9anTBQs0sEMXsXZMrTRVPbhXpmqh265vpKz_YIgj2bJ-WJR2oGABw&expires_in=3600
        try {
            $component_appid = empty($_SESSION['component_appid']) ? "" : $_SESSION['component_appid'];
            if (empty($component_appid)) {
                throw new \Exception("appid未定义");
            }
            $_GET['component_appid'] = $component_appid;

            // 初始化
            $this->doInitializeLogic();

            $expires_in = isset($_GET['expires_in']) ? trim($_GET['expires_in']) : '';
            $auth_code = isset($_GET['auth_code']) ? trim($_GET['auth_code']) : '';
            if (empty($auth_code)) {
                return abort(500, "auth_code不能为空");
            }
            $redirect = empty($_SESSION['redirect']) ? "" : $_SESSION['redirect'];
            if (empty($redirect)) {
                return abort(500, "回调地址未定义");
            }

            // 使用授权码换取公众号的接口调用凭据和授权信息
            $authInfo = $this->objWeixinComponent->apiQueryAuth($auth_code);
            // Array (
            // [appid] => wxca8519f703c07d32
            // [authorizer_access_token] => doQIVUEgvqAgLCeN3GtniVVEFfV-SoZ2sPKSepbLTFhy5jZbHXRdzd2qDd1AsZq_xm5c0BKfyO8X0RZ9YAxEPiLmErooso1zUdcA_mbL0ftq75Ax2i1hd6DwoQ8sMPUHKDYfALDPID
            // [expires_in] => 7200
            // [authorizer_refresh_token] => refreshtoken@@@MLsM93Cl_nO3WMSQ2enriI9sf0-gMgawkCWJA8dtOxQ
            // [func_info] => Array (
            // [0] => Array ( [funcscope_category] => Array ( [id] => 1 ) )
            // [1] => Array ( [funcscope_category] => Array ( [id] => 15 ) )
            // [2] => Array ( [funcscope_category] => Array ( [id] => 4 ) )
            // [3] => Array ( [funcscope_category] => Array ( [id] => 7 ) )
            // [4] => Array ( [funcscope_category] => Array ( [id] => 2 ) )
            // [5] => Array ( [funcscope_category] => Array ( [id] => 3 ) )
            // [6] => Array ( [funcscope_category] => Array ( [id] => 11 ) )
            // [7] => Array ( [funcscope_category] => Array ( [id] => 6 ) )
            // [8] => Array ( [funcscope_category] => Array ( [id] => 5 ) )
            // [9] => Array ( [funcscope_category] => Array ( [id] => 8 ) )
            // [10] => Array ( [funcscope_category] => Array ( [id] => 13 ) )
            // [11] => Array ( [funcscope_category] => Array ( [id] => 9 ) )
            // [12] => Array ( [funcscope_category] => Array ( [id] => 10 ) )
            // [13] => Array ( [funcscope_category] => Array ( [id] => 12 ) )
            // )
            // )
            $authorizationInfo = $authInfo['authorization_info'];

            // 更新accesstoken
            $memo = [
                'auth_code' => $auth_code,
                'auth_expires_in' => $expires_in,
                'apiQueryAuth' => $authInfo
            ];
            $this->modelWeixinopenAuthorizer->createAndUpdateAuthorizer($this->componentConfig['appid'], $authorizationInfo['authorizer_appid'], $authorizationInfo['authorizer_access_token'], $authorizationInfo['authorizer_refresh_token'], $authorizationInfo['expires_in'], $authorizationInfo['func_info'], $memo);

            $login_type = empty($_SESSION['login_type']) ? "" : $_SESSION['login_type'];
            if ($login_type == "bind") {
                $this->trackingKey = "点击移动端链接快速授权";
            } elseif ($login_type == "login") {
                $this->trackingKey = "授权注册页面扫码授权";
            }
            $this->modelWeixinopenComponentLoginBindTracking->record($this->component_appid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $authorizationInfo['authorizer_appid']);

            header("location:{$redirect}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 授权事件接收URL
     * 在公众号第三方平台创建审核通过后，微信服务器会向其“授权事件接收URL”每隔10分钟定时推送component_verify_ticket。第三方平台方在收到ticket推送后也需进行解密（详细请见【消息加解密接入指引】），接收到后必须直接返回字符串success。
     */
    public function authorizecallbackAction()
    {
        // http://wxcrm.eintone.com/weixinopen/api/component/authorizecallback?component_appid=wxca8519f703c07d32
        try {
            // 兼容的写法
            $appid = isset($_GET['appid']) ? trim($_GET['appid']) : '';
            if (!empty($appid)) {
                $_GET['component_appid'] = $appid;
            }
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

            $encodingAESKey = isset($this->componentConfig['EncodingAESKey']) ? $this->componentConfig['EncodingAESKey'] : '';
            $verifyToken = isset($this->componentConfig['verify_token']) ? $this->componentConfig['verify_token'] : '';
            $receiveId = $this->componentConfig['appid'];
            $AESInfo['EncodingAESKey'] = $encodingAESKey;
            $AESInfo['verify_token'] = $verifyToken;
            $AESInfo['receiveId'] = $receiveId;
            $this->requestLogDatas['aes_info'] = $AESInfo;
            if (empty($verifyToken)) {
                throw new \Exception('application verify_token is null. config:' . \json_encode($this->componentConfig));
            }

            // 签名正确，将接受到的xml转化为数组数据并记录数据
            $datas = $this->getDataFromWeixinServer();
            foreach ($datas as $dtkey => $dtvalue) {
                $this->requestLogDatas[$dtkey] = $dtvalue;
            }
            $this->requestLogDatas['response'] = 'success';

            $AppId = isset($datas['AppId']) ? trim($datas['AppId']) : '';
            $InfoType = isset($datas['InfoType']) ? trim($datas['InfoType']) : '';
            $CreateTime = isset($datas['CreateTime']) ? ($datas['CreateTime']) : time();

            // 关于重试的消息排重
            $uniqueKey = $AppId . "-" . $CreateTime . "-" . $InfoType;
            $this->requestLogDatas['lock_uniqueKey'] = $uniqueKey;
            if (!empty($uniqueKey)) {
                $objLock = new \iLock(md5($uniqueKey));
                if ($objLock->lock()) {
                    return "success";
                }
            }

            /**
             * ==================================================================================
             * ====================================以上逻辑请勿修改===================================
             * ==================================================================================
             */

            if ($InfoType == 'component_verify_ticket') { // 推送component_verify_ticket协议
                /**
                 * <xml>
                 * <AppId></AppId>
                 * <CreateTime>1413192605 </CreateTime>
                 * <InfoType> </InfoType>
                 * <ComponentVerifyTicket> </ComponentVerifyTicket>
                 * </xml>
                 */
                $componentVerifyTicket = isset($datas['ComponentVerifyTicket']) ? trim($datas['ComponentVerifyTicket']) : ''; // Ticket内容

                // 获取第三方平台component_access_token
                $componentToken = $this->objWeixinComponent->apiComponentToken($componentVerifyTicket);

                // 更新component_access_token
                $this->modelWeixinopenComponent->updateAccessToken($this->componentConfig['_id'], $componentToken['component_access_token'], $componentToken['expires_in'], $componentVerifyTicket);

                // 消息解密
            } elseif ($InfoType == 'unauthorized') { // 取消授权通知
                /**
                 * <xml>
                 * <AppId>第三方平台appid</AppId>
                 * <CreateTime>1413192760</CreateTime>
                 * <InfoType>unauthorized</InfoType>
                 * <AuthorizerAppid>公众号appid</AuthorizerAppid>
                 * </xml>
                 */
                $AuthorizerAppid = isset($datas['AuthorizerAppid']) ? trim($datas['AuthorizerAppid']) : ''; // 公众号appid
            } elseif ($InfoType == 'authorized') { // 授权成功通知
                /**
                 * <xml>
                 * <AppId>第三方平台appid</AppId>
                 * <CreateTime>1413192760</CreateTime>
                 * <InfoType>authorized</InfoType>
                 * <AuthorizerAppid>公众号appid</AuthorizerAppid>
                 * <AuthorizationCode>授权码（code）</AuthorizationCode>
                 * <AuthorizationCodeExpiredTime>过期时间</AuthorizationCodeExpiredTime>
                 * </xml>
                 */
                $AuthorizerAppid = isset($datas['AuthorizerAppid']) ? trim($datas['AuthorizerAppid']) : ''; // 公众号appid
                $AuthorizationCode = isset($datas['AuthorizationCode']) ? trim($datas['AuthorizationCode']) : ''; // 授权码（code）
                $AuthorizationCodeExpiredTime = isset($datas['AuthorizationCodeExpiredTime']) ? trim($datas['AuthorizationCodeExpiredTime']) : ''; // 过期时间
            } elseif ($InfoType == 'updateauthorized') { // 授权更新通知
                /**
                 * <xml>
                 * <AppId>第三方平台appid</AppId>
                 * <CreateTime>1413192760</CreateTime>
                 * <InfoType>updateauthorized</InfoType>
                 * <AuthorizerAppid>公众号appid</AuthorizerAppid>
                 * <AuthorizationCode>授权码（code）</AuthorizationCode>
                 * <AuthorizationCodeExpiredTime>过期时间</AuthorizationCodeExpiredTime>
                 * </xml>
                 */
                $AuthorizerAppid = isset($datas['AuthorizerAppid']) ? trim($datas['AuthorizerAppid']) : ''; // 公众号appid
                $AuthorizationCode = isset($datas['AuthorizationCode']) ? trim($datas['AuthorizationCode']) : ''; // 授权码（code）
                $AuthorizationCodeExpiredTime = isset($datas['AuthorizationCodeExpiredTime']) ? trim($datas['AuthorizationCodeExpiredTime']) : ''; // 过期时间
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
     * 获取（刷新）授权公众号的接口调用凭据
     */
    public function authorizerTokenAction()
    {
        // http://wxcrm.eintone.com/weixinopen/api/component/authorizer-token?component_appid=wxca8519f703c07d32&authorizer_appid=xxx
        try {
            // 初始化
            $this->doInitializeLogic();

            $authorizer_appid = $this->request->get("authorizer_appid", "");
            if (empty($authorizer_appid)) {
                throw new \Exception("authorizer_appid:{$authorizer_appid}所对应的记录不存在");
            }

            $authorizerInfo = $this->modelWeixinopenAuthorizer->getInfoByAppId($this->component_appid, $authorizer_appid);

            if (empty($authorizerInfo)) {
                throw new \Exception("component_appid:{$this->component_appid}和authorizer_appid:{$authorizer_appid}所对应的记录不存在");
            }

            // 该API用于在授权方令牌（authorizer_access_token）失效时，可用刷新令牌（authorizer_refresh_token）获取新的令牌。
            $authorizerTokenInfo = $this->objWeixinComponent->apiAuthorizerToken($authorizer_appid, $authorizerInfo['refresh_token']);

            // 更新accesstoken
            $ret = $this->modelWeixinopenAuthorizer->updateAccessToken($authorizerInfo['_id'], $authorizerTokenInfo['authorizer_access_token'], $authorizerTokenInfo['authorizer_refresh_token'], $authorizerTokenInfo['expires_in'], null);

            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 获取（刷新）授权公众号的接口调用凭据
     */
    public function getComponentAccessTokenAction()
    {
        // http://wxcrm.eintone.com/weixinopen/api/component/get-component-access-token?component_appid=wxca8519f703c07d32
        try {
            // 初始化
            $this->doInitializeLogic();
            return $this->result("OK", $this->componentConfig);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        // 第三方平台运用ID
        $this->component_appid = isset($_GET['component_appid']) ? trim($_GET['component_appid']) : "";
        // 授权方ID
        $this->authorizer_appid = isset($_GET['authorizer_appid']) ? trim($_GET['authorizer_appid']) : "";
        // 创建service
        $this->weixinopenService = new \App\Weixin2\Services\WeixinService($this->authorizer_appid, $this->component_appid);
        $this->componentConfig = $this->weixinopenService->getAppConfig4Component();
        if (empty($this->componentConfig)) {
            throw new \Exception("component_appid:{$this->component_appid}所对应的记录不存在");
        }
        $this->objWeixinComponent = $this->weixinopenService->getWeixinComponent();
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
                $this->modelWeixinopenAuthorizeLog->record($this->requestLogDatas);
            }
        }
    }
}
