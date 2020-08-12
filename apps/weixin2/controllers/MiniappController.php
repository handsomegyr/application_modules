<?php

namespace App\Weixin2\Controllers;

/**
 * 小程序授权
 */
class MiniappController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 1;

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
                    'openid' => $userInfo['openid'],
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
            $openid = $decRes['openId'];
            $userInfo4Session = [
                'openid' => $decRes['openId'],
                'nickname' => $decRes['nickName'],
                'headimgurl' => $decRes['avatarUrl'],
                'city' => $decRes['city'],
                'country' => $decRes['country'],
                'sex' => $decRes['gender'],
                'province' => $decRes['province'],
                'subscribe_time' => time(),
                'unionid' => isset($decRes['unionId']) ? $decRes['unionId'] : '',
            ];

            $lock = new \iLock($this->lock_key_prefix . $openid . $this->authorizer_appid . $this->component_appid);
            if ($lock->lock()) {
                return $this->error(50001, "请稍等,系统繁忙!");
            }

            // 获取头像
            $tokenInfo = $this->getTokenInfo($token);
            $headimgurl = $tokenInfo['userInfo']['headimgurl'];

            // 如果有头像的话并且头像发生了改变的时候
            if (!empty($decRes['avatarUrl']) && $headimgurl != $decRes['avatarUrl']) {
                $stream_opts = [
                    "ssl" => [
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ]
                ];
                $path = 'weixinheadimgurl/' . $decRes['openId'] . '.jpg';
                $file = file_get_contents($decRes['avatarUrl'], false, stream_context_create($stream_opts));
                // 可以将头像内容上传到OSS上或本地地址
                // $ossService = new OssService();
                // $ossService->upload_file_by_content($file, $path);
                $r = file_put_contents(APP_PATH . '/public/' . $path, $file); // 返回的是字节数
                if (!$r) {
                    throw new \Exception('保存文件失败');
                }
                $userInfo4Session['oss_headimgurl'] = $path;
            }

            $userInfo = $this->modelWeixinopenUser->updateUserInfoBySns($openid, $this->authorizer_appid, $this->component_appid, $userInfo4Session);
            $this->setUserInfo($userInfo, $session_key);

            $ret = [
                'openid' => $userInfo['openid'],
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

            $tokenInfo = $this->getTokenInfo($token);
            $openid = $tokenInfo['userInfo']['openid'];

            $lock = new \iLock($this->lock_key_prefix . $openid . $this->authorizer_appid . $this->component_appid);
            if ($lock->lock()) {
                return $this->error(50001, "请稍等,系统繁忙!");
            }

            // 解密处理
            $decRes = $this->decryptAction($this->authorizer_appid, $encryptedData, $iv, $session_key);
            if (empty($decRes)) {
                return $this->error(71002, "微信解密失败");
            }
            $mobile = $decRes['phoneNumber'];

            // 更新手机号
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

            switch ($type) {
                case "getwxacode":
                    $query = [
                        'path' => $path,
                        'authorizer_appid' => $this->authorizer_appid,
                        'component_appid' => $this->component_appid,
                        'type' => $type
                    ];
                    break;
                case "getwxacodeunlimit":
                    $query = [
                        'pagepath' => $path,
                        'scene' => $scene,
                        'authorizer_appid' => $this->authorizer_appid,
                        'component_appid' => $this->component_appid,
                        'type' => $type
                    ];
                    break;
                case "createwxaqrcode":
                    $query = [
                        'path' => $path,
                        'authorizer_appid' => $this->authorizer_appid,
                        'component_appid' => $this->component_appid,
                        'type' => $type
                    ];
                    break;
            }

            $lock = new \iLock($this->lock_key_prefix . md5(\json_encode($query)) . $this->authorizer_appid . $this->component_appid);
            if ($lock->lock()) {
                return $this->error(50001, "请稍等,系统繁忙!");
            }

            $modelQrcode = new \App\Weixin2\Models\Miniprogram\Qrcode\Qrcode();
            $modelQrcode->begin();

            // 查找数据
            $data = $modelQrcode->findOne($query);
            // 如果不存在就追加
            if (empty($data)) {
                $data = $modelQrcode->insert($query);
            }

            // 没有生成过二维码的话
            if (empty($data['url'])) {
                $name = $this->authorizer_appid . "_" . uniqid();
                // 创建service
                $weixinopenService = new \App\Weixin2\Services\WeixinService($this->authorizer_appid, $this->component_appid);
                $data['url'] = $weixinopenService->createMiniappQrcode($data->id, 1, $channel, $name);
            } else {
                if (!empty($channel) && empty($data['channel'])) {
                    $modelQrcode->update(array('_id' => $data['_id']), array('$set' => array('channel' => $channel)));
                }
            }

            $modelQrcode->commit();

            $ret = array();
            $ret['qrcode_url'] = $data['url'];
            return $this->result("OK", $ret);
        } catch (\Exception $e) {
            $modelQrcode->rollback();
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
