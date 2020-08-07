<?php

namespace App\Weixin2\Controllers;

/**
 * 小程序授权
 */
class MiniappController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 4;

    /**
     * @var \App\Weixin2\Models\User\User
     */
    private $modelWeixinopenUser;

    /**
     * @var \App\Weixin2\Models\Authorize\Authorizer
     */
    private $modelWeixinopenAuthorizer;

    /**
     * @var \App\Weixin2\Models\ScriptTracking
     */
    private $modelWeixinopenScriptTracking;

    /**
     * @var \App\Weixin2\Models\SnsApplication
     */
    private $modelWeixinopenSnsApplication;

    // lock key
    private $lock_key_prefix = 'weixinopen_miniapp_sns_';

    private $cookie_session_key = 'weixinopen_miniapp_sns_';

    private $sessionKey;

    private $trackingKey = "小程序授权";

    private $appid;

    private $appConfig;

    private $component_appid;

    // private $componentConfig;

    private $authorizer_appid;

    private $authorizerConfig;

    //应用类型 1:公众号 2:小程序 3:订阅号
    private $app_type = 0;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();

        $this->modelWeixinopenUser = new \App\Weixin2\Models\User\User();
        $this->modelWeixinopenAuthorizer = new \App\Weixin2\Models\Authorize\Authorizer();
        $this->modelWeixinopenScriptTracking = new \App\Weixin2\Models\ScriptTracking();
        $this->modelWeixinopenSnsApplication = new \App\Weixin2\Models\SnsApplication();

        $_SESSION['miniapp_start_time'] = microtime(true);
    }

    /**
     * 引导用户去授权
     */
    public function authorizeAction()
    {
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/miniappsns/authorize?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_userinfo&refresh=1
        // http://www.miniappmodule.com/weixinopen/api/miniappsns/authorize?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_userinfo&refresh=1
        // http://www.miniappmodule.com/weixinopen/api/miniappsns/authorize?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_userinfo&refresh=1
        try {
            // 初始化
            $this->doInitializeLogic();

            $redirect = isset($_GET['redirect']) ? (trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址

            // $dc = isset($_GET['dc']) ? intval($_GET['dc']) : 1; // 是否检查回调域名
            $dc = empty($this->appConfig['is_cb_url_check']) ? 0 : 1; // 是否检查回调域名

            $refresh = isset($_GET['refresh']) ? intval($_GET['refresh']) : 0; // 是否刷新

            if ($dc) {
                // 添加重定向域的检查
                // $list = $this->modelWeixinopenCallbackurls->getValidCallbackUrlList($this->authorizer_appid, $this->component_appid, true);
                // $hostret = $this->modelWeixinopenCallbackurls->getHost($redirect);
                // return Result::success($hostret);
                $isValid = $this->modelWeixinopenCallbackurls->isValid($this->authorizer_appid, $this->component_appid, $redirect);
                if (empty($isValid)) {
                    throw new \Exception("回调地址不合法");
                }
            }

            if (!$refresh && !empty($_SESSION[$this->sessionKey])) {
                $arrAccessToken = $_SESSION[$this->sessionKey];
                $redirect = $this->getRedirectUrl4Sns($redirect, $arrAccessToken);
                $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid'], $this->appConfig['_id']);
                header("location:{$redirect}");
                exit();
            } else {
                // 存储跳转地址
                $_SESSION['redirect'] = $redirect;
                $_SESSION['state'] = $this->state;
                $_SESSION['appid'] = $this->appid;

                $moduleName = 'weixin2';
                $controllerName = $this->controllerName;
                $scheme = $this->getRequest()->getScheme();
                $redirectUri = $scheme . '://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/' . $moduleName;
                $redirectUri .= '/' . $controllerName;
                $redirectUri .= '/callback';

                // 授权处理
                //应用类型 1:公众号 2:小程序 3:订阅号
                if ($this->app_type == \App\Weixin2\Models\Authorize\Authorizer::APPTYPE_PUB) {
                    $objSns = new \Weixin\Token\Sns($this->authorizer_appid, $this->authorizerConfig['appsecret']);
                } else {
                    throw new \Exception('该运用不支持授权操作');
                }
                $objSns->setScope($this->scope);
                $objSns->setState($this->state);
                $objSns->setRedirectUri($redirectUri);
                $redirectUri = $objSns->getAuthorizeUrl(false);
                header("location:{$redirectUri}");
                exit();
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 第二步：获取code
     *
     * 用户允许授权后，将会重定向到redirect_uri的网址上，并且带上code, state以及appid
     *
     * redirect_uri?code=CODE&state=STATE&appid=APPID
     * 若用户禁止授权，则重定向后不会带上code参数，仅会带上state参数
     *
     * redirect_uri?state=STATE
     * 第二步：通过code换取access_token
     * 请求方法
     * 获取第一步的code后，请求以下链接获取access_token：
     */
    public function callbackAction()
    {
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/miniappsns/callback?appid=xxx&code=xxx&scope=auth_user&state=xxx
        try {
            $appid = empty($_SESSION['appid']) ? "" : $_SESSION['appid'];
            if (empty($appid)) {
                throw new \Exception("appid未定义");
            }
            $_GET['appid'] = $appid;

            // 初始化
            $this->doInitializeLogic();

            $code = isset($_GET['code']) ? ($_GET['code']) : '';
            if (empty($code)) {
                // 如果用户未授权登录，点击取消，自行设定取消的业务逻辑
                throw new \Exception("点击取消,用户未授权登录");
            }
            $redirect = empty($_SESSION['redirect']) ? "" : $_SESSION['redirect'];
            if (empty($redirect)) {
                throw new \Exception("回调地址未定义");
            }

            $state = empty($_SESSION['state']) ? "" : $_SESSION['state'];
            if ($state != $this->state) {
                throw new \Exception("state发生了改变");
            }

            $updateInfoFromWx = false;
            $sourceFromUserName = !empty($_GET['FromUserName']) ? $_GET['FromUserName'] : '';

            // 第二步：通过code换取access_token
            //应用类型 1:公众号 2:小程序 3:订阅号
            if ($this->app_type == \App\Weixin2\Models\Authorize\Authorizer::APPTYPE_PUB) {
                $objSns = new \Weixin\Token\Sns($this->authorizer_appid, $this->authorizerConfig['appsecret']);
                $arrAccessToken = $objSns->getAccessToken();
                if (!empty($arrAccessToken['errcode'])) {
                    throw new \Exception("获取token失败,原因:" . json_encode($arrAccessToken, JSON_UNESCAPED_UNICODE));
                }
            } else {
                throw new \Exception('该运用不支持授权操作');
            }

            // 授权成功后，记录该微信用户的基本信息
            $updateInfoFromWx = true;
            $userInfo = array();
            // 用户授权的作用域，使用逗号（,）分隔
            $scopeArr = \explode(',', $arrAccessToken['scope']);
            if (in_array('snsapi_userinfo', $scopeArr) || in_array('snsapi_login', $scopeArr)) {
                // 先判断用户在数据库中是否存在最近一周产生的openid，如果不存在，则再动用网络请求，进行用户信息获取
                $userInfo = $this->modelWeixinopenUser->getUserInfoByIdLastWeek($arrAccessToken['openid'], $this->authorizer_appid, $this->component_appid, $this->now);
                if (true || empty($userInfo)) {
                    $updateInfoFromWx = true;
                    //应用类型 1:公众号 2:小程序 3:订阅号
                    if ($this->app_type == \App\Weixin2\Models\Authorize\Authorizer::APPTYPE_PUB) {
                        $weixin = new \Weixin\Client();
                        $weixin->setSnsAccessToken($arrAccessToken['access_token']);
                        $userInfo = $weixin->getSnsManager()->getSnsUserInfo($arrAccessToken['openid']);
                    }
                    if (isset($userInfo['errcode'])) {
                        throw new \Exception("获取用户信息失败，原因:" . json_encode($userInfo, JSON_UNESCAPED_UNICODE));
                    }
                }
            }
            $userInfo['access_token'] = array_merge($arrAccessToken, $userInfo);
            if (!empty($userInfo)) {
                if (!empty($userInfo['nickname'])) {
                    $arrAccessToken['nickname'] = ($userInfo['nickname']);
                }

                if (!empty($userInfo['headimgurl'])) {
                    $arrAccessToken['headimgurl'] = stripslashes($userInfo['headimgurl']);
                }

                if (!empty($userInfo['unionid'])) {
                    $arrAccessToken['unionid'] = ($userInfo['unionid']);
                }
            }

            $_SESSION[$this->sessionKey] = $arrAccessToken;
            $redirect = $this->getRedirectUrl4Sns($redirect, $arrAccessToken);

            if ($sourceFromUserName !== null && $sourceFromUserName == $arrAccessToken['openid']) {
                $redirect = $this->addUrlParameter($redirect, array(
                    '__self' => true
                ));
            }

            // 调整数据库操作的执行顺序，优化跳转速度
            if ($updateInfoFromWx) {
                if (!empty($userInfo['headimgurl'])) {
                    $userInfo['headimgurl'] = stripslashes($userInfo['headimgurl']);
                }
                $lock = new \iLock($this->lock_key_prefix . $arrAccessToken['openid'] . $this->authorizer_appid . $this->component_appid);
                if (!$lock->lock()) {
                    $this->modelWeixinopenUser->updateUserInfoBySns($arrAccessToken['openid'], $this->authorizer_appid, $this->component_appid, $userInfo);
                }
            }
            $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid'], $this->appConfig['_id']);
            header("location:{$redirect}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 小程序用户登录接口
     *
     * @param Request $request            
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function loginAction()
    {
        // http://wxcrm.eintone.com/weixinopen/api/miniapp/login?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/miniapp/login?appid=4m9QOrJMzAjpx75Y
        try {
            $code = isset($_GET['code']) ? trim($_GET['code']) : '';
            $scene = isset($_GET['scene']) ? trim($_GET['scene']) : '';

            if (empty($code)) {
                return $this->error(40001, "code未指定");
            }

            // 初始化
            $this->doInitializeLogic();

            $sns = new \Weixin\Token\Sns($this->authorizer_appid, $this->authorizerConfig['appsecret']);
            $res = $sns->getJscode2session($code);
            if (isset($res['errcode'])) {
                return $this->error(71002, "微信解密失败");
            }

            $session_key = $res['session_key'];
            $openid = $res['openid'];
            $unionid = isset($res['unionid']) ? $res['unionid'] : "";

            $lock = new \iLock($this->lock_key_prefix . $openid . $this->authorizer_appid . $this->component_appid);
            if ($lock->lock()) {
                return $this->error(50001, "请稍等,系统繁忙!");
            }

            // 追加或修改用户信息
            $userInfo4Session = [
                'openid' => $openid,
                'unionid' => $unionid,
                'qr_scene' => $scene,
                'session_key' => $session_key,
            ];
            $userInfo = $this->modelWeixinopenUser->updateUserInfoBySns($openid, $this->authorizer_appid, $this->component_appid, $userInfo4Session);
            $token = $this->setToken($userInfo, $session_key);
            // 加密
            $sec = $this->encryptParams([
                'openid' => $openid
            ]);

            $ret = [
                'token' => $token,
                'sec' => $sec,
                'userInfo' => [
                    'nickname' => $userInfo['nickname'],
                    'headimgurl' => $userInfo['headimgurl'],
                    'headimgurl4Oss' => $userInfo['oss_headimgurl'],
                ]
            ];
            $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid,  $this->trackingKey, $_SESSION['miniapp_start_time'], microtime(true), "login", $this->appConfig['id']);
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 小程序获取用户信息接口
     */
    public function decryptuserinfoAction()
    {
        // http://wxcrm.eintone.com/weixinopen/api/miniapp/decryptuserinfo?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/miniapp/decryptuserinfo?appid=4m9QOrJMzAjpx75Y
        try {
            $rawData = isset($_GET['rawData']) ? trim($_GET['rawData']) : '';
            $signature = isset($_GET['signature']) ? trim($_GET['signature']) : '';
            $encryptedData = isset($_GET['encryptedData']) ? trim($_GET['encryptedData']) : '';
            $iv = isset($_GET['iv']) ? trim($_GET['iv']) : '';

            if (empty($encryptedData)) {
                return $this->error(40001, "encryptedData未指定");
            }
            if (empty($iv)) {
                return $this->error(40001, "iv未指定");
            }

            // 初始化
            $this->doInitializeLogic();

            $token = $this->getToken();
            if (empty($token)) {
                return $this->error(40001, "token未指定");
            }

            $session_key = $this->getSessionKey($token);
            if (empty($session_key)) {
                return $this->error(41001, "token不存在");
            }

            $localSign = sha1($rawData . $session_key);
            if ($localSign != $signature) {
                return $this->error(41008, "签名错误");
            }

            // 解密处理
            $decRes = $this->decryptAction($this->authorizer_appid, $encryptedData, $iv, $session_key);
            if (empty($decRes)) {
                return $this->error(71002, "微信解密失败");
            }
            $openid = $decRes['result']['openId'];
            $userInfo4Session = [
                'openid' => $decRes['result']['openId'],
                'nickname' => $decRes['result']['nickName'],
                'headimgurl' => $decRes['result']['avatarUrl'],
                'city' => $decRes['result']['city'],
                'country' => $decRes['result']['country'],
                'sex' => $decRes['result']['gender'],
                'province' => $decRes['result']['province'],
                'subscribe_time' => time(),
                'unionid' => isset($decRes['result']['unionId']) ? $decRes['result']['unionId'] : '',
            ];

            $lock = new \iLock($this->lock_key_prefix . $openid . $this->authorizer_appid . $this->component_appid);
            if ($lock->lock()) {
                return $this->error(50001, "请稍等,系统繁忙!");
            }

            // 如果有头像的话
            if (!empty($decRes['result']['avatarUrl'])) {
                // $stream_opts = [
                //     "ssl" => [
                //         "verify_peer" => false,
                //         "verify_peer_name" => false,
                //     ]
                // ];
                // $path = 'weixinheadimgurl/' . $decRes['result']['openId'] . '.jpg';
                // $file = file_get_contents($decRes['result']['avatarUrl'], false, stream_context_create($stream_opts));
                // $ossService = new OssService();
                // $ossService->upload_file_by_content($file, $path);
                // $userInfo4Session['oss_headimgurl'] = $path;
            }

            $userInfo = $this->modelWeixinopenUser->updateUserInfoBySns($openid, $this->authorizer_appid, $this->component_appid, $userInfo4Session);
            $this->setUserInfo($userInfo, $session_key);

            $ret = [
                'nickname' => $userInfo['nickname'],
                'headimgurl' => $userInfo['headimgurl'],
                'headimgurl4Oss' => $userInfo['oss_headimgurl'],
            ];
            $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid,  $this->trackingKey, $_SESSION['miniapp_start_time'], microtime(true), "decryptUserInfo", $this->appConfig['id']);
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }
    /**
     * 小程序手机号解密接口
     */
    public function decryptphoneAction()
    {
        // http://wxcrm.eintone.com/weixinopen/api/miniapp/decryptphone?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/miniapp/decryptphone?appid=4m9QOrJMzAjpx75Y
        try {
            $encryptedData = isset($_GET['encryptedData']) ? trim($_GET['encryptedData']) : '';
            $iv = isset($_GET['iv']) ? trim($_GET['iv']) : '';
            if (empty($encryptedData)) {
                return $this->error(40001, "encryptedData未指定");
            }
            if (empty($iv)) {
                return $this->error(40001, "iv未指定");
            }

            // 初始化
            $this->doInitializeLogic();

            $token = $this->getToken();
            if (empty($token)) {
                return $this->error(40001, "token未指定");
            }

            $session_key = $this->getSessionKey($token);
            if (empty($session_key)) {
                return $this->error(41001, "token不存在");
            }

            // 解密处理
            $decRes = $this->decryptAction($this->authorizer_appid, $encryptedData, $iv, $session_key);
            if (empty($decRes)) {
                return $this->error(71002, "微信解密失败");
            }
            $mobile = $decRes['phoneNumber'];

            // 更新手机号
            $tokenInfo = $this->getTokenInfo($token);
            $openid = $tokenInfo['userInfo']['openid'];
            $userInfo4Session = array(
                'openid' => $openid,
                'mobile' => $mobile
            );
            $userInfo = $this->modelWeixinopenUser->updateUserInfoBySns($openid, $this->authorizer_appid, $this->component_appid, $userInfo4Session);
            $this->setUserInfo($userInfo, $session_key);

            $decRes['mobileEncode'] = $this->encryptParams([
                'mobile' => $mobile,
            ]);
            $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid,  $this->trackingKey, $_SESSION['miniapp_start_time'], microtime(true), "decryptPhone", $this->appConfig['id']);
            return $this->result("OK", $decRes);
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }
    /**
     * 记录小程序扫码接口
     */
    public function scanqrcodeAction()
    {
        // http://wxcrm.eintone.com/weixinopen/api/miniapp/scanqrcode?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/miniapp/scanqrcode?appid=4m9QOrJMzAjpx75Y
        try {
            $scene = isset($_GET['scene']) ? trim($_GET['scene']) : '';
            if (empty($scene) || $scene == 'undefined') {
                return $this->error(40001, "scene未指定");
            }

            // 初始化
            $this->doInitializeLogic();

            $token = $this->getToken();
            if (empty($token)) {
                return $this->error(40001, "token未指定");
            }

            $session_key = $this->getSessionKey($token);
            if (empty($session_key)) {
                return $this->error(41001, "token不存在");
            }

            //获取小程序用户信息
            $tokenInfo = $this->getTokenInfo($token);
            $FromUserName = $tokenInfo['userInfo']['openid'];
            $weixinUserID = $tokenInfo['userInfo']['id'];

            $cache = $this->getDI()->get("cache");
            $cache->save('miniapp_scene_' . $FromUserName, $scene, 7200);

            $modelQrcodeLog = new \App\Weixin2\Models\Miniprogram\Qrcode\Log();
            $modelQrcodeLog->record(
                $this->authorizer_appid,
                $this->component_appid,
                $FromUserName,
                $weixinUserID,
                $scene,
                time()
            );
            return $this->result("OK");
        } catch (\Exception $e) {
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 生成小程序二维码接口
     */
    public function createqrcodeAction()
    {
        // http://wxcrm.eintone.com/weixinopen/api/miniapp/createqrcode?appid=4m9QOrJMzAjpx75Y
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/miniapp/createqrcode?appid=4m9QOrJMzAjpx75Y
        try {
            $scene = isset($_GET['scene']) ? trim($_GET['scene']) : '';
            $path = isset($_GET['path']) ? trim($_GET['path']) : '';
            $channel = isset($_GET['channel']) ? trim($_GET['channel']) : '';
            if (empty($path)) {
                return $this->error(40001, "path未指定");
            }
            if ($scene == 'ms_undefined') {
                return $this->error(40001, "scene是ms_undefined");
            }

            // 初始化
            $this->doInitializeLogic();

            $type = "getwxacodeunlimit";

            $model = new \App\Weixin2\Models\Miniprogram\Qrcode\Qrcocde();
            \DB::beginTransaction();

            switch ($type) {
                case "getwxacode":
                    $data = \App\Components\Weixinopen\Models\Miniprogram\Qrcode\QrcodeModel::firstOrCreate([
                        'path' => $path,
                        'authorizer_appid' => $this->authorizer_appid,
                        'component_appid' => $this->component_appid,
                        'type' => $type
                    ]);
                    break;
                case "getwxacodeunlimit":
                    $data = \App\Components\Weixinopen\Models\Miniprogram\Qrcode\QrcodeModel::firstOrCreate([
                        'pagepath' => $path,
                        'scene' => $scene,
                        'authorizer_appid' => $this->authorizer_appid,
                        'component_appid' => $this->component_appid,
                        'type' => $type
                    ]);
                    break;
                case "createwxaqrcode":
                    $data = \App\Components\Weixinopen\Models\Miniprogram\Qrcode\QrcodeModel::firstOrCreate([
                        'path' => $path,
                        'authorizer_appid' => $this->authorizer_appid,
                        'component_appid' => $this->component_appid,
                        'type' => $type
                    ]);
                    break;
            }

            // 没有生成过二维码的话
            if (empty($data->url)) {
                $name = $this->authorizer_appid . "_" . uniqid();
                // 创建service
                $weixinopenService = new \App\Weixin2\Services\WeixinService($this->authorizer_appid, $this->component_appid);
                $data->url = $weixinopenService->createMiniappQrcode($data->id, 1, $channel, $name);
            } else {
                if (!empty($channel) && empty($data->channel)) {
                    $data->channel = $channel;
                    $data->save();
                }
            }

            \DB::commit();

            $ret = array();
            $ret['qrcode_url'] = $data->url;
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->error(50000, "系统发生错误：" . $e->getMessage());
        }
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        // 应用ID
        $this->appid = $this->getAppId();
        if (empty($this->appid)) {
            throw new \Exception("appid为空");
        }
        $this->appConfig = $this->modelWeixinopenSnsApplication->getInfoByAppid($this->appid);
        if (empty($this->appConfig)) {
            throw new \Exception("appid:{$this->appid}所对应的记录不存在");
        }

        $isValid = $this->modelWeixinopenSnsApplication->checkIsValid($this->appConfig, $this->now);
        if (empty($isValid)) {
            throw new \Exception("appid:{$this->appid}所对应的记录已无效");
        }
        // 第三方平台运用ID
        $this->component_appid = $this->appConfig['component_appid'];
        // if (empty($this->component_appid)) {
        //     throw new \Exception("component_appid为空");
        // }

        // 授权方ID
        $this->authorizer_appid = $this->appConfig['authorizer_appid'];
        if (empty($this->authorizer_appid)) {
            throw new \Exception("authorizer_appid为空");
        }
        $this->authorizerConfig = $this->modelWeixinopenAuthorizer->getInfoByAppid($this->component_appid, $this->authorizer_appid);
        if (empty($this->authorizerConfig)) {
            throw new \Exception("component_appid:{$this->component_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
        }
        //应用类型 1:公众号 2:小程序 3:订阅号
        $this->app_type = intval($this->authorizerConfig['app_type']);
    }

    // 从header或url参数上获取appid
    protected function getAppId()
    {
        // $appid = \Request::header('appid');
        // if (empty($appid)) {
        //     $appid = \Request::query('appid');
        // }
        $appid = isset($_GET['appid']) ? trim($_GET['appid']) : "";
        return $appid;
    }

    //从header或url参数上获取用户授权token
    protected function getToken()
    {
        $headToken = 'token';
        $headAuthorization = 'authorization';

        $token = "";
        // $header = $request->header($headAuthorization);
        // if (\Str::startsWith(strtolower($header), 'bearer')) {
        //     $token = trim(str_ireplace('bearer', '', $header));
        // } else {
        //     $token = $request->query($headToken, '');
        // }
        $token = isset($_GET[$headToken]) ? trim($_GET[$headToken]) : "";
        return $token;
    }

    protected function setToken($userInfo, $session_key, $expires_in = 7200)
    {
        if (empty($userInfo)) {
            return false;
        }

        $rand = createRandCode(16);
        $token = sha1($rand . $this->authorizer_appid . $session_key);
        $this->setUserInfo($userInfo, $token, $expires_in);

        return $token;
    }

    protected function setUserInfo($userInfo, $token, $expires_in = 7200)
    {
        $params = [
            'userInfo' => $userInfo,
        ];
        $cache = $this->getDI()->get("cache");
        $cache->save('miniapp_' . $token, $params, $expires_in);
        return $token;
    }

    protected function getSessionKey($token)
    {
        $tokenInfo = $this->getTokenInfo($token);
        if (empty($tokenInfo) || empty($tokenInfo['userInfo']) || empty($tokenInfo['userInfo']['session_key'])) {
            return '';
        }
        $sessionKey = $tokenInfo['userInfo']['session_key'];
        return $sessionKey;
    }

    protected function getTokenInfo($token)
    {
        $cache = $this->getDI()->get("cache");
        $tokenInfo = $cache->get('miniapp_' . $token);

        if (empty($tokenInfo)) {
            return [];
        }
        return $tokenInfo;
    }

    protected function decryptAction($appID, $encryptedData, $iv, $sessionKey)
    {
        $data = '';
        $pc = new \Weixin\ThirdParty\AesCrypt\WXBizDataCrypt($appID, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);

        if ($errCode == 0) {
            $res = json_decode($data, true);
            if (empty($res)) {
                return false;
            } else {
                return $res;
            }
        } else {
            return false;
        }
    }

    protected function encryptParams($params)
    {
        $key = "Co1UfVho5l8OukwQ";
        $signKey = "&j09JaRiVIWheNgK";
        $encryptService = new \App\Services\EncryptService($key, $signKey);
        $sec = $encryptService->encrypt($params);
        return $sec;
    }
}
