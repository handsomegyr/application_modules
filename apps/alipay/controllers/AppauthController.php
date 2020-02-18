<?php

namespace App\Alipay\Controllers;

use App\Alipay\Models\User;
use App\Alipay\Models\Application;
use App\Alipay\Models\ScriptTracking;
use App\Alipay\Models\Callbackurls;

/**
 * 支付宝第三方应用授权
 * 注意 请不要修改这个文件,这个文件是同步的
 *
 * @author Administrator
 */
class AppauthController extends ControllerBase
{

    protected $_user;

    protected $_app;

    protected $_tracking;

    protected $_callbackurls;

    protected $_config;

    protected $_appConfig;

    protected $appid;

    protected $scope;

    protected $state;

    protected $cookie_session_key = 'iAlipay_appauth';

    protected $trackingKey = "APP授权";

    public function init()
    {
        parent::initialize();
        $this->view->disable();

        $this->_config = $this->getDI()->get('config');

        $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : $this->_config['alipay']['appid'];

        $this->_app = new Application();
        $this->_user = new User();
        $this->_tracking = new ScriptTracking();
        $this->_callbackurls = new Callbackurls();

        $this->doInitializeLogic();
    }

    /**
     * 应用授权URL拼装
     * 拼接规则
     * https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id=2015101400446982&redirect_uri=http%3A%2F%2Fexample.com
     *
     * http://www.example.com/alipay/appauth/index?appid=xxx&redirect=回调地址&dc=1
     * http://170908fg0353demo.umaman.com/alipay/appauth/index?appid=2017071707783020&redirect=https%3A%2F%2Fwww.baidu.com%2F
     */
    public function indexAction()
    {
        $scheme = $this->getRequest()->getScheme();

        try {
            $redirect = isset($_GET['redirect']) ? urlencode(trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
            $redirectUri = 'http://';
            $redirectUri .= $_SERVER["HTTP_HOST"];
            $redirectUri .= '/alipay';
            $redirectUri .= '/appauth';
            $redirectUri .= '/callback';
            $redirectUri .= '?appid=' . $this->appid;
            $redirectUri .= '&scope=' . $this->scope;
            $redirectUri .= '&redirect=' . urlencode($redirect);
            // 授权处理
            $redirectUri = \iAlipay::getAuthorizeUrl4Appauth($redirectUri, $this->appid);
            header("location:{$redirectUri}");
            exit();
        } catch (\Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    /**
     * 获取app_auth_code
     *
     * 商户授权成功后，pc或者钱包客户端会跳转至开发者定义的回调页面（即redirect_uri参数对应的url），在回调页面请求中会带上当次授权的授权码app_auth_code和开发者的app_id，示例如下：
     *
     * http://example.com/doc/toAuthPage.html?app_id=2015101400446982&app_auth_code=ca34ea491e7146cc87d25fca24c4cD11
     *
     * 第五步：使用app_auth_code换取app_auth_token
     */
    public function callbackAction()
    {
        try {
            $app_id = isset($_GET['app_id']) ? ($_GET['app_id']) : '';
            $userOutputs = isset($_GET['userOutputs']) ? ($_GET['userOutputs']) : '';
            $redirect = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : '';
            $auth_code = isset($_GET['auth_code']) ? ($_GET['auth_code']) : '';

            $source = isset($_GET['source']) ? ($_GET['source']) : '';
            $alipay_token = isset($_GET['alipay_token']) ? ($_GET['alipay_token']) : '';
            $readauth = isset($_GET['readauth']) ? ($_GET['readauth']) : '';
            if (empty($redirect)) {
                throw new \Exception("回调地址未定义");
            }
            echo '<pre>';
            print_r($_GET);
            die('xxx');
        } catch (\Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        $this->_appConfig = $this->_app->getApplicationInfoByAppId($this->appid);
        if (empty($this->_appConfig)) {
            throw new \Exception('appid所对应的记录不存在');
        }
    }
}
