<?php
namespace App\Weixin\Controllers;

use App\Weixin\Models\Source;
use App\Weixin\Models\Keyword;
use App\Weixin\Models\Reply;
use App\Weixin\Models\Application;
use App\Weixin\Models\User;
use App\Weixin\Models\NotKeyword;
use App\Weixin\Models\Menu;
use App\Weixin\Models\ConditionalMenu;
use App\Weixin\Models\ConditionalMenuMatchRule;
use App\Weixin\Models\Qrcode;
use App\Weixin\Models\Scene;
use App\Weixin\Models\ComponentApplication;
use App\Weixin\Models\ScriptTracking;
use function GuzzleHttp\json_encode;

class IndexController extends ControllerBase
{

    protected $_source;

    protected $_sourceDatas;

    protected $_keyword;

    protected $_reply;

    protected $_app;

    protected $_accessToken;

    protected $_user;

    protected $_not_keyword;

    protected $_menu;

    protected $_conditional_menu;
	
    protected $_conditional_menu_match_rule;

    protected $_weixin;

    protected $_qrcode;

    protected $_scene;

    protected $_tracking;

    protected $_appConfig;

    protected $appid;

    protected $_config;

    protected $isNeedDecryptAndEncrypt = false;

    public function initialize()
    {
        parent::initialize();
        
        try {
            $this->_source = new Source();
            $this->_keyword = new Keyword();
            $this->_not_keyword = new NotKeyword();
            $this->_reply = new Reply();
            $this->_reply->HOST_URL = $this->webUrl;
            $this->_user = new User();
            $this->_menu = new Menu();
            $this->_conditional_menu = new ConditionalMenu();
            $this->_conditional_menu_match_rule = new ConditionalMenuMatchRule();
            $this->_qrcode = new Qrcode();
            $this->_scene = new Scene();
            $this->_tracking = new ScriptTracking();
            
            $this->_config = $this->getDI()->get('config');
            $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : $this->_config['weixin']['appid'];
            
            $this->doInitializeLogic();
            
            $this->_app = new Application();
            $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
            
            $this->_weixin = new \Weixin\Client();
            if (! empty($this->_appConfig['access_token'])) {
                $this->_weixin->setAccessToken($this->_appConfig['access_token']);
            }
        } catch (\Exception $e) {
            if (! in_array($this->router->getActionName(), array(
                "getsettings",
                "getjssdkinfo",
                "getaccesstoken",
                "getjsapiticket"
            ))) {
                var_dump($e);
            }
        }
    }

    public function indexAction()
    {
        die('index');
    }

    /**
     * 处理微信的回调数据
     *
     * @return boolean
     */
    public function callbackAction()
    {
        try {
            /**
             * ==================================================================================
             * ====================================以下逻辑请勿修改===================================
             * ==================================================================================
             */
            $onlyRevieve = false;
            $__DEBUG__ = isset($_GET['__DEBUG__']) ? trim(strtolower($_GET['__DEBUG__'])) : false;
            
            $AESInfo = array();
            $AESInfo['timestamp'] = isset($_GET['timestamp']) ? trim(strtolower($_GET['timestamp'])) : '';
            $AESInfo['nonce'] = isset($_GET['nonce']) ? $_GET['nonce'] : '';
            $AESInfo['encrypt_type'] = isset($_GET['encrypt_type']) ? $_GET['encrypt_type'] : '';
            $AESInfo['msg_signature'] = isset($_GET['msg_signature']) ? $_GET['msg_signature'] : '';
            $AESInfo['api'] = 'callback';
            $AESInfo['appid'] = isset($_GET['appid']) ? trim(($_GET['appid'])) : '';
            $AESInfo['appid2'] = isset($_GET['appid2']) ? trim(($_GET['appid2'])) : '';
            $this->_sourceDatas['AESInfo'] = $AESInfo;
            
            $verifyToken = isset($this->_appConfig['verify_token']) ? $this->_appConfig['verify_token'] : '';
            if (empty($verifyToken)) {
                throw new \Exception('application verify_token is null');
            }
            $this->_sourceDatas['AESInfo']['verify_token'] = $verifyToken;
            
            // 合法性校验
            $this->_weixin->verify($verifyToken);
            
            if (! $__DEBUG__) {
                if (! $this->_weixin->checkSignature($verifyToken)) {
                    $debug = debugVar($_GET, $this->_weixin->getSignature());
                    throw new \Exception('签名错误' . $debug);
                }
            }
            
            // 签名正确，将接受到的xml转化为数组数据并记录数据
            $datas = $this->getDataFromWeixinServer();
            
            // $this->_sourceDatas = $datas;
            foreach ($datas as $dtkey => $dtvalue) {
                $this->_sourceDatas[$dtkey] = $dtvalue;
            }
            $this->_sourceDatas['response'] = 'success';
            
            // 调试接口信息
            if ($__DEBUG__) {
                $datas = $this->_app->debug($__DEBUG__);
            }
            // 开始处理相关的业务逻辑
            $content = isset($datas['Content']) ? strtolower(trim($datas['Content'])) : '';
            
            $FromUserName = isset($datas['FromUserName']) ? trim($datas['FromUserName']) : '';
            $__TIME_STAMP__ = time();
            $__SIGN_KEY__ = $this->_app->getSignKey($FromUserName, $this->_appConfig['secretKey'], $__TIME_STAMP__);
            // Zend_Registry::set('__FROM_USER_NAME__', $FromUserName);
            // Zend_Registry::set('__TIME_STAMP__', $__TIME_STAMP__);
            // Zend_Registry::set('__SIGN_KEY__', $__SIGN_KEY__);
            $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
            $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
            $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
            $EventKey = isset($datas['EventKey']) ? trim($datas['EventKey']) : '';
            $MediaId = isset($datas['MediaId']) ? trim($datas['MediaId']) : '';
            $Ticket = isset($datas['Ticket']) ? trim($datas['Ticket']) : '';
            $MsgId = isset($datas['MsgId']) ? trim($datas['MsgId']) : '';
            $CreateTime = isset($datas['CreateTime']) ? intval($datas['CreateTime']) : time();
            
            // 关于重试的消息排重，有msgid的消息推荐使用msgid排重。事件类型消息推荐使用FromUserName + CreateTime 排重。
            if (! empty($MsgId)) {
                $uniqueKey = $MsgId . "-" . $this->_appConfig['appid'];
            } else {
                $uniqueKey = $FromUserName . "-" . $CreateTime . "-" . $this->_appConfig['appid'];
            }
            if (! empty($uniqueKey)) {
                $objLock = new \iLock(md5($uniqueKey));
                if ($objLock->lock()) {
                    echo "success";
                    return true;
                }
            }
            // 设定来源和目标用户的openid
            $this->_weixin->setFromAndTo($FromUserName, $ToUserName);
            
            // 获取微信用户的个人信息
            if (! empty($this->_appConfig['access_token'])) {
                $this->_user->setWeixinInstance($this->_weixin);
                $this->_user->updateUserInfoByAction($FromUserName);
            }
            
            // 为回复的Model装载weixin对象
            $this->_reply->setWeixinInstance($this->_weixin);
            /**
             * ==================================================================================
             * ====================================以上逻辑请勿修改===================================
             * ==================================================================================
             */
            
            // 一般的业务逻辑开始
            $datas = $this->doCommonLogic($datas);
            // 一般的业务逻辑结束
            
            // 不同项目特定的业务逻辑开始
            $objService = \App\Weixin\Services\Base::getServiceObject();
            $objService->_weixin = $this->_weixin;
            if (! isset($datas['__UNDO_SPECIAL_'])) {
                $datas = $objService->doSpecialLogic($datas);
            }
            // 不同项目特定的业务逻辑结束
            
            $content = $datas['content_process'];
            $response = $datas['response'];
            
            /**
             * ==================================================================================
             * ====================================以下逻辑请勿修改===================================
             * ==================================================================================
             */
            if ($onlyRevieve)
                return false;
            
            if ($__DEBUG__) {
                print_r($content);
            }
            
            if ($content == 'debug') {
                $response = $this->_weixin->getMsgManager()
                    ->getReplySender()
                    ->replyText(debugVar($datas));
            }
            if (empty($response)) {
                $response = followUrl($this->answer($content), array(
                    'FromUserName' => $FromUserName,
                    'timestamp' => $__TIME_STAMP__,
                    'signkey' => $__SIGN_KEY__
                ));
            }
            // 输出响应结果
            $response = $this->responseToWeixinServer($response);
            echo $response;
            
            // 以下部分执行的操作，不影响执行速度，但是也将无法输出到返回结果中
            if (! $__DEBUG__) {
                fastcgi_finish_request();
            }
            
            $this->_sourceDatas['response'] = $response;
            
            /**
             * ==================================================================================
             * ====================================以上逻辑请勿修改===================================
             * ==================================================================================
             */
            
            // 将一些执行很慢的逻辑，放在这里执行，提高微信的响应速度开始
            $objService->processAfter($datas);
            // 将一些执行很慢的逻辑，放在这里执行，提高微信的响应速度结束
            
            return true;
        } catch (\Exception $e) {
            // 如果脚本执行中发现异常，则记录返回的异常信息
            $this->_sourceDatas['response'] = exceptionMsg($e);
            return false;
        }
    }

    /**
     * 同步菜单选项的Hook
     */
    public function syncmenuAction()
    {
        try {
            $menus = $this->_menu->buildMenu();
            if (! empty($menus)) {
                var_dump($this->_weixin->getMenuManager()->create($menus));
            } else {
                var_dump($this->_weixin->getMenuManager()->delete(array()));
            }
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }
    }

    /**
     * 同步个性化菜单选项的Hook
     */
    public function syncconditionalmenuAction()
    {
        try {
            $matchRuleList = $this->_conditional_menu->getList4MatchRule();
            if (! empty($matchRuleList)) {
                foreach ($matchRuleList as $matchRule) {
					$ruleInfo = $this->_conditional_menu_match_rule->getInfoById($matchRule['matchrule']);
					if(empty($ruleInfo)){
						continue;
					}
					$matchRule['ruleInfo'] = $ruleInfo;
                    // 如果原来的有值的话就删除
                    if (! empty($matchRule['menuid'])) {
                        $ret = $this->_weixin->getMenuManager()->delconditional($matchRule['menuid']);
                        if (! empty($ret['errcode'])) {
                            throw new \Exception( $ret['errmsg'], $ret['errcode']);
                        }
                    }
                    
                    // 增加菜单
                    $menusWithMatchrule = $this->_conditional_menu->buildMenusWithMatchrule($matchRule);
                    $ret = $this->_weixin->getMenuManager()->addconditional($menusWithMatchrule);
                    if (! empty($ret['errcode'])) {
                        throw new \Exception($ret['errmsg'],$ret['errcode'] );
                    }
                    $this->_conditional_menu->recordMenuId($matchRule, $ret['menuid']);
                }
            }
            return true;
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }
    }

    /**
     * 创建二维码ticket的Hook
     */
    public function createqrcodeAction()
    {
        try {
            $scenes = $this->_scene->getAll();
            foreach ($scenes as $scene) {
                if (empty($scene['is_temporary']) && ! empty($scene['is_created'])) { // 如果是永久并且已生成的话
                    continue;
                }
                if (! empty($scene['is_temporary']) && ! empty($scene['is_created']) && ($scene['ticket_time']->sec + $scene['expire_seconds']) > (time())) { // 如果是临时并且已生成并且没有过期
                    continue;
                }
                $ticketInfo = $this->_weixin->getQrcodeManager()->create($scene['sence_id'], ! empty($scene['is_temporary']) ? $scene['is_temporary'] : false, ! empty($scene['expire_seconds']) ? $scene['expire_seconds'] : 0);
                $ticket = urlencode($ticketInfo['ticket']);
                $ticket = $this->_weixin->getQrcodeManager()->getQrcodeUrl($ticket);
                $this->_scene->recordTicket($scene, $ticket, $ticketInfo['url']);
            }
            return true;
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }
    }

    /**
     * 发送模板消息
     */
    public function sendtemplatemsgAction()
    {
        try {
            $template_id = "5UWzYiEm8AsW97T9uZbX-zUGporKDGIfDFf-wUi8OD4";
            $url = "http://www.baidu.com";
            $topcolor = "#FF0000";
            $touser = "oFEX-joe9BYUKqluMFux104CxRNE";
            $data = array();
            $data['first'] = array(
                "value" => "您好，您已成功消费。",
                "color" => "#0A0A0A"
            );
            $data['date'] = array(
                "value" => "10月22日12时37分",
                "color" => "#0A0A0A"
            );
            $data['tradetype'] = array(
                "value" => "银证转账",
                "color" => "#0A0A0A"
            );
            $data['tradenum'] = array(
                "value" => "1000.21元",
                "color" => "#0A0A0A"
            );
            $data['traderemain'] = array(
                "value" => "1000000.19元",
                "color" => "#0A0A0A"
            );
            $data['remark'] = array(
                "value" => "回复“持仓”即可查看您目前持有资产，回复“投顾”即可查看您专属的投资顾近期的投资建议。",
                "color" => "#0A0A0A"
            );
            
            $this->_weixin->getMsgManager()
                ->getTemplateSender()
                ->send($touser, $template_id, $url, $topcolor, $data);
            return true;
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }
    }

    /**
     * 获取所有微信设置信息的接口
     */
    public function getsettingsAction()
    {
        try {
            if (empty($this->_appConfig)) {
                $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
            }
            if (empty($this->_appConfig)) {
                echo $this->error("-1", "获取失败");
                return false;
            } else {
                $rs['weixin_name'] = $this->_appConfig['weixin_name'];
                $rs['weixin_id'] = $this->_appConfig['weixin_id'];
                $rs['appid'] = $this->_appConfig['appid'];
                $rs['access_token'] = $this->_appConfig['access_token'];
                $rs['access_token_expire'] = $this->_appConfig['access_token_expire'];
                $rs['jsapi_ticket'] = $this->_appConfig['jsapi_ticket'];
                $rs['jsapi_ticket_expire'] = $this->_appConfig['jsapi_ticket_expire'];
                echo $this->result("OK", $rs);
            }
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取所有微信wssdk设置信息的接口
     */
    public function getjssdkinfoAction()
    {
        try {
            // 如果不是跨域请求的话
            $jsonpcallback = trim($this->get('jsonpcallback'));
            if (empty($jsonpcallback)) {
                $isPost = $this->getRequest()->isPost();
                if (empty($isPost)) {
                    echo $this->error("-3", "请求方式不正确");
                    return false;
                }
            }
            $url = $this->get('url', '');
            if (empty($url)) {
                echo $this->error("-1", "参数URL的为空");
                return false;
            }
            $url = urldecode($url);
            
            if (empty($this->_appConfig)) {
                $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
            }
            if (empty($this->_appConfig) || empty($this->_appConfig['jsapi_ticket'])) {
                echo $this->error("-2", "jsapi_ticket获取失败");
                return false;
            } else {
                $objJssdk = new \Weixin\Jssdk();
                $objJssdk->setAppId($this->_appConfig['appid']);
                $objJssdk->setAppSecret($this->_appConfig['secret']);
                $objJssdk->setAccessToken($this->_appConfig['access_token']);
                $rs = $objJssdk->getSignPackage($url, $this->_appConfig['jsapi_ticket']);
                $rs['access_token'] = $this->_appConfig['access_token'];
                $rs['jsapi_ticket'] = $this->_appConfig['jsapi_ticket'];
                $rs['expire_time'] = $this->_appConfig['expire_time'];
                echo $this->result("OK", $rs);
                return true;
            }
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取微信accesstoken信息的接口
     */
    public function getaccesstokenAction()
    {
        try {
            if (empty($this->_appConfig)) {
                $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
            }
            if (empty($this->_appConfig)) {
                echo $this->error("-1", "获取失败");
                return false;
            } else {
                $rs = array();
                $rs['access_token'] = $this->_appConfig['access_token'];
                $rs['expire_time'] = $this->_appConfig['access_token_expire']->sec;
                echo $this->result("OK", $rs);
                return true;
            }
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取微信jsapiticket信息的接口
     */
    public function getjsapiticketAction()
    {
        try {
            if (empty($this->_appConfig)) {
                $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
            }
            if (empty($this->_appConfig)) {
                echo $this->error("-1", "获取失败");
                return false;
            } else {
                $rs = array();
                $rs['jsapi_ticket'] = $this->_appConfig['jsapi_ticket'];
                $rs['expire_time'] = $this->_appConfig['jsapi_ticket_expire']->sec;
                $rs['appid'] = $this->_appConfig['appid'];
                echo $this->result("OK", $rs);
                return true;
            }
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取微信服务器IP地址的接口
     */
    public function getcallbackipAction()
    {
        try {
            if (empty($this->_appConfig)) {
                $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
            }
            if (empty($this->_appConfig)) {
                echo $this->error("-1", "获取失败");
                return false;
            } else {
                $rs = array();
                $rs = $this->_weixin->getIpManager()->getcallbackip();
                echo $this->result("OK", $rs);
                return true;
            }
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取微信服务号组ID的接口
     */
    public function getallgroupAction()
    {
        try {
            if (empty($this->_appConfig)) {
                $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
            }
            if (empty($this->_appConfig)) {
                echo $this->error("-1", "获取失败");
                return false;
            } else {
                $rs = array();
                $rs = $this->_weixin->getGroupManager()->get();
                echo $this->result("OK", $rs);
                return true;
            }
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 获取微信服务号组ID的接口
     */
    public function getuserinfoAction()
    {
        try {
            $FromUserName = $this->get('FromUserName', '');
            
            if (empty($this->_appConfig)) {
                $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
            }
            if (empty($this->_appConfig)) {
                echo $this->error("-1", "获取失败");
                return false;
            } else {
                $rs = array();
                $userInfo = $this->_weixin->getUserManager()->getUserInfo($FromUserName);
                echo $this->result("OK", $userInfo);
                return true;
            }
        } catch (\Exception $e) {
            echo $this->error($e->getCode(), $e->getMessage());
            return false;
        }
    }

    /**
     * 创建/更新公众平台组信息并更新到场景管理的微信组ID数组里
     */
    public function updategroupsAction()
    {
        try {
            if (empty($this->_appConfig)) {
                $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
            }
            
            if (empty($this->_appConfig)) {
                echo $this->error("-1", "获取失败");
                return false;
            } else {
                $rs = array();
                $rs = $this->_weixin->getGroupManager()->get();
                $groups = new Weixin_Model_Groups();
                
                foreach ($rs['groups'] as $k => $v) {
                    $groups_id = $name = $count = '';
                    $groups_id = $v['id'];
                    $name = $v['name'];
                    $count = $v['count'];
                    $groupsInfo = $groups->getGroupsInfo($groups_id);
                    if (empty($groupsInfo)) {
                        $groups->record($groups_id, $name, $count);
                    } else {
                        if ($count != $groupsInfo['count']) {
                            $groups->updateGroupsInfo($groupsInfo['_id'], $groups_id, $name, $count);
                        }
                    }
                }
                echo $this->result("OK", $rs);
                return true;
            }
            
            return true;
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }
    }

    /**
     * 匹配文本并进行自动回复
     *
     * @param string $content            
     * @return boolean
     */
    protected function answer($content)
    {
        $match = $this->_keyword->matchKeyWord($content);
        if (empty($match)) {
            $this->_not_keyword->record($content);
            $match = $this->_keyword->matchKeyWord('默认回复');
        }
        return $this->_reply->answer($match);
    }

    /**
     * 处理一般的逻辑
     *
     * @param array $datas            
     * @return array
     */
    protected function doCommonLogic(array $datas)
    {
        $content = isset($datas['Content']) ? strtolower(trim($datas['Content'])) : '';
        $ToUserName = isset($datas['ToUserName']) ? trim($datas['ToUserName']) : '';
        $MsgType = isset($datas['MsgType']) ? trim($datas['MsgType']) : '';
        $Event = isset($datas['Event']) ? trim($datas['Event']) : '';
        $EventKey = isset($datas['EventKey']) ? trim($datas['EventKey']) : '';
        $MediaId = isset($datas['MediaId']) ? trim($datas['MediaId']) : '';
        $Ticket = isset($datas['Ticket']) ? trim($datas['Ticket']) : '';
        $response = isset($datas['response']) ? strtolower(trim($datas['response'])) : '';
        
        // 转化为关键词方式，表示关注
        if ($MsgType == 'event') { // 接收事件推送
            if ($Event == 'subscribe') { // 关注事件
                /**
                 */
                // EventKey 事件KEY值，qrscene_为前缀，后面为二维码的参数值
                
                // Ticket 二维码的ticket，可用来换取二维码图片
                if (! empty($Ticket) && ! empty($EventKey)) { // 扫描带参数二维码事件 用户未关注时，进行关注后的事件推送
                                                              
                    // var_dump($FromUserName, $Event, $EventKey, $Ticket);
                    $this->_qrcode->record($FromUserName, $Event, $EventKey, $Ticket);
                    // 不同项目特定的业务逻辑开始
                    $sence_id = intval(str_ireplace('qrscene_', '', $EventKey));
                    
                    // 二维码场景管理
                    if ($sence_id > 0) {
                        $content = "扫描二维码{$sence_id}";
                    }
                    // 不同项目特定的业务逻辑结束
                }
                
                // 扫描二维码送优惠券
                if (empty($content)) {
                    $content = '首访回复';
                }
            } elseif ($Event == 'SCAN') { // 扫描带参数二维码事件 用户已关注时的事件推送
                $this->_qrcode->record($FromUserName, $Event, $EventKey, $Ticket);
                /**
                 */
                // $onlyRevieve = true;
                // EventKey 事件KEY值，是一个32位无符号整数
                // Ticket 二维码的ticket，可用来换取二维码图片
                // 不同项目特定的业务逻辑开始
                $content = "扫描二维码{$EventKey}";
                // 不同项目特定的业务逻辑结束
            } elseif ($Event == 'unsubscribe') { // 取消关注事件
            /**
             */
                // 不同项目特定的业务逻辑开始
                // 不同项目特定的业务逻辑结束
            } elseif ($Event == 'LOCATION') { // 上报地理位置事件
                /**
                 */
                // Latitude 地理位置纬度
                // Longitude 地理位置经度
                // Precision 地理位置精度
                $Latitude = isset($datas['Latitude']) ? floatval($datas['Latitude']) : 0;
                $Longitude = isset($datas['Longitude']) ? floatval($datas['Longitude']) : 0;
                $Precision = isset($datas['Precision']) ? floatval($datas['Precision']) : 0;
                $onlyRevieve = true;
                // 不同项目特定的业务逻辑开始
                // 不同项目特定的业务逻辑结束
            } elseif ($Event == 'CLICK') { // 自定义菜单事件推送
                                           
                // 相对点击事件做特别处理，请在这里，并删除$content = $EventKey;
                $content = $EventKey;
            } elseif ($Event == 'scancode_push') { // 自定义菜单事件推送 -scancode_push：扫码推事件的事件推送
                                                   
                // 相对点击事件做特别处理，请在这里，并删除$content = $EventKey;
                $content = $EventKey;
                /**
                 * <ScanCodeInfo><ScanType><![CDATA[qrcode]]></ScanType>
                 * <ScanResult><![CDATA[1]]></ScanResult>
                 * </ScanCodeInfo>
                 */
                // ScanCodeInfo 扫描信息
                // ScanType 扫描类型，一般是qrcode
                // ScanResult 扫描结果，即二维码对应的字符串信息
                $ScanType = isset($datas['ScanCodeInfo']['ScanType']) ? trim($datas['ScanCodeInfo']['ScanType']) : "";
                $ScanResult = isset($datas['ScanCodeInfo']['ScanResult']) ? trim($datas['ScanCodeInfo']['ScanResult']) : "";
            } elseif ($Event == 'scancode_waitmsg') { // 自定义菜单事件推送 -scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框的事件推送
                                                      
                // 相对点击事件做特别处理，请在这里，并删除$content = $EventKey;
                $content = $EventKey;
                /**
                 * <ScanCodeInfo><ScanType><![CDATA[qrcode]]></ScanType>
                 * <ScanResult><![CDATA[1]]></ScanResult>
                 * </ScanCodeInfo>
                 */
                
                // ScanCodeInfo 扫描信息
                // ScanType 扫描类型，一般是qrcode
                // ScanResult 扫描结果，即二维码对应的字符串信息
                $ScanType = isset($datas['ScanCodeInfo']['ScanType']) ? trim($datas['ScanCodeInfo']['ScanType']) : "";
                $ScanResult = isset($datas['ScanCodeInfo']['ScanResult']) ? trim($datas['ScanCodeInfo']['ScanResult']) : "";
            } elseif ($Event == 'pic_sysphoto') { // 自定义菜单事件推送 -pic_sysphoto：弹出系统拍照发图的事件推送
                                                  
                // 相对点击事件做特别处理，请在这里，并删除$content = $EventKey;
                $content = $EventKey;
                
                /**
                 * <SendPicsInfo>
                 * <Count>1</Count>
                 * <PicList>
                 * <item>
                 * <PicMd5Sum><![CDATA[1b5f7c23b5bf75682a53e7b6d163e185]]></PicMd5Sum>
                 * </item>
                 * </PicList>
                 * </SendPicsInfo>
                 */
                
                // SendPicsInfo 发送的图片信息
                // Count 发送的图片数量
                // PicList 图片列表
                // PicMd5Sum 图片的MD5值，开发者若需要，可用于验证接收到图片
                $Count = isset($datas['SendPicsInfo']['Count']) ? trim($datas['SendPicsInfo']['Count']) : 0;
                $PicList = isset($datas['SendPicsInfo']['PicList']) ? trim($datas['SendPicsInfo']['PicList']) : "";
            } elseif ($Event == 'pic_photo_or_album') { // 自定义菜单事件推送 -pic_photo_or_album：弹出拍照或者相册发图的事件推送
                                                        
                // 相对点击事件做特别处理，请在这里，并删除$content = $EventKey;
                $content = $EventKey;
                
                /**
                 * <SendPicsInfo>
                 * <Count>1</Count>
                 * <PicList>
                 * <item>
                 * <PicMd5Sum><![CDATA[1b5f7c23b5bf75682a53e7b6d163e185]]></PicMd5Sum>
                 * </item>
                 * </PicList>
                 * </SendPicsInfo>
                 */
                
                // SendPicsInfo 发送的图片信息
                // Count 发送的图片数量
                // PicList 图片列表
                // PicMd5Sum 图片的MD5值，开发者若需要，可用于验证接收到图片
                $Count = isset($datas['SendPicsInfo']['Count']) ? trim($datas['SendPicsInfo']['Count']) : 0;
                $PicList = isset($datas['SendPicsInfo']['PicList']) ? trim($datas['SendPicsInfo']['PicList']) : "";
            } elseif ($Event == 'pic_weixin') { // 自定义菜单事件推送 -pic_weixin：弹出微信相册发图器的事件推送
                                                
                // 相对点击事件做特别处理，请在这里，并删除$content = $EventKey;
                $content = $EventKey;
                
                /**
                 * <SendPicsInfo>
                 * <Count>1</Count>
                 * <PicList>
                 * <item>
                 * <PicMd5Sum><![CDATA[1b5f7c23b5bf75682a53e7b6d163e185]]></PicMd5Sum>
                 * </item>
                 * </PicList>
                 * </SendPicsInfo>
                 */
                
                // SendPicsInfo 发送的图片信息
                // Count 发送的图片数量
                // PicList 图片列表
                // PicMd5Sum 图片的MD5值，开发者若需要，可用于验证接收到图片
                $Count = isset($datas['SendPicsInfo']['Count']) ? trim($datas['SendPicsInfo']['Count']) : 0;
                $PicList = isset($datas['SendPicsInfo']['PicList']) ? trim($datas['SendPicsInfo']['PicList']) : "";
            } elseif ($Event == 'location_select') { // 自定义菜单事件推送 -location_select：弹出地理位置选择器的事件推送
                                                     
                // 相对点击事件做特别处理，请在这里，并删除$content = $EventKey;
                $content = $EventKey;
                
                /**
                 * <SendLocationInfo>
                 * <Location_X><![CDATA[23]]></Location_X>
                 * <Location_Y><![CDATA[113]]></Location_Y>
                 * <Scale><![CDATA[15]]></Scale>
                 * <Label><![CDATA[ 广州市海珠区客村艺苑路 106号]]></Label>
                 * <Poiname><![CDATA[]]></Poiname>
                 * </SendLocationInfo>
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
            } elseif ($Event == 'MASSSENDJOBFINISH') { // 事件推送群发结果
                
                /**
                 */
                // Status 群发的结构，为“send success”或“send fail”或“err(num)”。但send success时，也有可能因用户拒收公众号的消息、系统错误等原因造成少量用户接收失败。err(num)是审核失败的具体原因，可能的情况如下：err(10001), //涉嫌广告 err(20001), //涉嫌政治 err(20004), //涉嫌社会 err(20002), //涉嫌色情 err(20006), //涉嫌违法犯罪 err(20008), //涉嫌欺诈 err(20013), //涉嫌版权 err(22000), //涉嫌互推(互相宣传) err(21000), //涉嫌其他
                // TotalCount group_id下粉丝数；或者openid_list中的粉丝数
                // FilterCount 过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数，原则上，FilterCount = SentCount + ErrorCount
                // SentCount 发送成功的粉丝数
                // ErrorCount 发送失败的粉丝数
                
                $Status = isset($datas['Status']) ? trim($datas['Status']) : '';
                $TotalCount = isset($datas['TotalCount']) ? intval($datas['TotalCount']) : 0;
                $FilterCount = isset($datas['FilterCount']) ? intval($datas['FilterCount']) : 0;
                $SentCount = isset($datas['SentCount']) ? intval($datas['SentCount']) : 0;
                $ErrorCount = isset($datas['ErrorCount']) ? intval($datas['ErrorCount']) : 0;
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
            } elseif (in_array($Event, array(
                'qualification_verify_success', // 资质认证成功
                'qualification_verify_fail', // 资质认证失败
                'naming_verify_success', // 名称认证成功
                'naming_verify_fail', // 名称认证失败
                'annual_renew', // 年审通知
                'verify_expired'
            ))) // 认证过期失效通知

            { // 微信认证事件推送
                
                /**
                 */
                
                // ExpiredTime 有效期 (整形)，指的是时间戳
                // FailTime 失败发生时间 (整形)，时间戳
                // FailReason 认证失败的原因
                
                $ExpiredTime = isset($datas['ExpiredTime']) ? intval($datas['ExpiredTime']) : 0;
                $FailTime = isset($datas['FailTime']) ? intval($datas['FailTime']) : 0;
                $FailReason = isset($datas['FailReason']) ? trim($datas['FailReason']) : '';
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
            } else {
                $response = "success";
            }
        }
        
        // 语音逻辑开始
        if ($MsgType == 'voice') { // 接收普通消息----语音消息 或者接收语音识别结果
            /**
             */
            // MediaID 语音消息媒体id，可以调用多媒体文件下载接口拉取该媒体
            // Format 语音格式：amr
            // Recognition 语音识别结果，UTF8编码
            $Recognition = isset($datas['Recognition']) ? trim($datas['Recognition']) : '';
            // 不同项目特定的业务逻辑开始
            // 不同项目特定的业务逻辑结束
            $content = '默认语音回复';
        }
        // 语音逻辑结束
        
        // 图片逻辑开始
        if ($MsgType == 'image') { // 接收普通消息----图片消息
            /**
             */
            // PicUrl 图片链接
            // MediaId 图片消息媒体id，可以调用多媒体文件下载接口拉取数据。
            $PicUrl = isset($datas['PicUrl']) ? trim($datas['PicUrl']) : '';
            
            // 使用闭包，提高相应速度
            $content = '默认图片回复';
        }
        // 图片逻辑结束
        
        // 不同项目特定的业务逻辑开始
        if ($MsgType == 'text') { // 接收普通消息----文本消息
        }
        // 不同项目特定的业务逻辑结束
        
        // 不同项目特定的业务逻辑开始
        if ($MsgType == 'video' || $MsgType == 'shortvideo') { // 接收普通消息----视频消息或小视频消息
            /**
             */
            // MediaId 视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
            // ThumbMediaId 视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
            $ThumbMediaId = isset($datas['ThumbMediaId']) ? trim($datas['ThumbMediaId']) : '';
        }
        // 不同项目特定的业务逻辑结束
        
        // 处理地理位置信息开始
        if ($MsgType == 'location') { // 接收普通消息----地理位置消息
            /**
             */
            // Location_X 地理位置维度
            // Location_Y 地理位置经度
            // Scale 地图缩放大小
            $Location_X = isset($datas['Location_X']) ? trim($datas['Location_X']) : 0;
            $Location_Y = isset($datas['Location_Y']) ? trim($datas['Location_Y']) : 0;
            $Scale = isset($datas['Scale']) ? trim($datas['Scale']) : 0;
        }
        
        // 不同项目特定的业务逻辑开始
        if ($MsgType == 'link') { // 接收普通消息----链接消息
            /**
             */
            // Title 消息标题
            // Description 消息描述
            // Url 消息链接
            $Title = isset($datas['Title']) ? trim($datas['Title']) : '';
            $Description = isset($datas['Description']) ? trim($datas['Description']) : '';
            $Url = isset($datas['Url']) ? trim($datas['Url']) : '';
        }
        
        $datas['content_process'] = $content;
        $datas['response'] = $response;
        return $datas;
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        $this->_app = new Application();
        $this->_appConfig = $this->_app->getTokenByAppid($this->appid);
        if (empty($this->_appConfig)) {
            throw new \Exception('appid所对应的记录不存在');
        }
    }

    protected function getDataFromWeixinServer()
    {
        $postStr = file_get_contents('php://input');
        $datas = $this->_source->revieve($postStr);
        // 需要解密
        if ($this->isNeedDecryptAndEncrypt) {
            
            $encodingAESKey = isset($this->_appConfig['EncodingAESKey']) ? $this->_appConfig['EncodingAESKey'] : '';
            if (empty($encodingAESKey)) {
                throw new \Exception('application EncodingAESKey is null');
            }
            $this->_sourceDatas['AESInfo']['EncodingAESKey'] = $encodingAESKey;
            
            $decryptMsg = "";
            $pc = new \Weixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($this->_sourceDatas['AESInfo']['verify_token'], $this->_sourceDatas['AESInfo']['EncodingAESKey'], $this->_appConfig['appid']);
            $errCode = $pc->decryptMsg($this->_sourceDatas['AESInfo']['msg_signature'], $this->_sourceDatas['AESInfo']['timestamp'], $this->_sourceDatas['AESInfo']['nonce'], $postStr, $decryptMsg);
            if (empty($errCode)) {
                $datas = $this->_source->revieve($decryptMsg);
                $this->_sourceDatas['AESInfo']['decryptMsg'] = $decryptMsg;
            } else {
                throw new \Exception('application EncodingAESKey is failure in decryptMsg callbackAction appid:' . $this->_appConfig['appid']);
            }
        }
        return $datas;
    }

    protected function responseToWeixinServer($response)
    {
        if ($response != 'success') {
            // 需要加密
            if ($this->isNeedDecryptAndEncrypt) {
                $this->_sourceDatas['AESInfo']['encryptMsg'] = $response;
                
                $encryptMsg = '';
                $timeStamp = time();
                $nonce = $this->_sourceDatas['AESInfo']['nonce'];
                $pc = new \Weixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($this->_sourceDatas['AESInfo']['verify_token'], $this->_sourceDatas['AESInfo']['EncodingAESKey'], $this->_appConfig['appid']);
                $errCode = $pc->encryptMsg($response, $timeStamp, $nonce, $encryptMsg);
                
                if (empty($errCode)) {
                    $response = $encryptMsg;
                } else {
                    throw new \Exception('application EncodingAESKey is failure in encryptMsg callbackAction appid:' . $this->_appConfig['appid']);
                }
            }
        }
        return $response;
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if (! empty($this->_sourceDatas)) {
            if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
                $this->_sourceDatas['interval'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            }
            $this->_sourceDatas['response_time'] = getCurrentTime();
            $postStr = file_get_contents('php://input');
            $this->_sourceDatas['request_xml'] = $postStr;
            if (isset($this->_sourceDatas['AESInfo'])) {
                $this->_sourceDatas['AESInfo'] = json_encode($this->_sourceDatas['AESInfo']);
            }
            unset($datas['__UNDO_SPECIAL_']);
            $this->_source->save($this->_sourceDatas);
        }
    }
}

