<?php

/**
 * 公众号授权给第三方平台的技术实现流程
 * @author Administrator
 *
 */
namespace App\Weixin\Controllers;

use App\Weixin\Models\ComponentApplication;

class ComponentController extends IndexController
{

    private $_weixinComponent;

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        $this->isNeedDecryptAndEncrypt = true;
        
        $this->_app = new ComponentApplication();
        
        $this->_appConfig = $this->_app->getTokenByAuthorizerAppid($this->appid);
        if (empty($this->_appConfig)) {
            throw new \Exception('appid所对应的记录不存在');
        }
        $this->_weixinComponent = new \Weixin\Component($this->_appConfig['appid'], $this->_appConfig['secret']);
        if (! empty($this->_appConfig['component_access_token'])) {
            $this->_weixinComponent->setAccessToken($this->_appConfig['component_access_token']);
        }
    }

    /**
     * http://www.example.com/weixin/component/login?appid=xxx&redirect=回调地址
     * http://weshopdemo.umaman.com/weixin/component/login?appid=wx1220b803c9a1dc9a&redirect=http%3A%2F%2Fwww.baidu.com%2F
     * 引导用户去往登录授权
     */
    public function loginAction()
    {
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            // 2 获取预授权码（pre_auth_code）
            $preAuthCodeInfo = $this->_weixinComponent->apiCreatePreauthcode();
            $pre_auth_code = $preAuthCodeInfo['pre_auth_code'];
            
            // 3 引入用户进入授权页
            $redirect = isset($_GET['redirect']) ? urlencode(trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
            
            $moduleName = 'weixin';
            $controllerName = 'component';
            
            $redirectUri = 'http://';
            $redirectUri .= $_SERVER["HTTP_HOST"];
            $redirectUri .= '/' . $moduleName;
            $redirectUri .= '/' . $controllerName;
            $redirectUri .= '/logincallback';
            $redirectUri .= '?appid=' . $this->appid;
            $redirectUri .= '&redirect=' . urlencode($redirect);
            $this->_weixinComponent->getComponentLoginPage($pre_auth_code, $redirectUri, true);
        } catch (\Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    /**
     * 授权后回调URI，得到授权码（authorization_code）和过期时间
     * 授权流程完成后，授权页会自动跳转进入回调URI，并在URL参数中返回授权码和过期时间(redirect_url?auth_code=xxx&expires_in=600)
     *
     * @return boolean
     */
    public function logincallbackAction()
    {
        // http://weshopdemo.umaman.com/weixin/component/logincallback?appid=wx1220b803c9a1dc9a&redirect=xxx&auth_code=xx
        try {
            $redirect = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : '';
            if (empty($redirect)) {
                throw new \Exception("回调地址未定义");
            }
            $auth_code = isset($_GET['auth_code']) ? trim($_GET['auth_code']) : '';
            if (empty($auth_code)) {
                throw new \Exception('auth_code不能为空');
            }
            // 使用授权码换取公众号的接口调用凭据和授权信息
            $authInfo = $this->_weixinComponent->apiQueryAuth($auth_code);
            $authorizationInfo = $authInfo['authorization_info'];
            // Array ( [authorizer_appid] => wxbf9165206b992f39 [authorizer_access_token] => doQIVUEgvqAgLCeN3GtniVVEFfV-SoZ2sPKSepbLTFhy5jZbHXRdzd2qDd1AsZq_xm5c0BKfyO8X0RZ9YAxEPiLmErooso1zUdcA_mbL0ftq75Ax2i1hd6DwoQ8sMPUHKDYfALDPID [expires_in] => 7200 [authorizer_refresh_token] => refreshtoken@@@MLsM93Cl_nO3WMSQ2enriI9sf0-gMgawkCWJA8dtOxQ [func_info] => Array ( [0] => Array ( [funcscope_category] => Array ( [id] => 1 ) ) [1] => Array ( [funcscope_category] => Array ( [id] => 15 ) ) [2] => Array ( [funcscope_category] => Array ( [id] => 4 ) ) [3] => Array ( [funcscope_category] => Array ( [id] => 7 ) ) [4] => Array ( [funcscope_category] => Array ( [id] => 2 ) ) [5] => Array ( [funcscope_category] => Array ( [id] => 3 ) ) [6] => Array ( [funcscope_category] => Array ( [id] => 11 ) ) [7] => Array ( [funcscope_category] => Array ( [id] => 6 ) ) [8] => Array ( [funcscope_category] => Array ( [id] => 5 ) ) [9] => Array ( [funcscope_category] => Array ( [id] => 8 ) ) [10] => Array ( [funcscope_category] => Array ( [id] => 13 ) ) [11] => Array ( [funcscope_category] => Array ( [id] => 9 ) ) [12] => Array ( [funcscope_category] => Array ( [id] => 10 ) ) [13] => Array ( [funcscope_category] => Array ( [id] => 12 ) ) ) ) logincallbackAction
            
            // print_r($authorizationInfo);
            // die('logincallbackAction');
            
            // 更新accesstoken
            $this->_app->updateAuthorizerAccessToken($this->_appConfig, $authorizationInfo['authorizer_access_token'], $authorizationInfo['authorizer_refresh_token'], $authorizationInfo['expires_in'], $authInfo);
            $this->_tracking->record("公众号授权给第三方平台流程", $_SESSION['oauth_start_time'], microtime(true), $authorizationInfo['authorizer_appid']);
            
            header("location:{$redirect}");
            exit();
        } catch (\Exception $e) {
            die($e->getMessage());
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    /**
     * 获取（刷新）授权公众号的接口调用凭据
     */
    public function authorizertokenAction()
    {
        // http://weshopdemo.umaman.com/weixin/component/authorizertoken?appid=wx1220b803c9a1dc9a
        try {
            // 该API用于在授权方令牌（authorizer_access_token）失效时，可用刷新令牌（authorizer_refresh_token）获取新的令牌。
            $authorizer_appid = $this->appid;
            $authorizer_refresh_token = $this->_appConfig['refresh_token'];
            $authorizerTokenInfo = $this->_weixinComponent->apiAuthorizerToken($authorizer_appid, $authorizer_refresh_token);
            
            // 更新accesstoken
            $this->_app->updateAuthorizerAccessToken($this->_appConfig, $authorizerTokenInfo['authorizer_access_token'], $authorizerTokenInfo['authorizer_refresh_token'], $authorizerTokenInfo['expires_in']);
            
            return true;
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }
    }

    /**
     * 授权事件接收URL
     * weshopdemo.umaman.com/weixin/component/authorizecallback?appid=xxxx
     * 在公众号第三方平台创建审核通过后，微信服务器会向其“授权事件接收URL”每隔10分钟定时推送component_verify_ticket。第三方平台方在收到ticket推送后也需进行解密（详细请见【消息加解密接入指引】），接收到后必须直接返回字符串success。
     */
    public function authorizecallbackAction()
    {
        // http://weshopdemo.umaman.com/weixin/component/authorizecallback?appid=wx1220b803c9a1dc9a
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
            $AESInfo['api'] = 'authorizecallback';
            $this->_sourceDatas['AESInfo'] = $AESInfo;
            
            $verifyToken = isset($this->_appConfig['verify_token']) ? $this->_appConfig['verify_token'] : '';
            if (empty($verifyToken)) {
                throw new \Exception('application verify_token is null');
            }
            $this->_sourceDatas['AESInfo']['verify_token'] = $verifyToken;
            
            $encodingAESKey = isset($this->_appConfig['EncodingAESKey']) ? $this->_appConfig['EncodingAESKey'] : '';
            if (empty($encodingAESKey)) {
                throw new \Exception('application EncodingAESKey is null');
            }
            $this->_sourceDatas['AESInfo']['EncodingAESKey'] = $encodingAESKey;
            
            // 签名正确，将接受到的xml转化为数组数据并记录数据
            $postStr = file_get_contents('php://input');
            $datas = $this->_source->revieve($postStr);
            $decryptMsg = "";
            $pc = new \Weixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($verifyToken, $encodingAESKey, $this->_appConfig['appid']);
            $errCode = $pc->decryptMsg($AESInfo['msg_signature'], $AESInfo['timestamp'], $AESInfo['nonce'], $postStr, $decryptMsg);
            if (empty($errCode)) {
                $datas = $this->_source->revieve($decryptMsg);
                $this->_sourceDatas['AESInfo']['decryptMsg'] = $decryptMsg;
            } else {
                throw new \Exception('application EncodingAESKey is failure in decryptMsg authorizecallbackAction appid:' . $this->_appConfig['appid']);
            }
            
            foreach ($datas as $dtkey => $dtvalue) {
                $this->_sourceDatas[$dtkey] = $dtvalue;
            }
            $this->_sourceDatas['response'] = 'success';
            
            // 调试接口信息
            if ($__DEBUG__) {
                $datas = $this->_app->debug($__DEBUG__);
            }
            
            $AppId = isset($datas['AppId']) ? trim($datas['AppId']) : '';
            $InfoType = isset($datas['InfoType']) ? trim($datas['InfoType']) : '';
            $CreateTime = isset($datas['CreateTime']) ? intval($datas['CreateTime']) : time();
            
            // 关于重试的消息排重
            $uniqueKey = $AppId . "-" . $CreateTime . "-" . $InfoType;
            if (! empty($uniqueKey)) {
                $objLock = new \iLock(md5($uniqueKey));
                if ($objLock->lock()) {
                    echo "success";
                    return true;
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
                $ComponentVerifyTicket = isset($datas['ComponentVerifyTicket']) ? trim($datas['ComponentVerifyTicket']) : ''; // Ticket内容
                                                                                                                              
                // 获取第三方平台component_access_token
                $componentToken = $this->_weixinComponent->apiComponentToken($ComponentVerifyTicket);
                
                // 更新component_access_token
                $this->_app->updateComponentAccessToken($this->_appConfig, $componentToken['component_access_token'], $componentToken['expires_in'], $ComponentVerifyTicket);
                
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
            // 输出响应结果
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
            
            // 将一些执行很慢的逻辑，放在这里执行，提高微信的响应速度结束
            
            return true;
        } catch (\Exception $e) {
            // 如果脚本执行中发现异常，则记录返回的异常信息
            $this->_sourceDatas['response'] = exceptionMsg($e);
            return false;
        }
    }

    /**
     * 测试用
     */
    public function decryptMsgAction()
    {
        // http://weshopdemo.umaman.com/weixin/component/decrypt-msg?appid=wx1220b803c9a1dc9a
        
        // <xml>
        // <AppId><![CDATA[wx7d9829b9bb066fe5]]></AppId>
        // <Encrypt><![CDATA[zVOkgBq+VNnmXwyDs9AIUymWt2P2cemjBrDROoeIu39Bb3FpqJ7+bTcwZtQL7sGfoZZHJ2DdGD7NKON3KpQfYorrm2bQAadlabSXHpgaZytcCPWvOLBLce2viKU0mBP7LTD45ASZ08evyuhSxU3WmNsi+WooxSRv6LjqnSyfg0qJbpfDTTBqOUok1Y11snMofp8YsHBfgh06zRdQjXw5au0z92dv4sdZVEwN2Fl83AlqrfbaLcvZbNSdY2/yKN4fZGMlOhF571h/AC6E/4IpBfCbKjfurd5ZYzBjmELRnR7fXuI8CsShV+ygRK2ResIqL+n20RbXOOOm3JNtZDrPilZggAvEL68NBLDaAvwLHuAMq+/9gR/vf9OhN3mCIcvnsJy/mMdnDebzPMJFcdmOw5ZSNgSrEnwgnfLRfBzyXCPYKQMJtrkOAE4orlhLUXo2CGHYvHoMwhz95PzXorIvsA==]]></Encrypt>
        // </xml>
        $verifyToken = isset($this->_appConfig['verify_token']) ? $this->_appConfig['verify_token'] : '';
        $encodingAesKey = isset($this->_appConfig['EncodingAESKey']) ? $this->_appConfig['EncodingAESKey'] : '';
        $AppId = $this->_appConfig['appid'];
        
        // die($encodingAesKey.strlen($encodingAesKey));
        $pc = new \Weixin\ThirdParty\MsgCrypt\WXBizMsgCrypt($verifyToken, $encodingAesKey, $AppId);
        
        // // {"timestamp":"1471587174","nonce":"391052533","encrypt_type":"aes","msg_signature":"d6e5d4ac83920b200e50cc6037a7ab97db507966"}
        // $msg_sign = "d6e5d4ac83920b200e50cc6037a7ab97db507966";
        // $timeStamp = "1471587174";
        // $nonce = "391052533";
        // $from_xml = "<xml><AppId><![CDATA[wx7d9829b9bb066fe5]]></AppId><Encrypt><![CDATA[zVOkgBq+VNnmXwyDs9AIUymWt2P2cemjBrDROoeIu39Bb3FpqJ7+bTcwZtQL7sGfoZZHJ2DdGD7NKON3KpQfYorrm2bQAadlabSXHpgaZytcCPWvOLBLce2viKU0mBP7LTD45ASZ08evyuhSxU3WmNsi+WooxSRv6LjqnSyfg0qJbpfDTTBqOUok1Y11snMofp8YsHBfgh06zRdQjXw5au0z92dv4sdZVEwN2Fl83AlqrfbaLcvZbNSdY2/yKN4fZGMlOhF571h/AC6E/4IpBfCbKjfurd5ZYzBjmELRnR7fXuI8CsShV+ygRK2ResIqL+n20RbXOOOm3JNtZDrPilZggAvEL68NBLDaAvwLHuAMq+/9gR/vf9OhN3mCIcvnsJy/mMdnDebzPMJFcdmOw5ZSNgSrEnwgnfLRfBzyXCPYKQMJtrkOAE4orlhLUXo2CGHYvHoMwhz95PzXorIvsA==]]></Encrypt></xml>";
        // $msg = "";
        // $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
        // if ($errCode == 0) {
        // // <xml><AppId><![CDATA[wx7d9829b9bb066fe5]]></AppId>
        // // <CreateTime>1471587174</CreateTime>
        // // <InfoType><![CDATA[component_verify_ticket]]></InfoType>
        // // <ComponentVerifyTicket><![CDATA[ticket@@@W5u6NJ1xgmN7-knPk5WBa4ryliCHRxtok6EjcQRRDa-dHiEbVCe24rHKfClu4T9vrTRHnenRHGh6PQraQsV5KQ]]></ComponentVerifyTicket>
        // // </xml>
        // print("解密后: " . $msg . "\n");
        // } else {
        // die('errcode:' . $errCode);
        // }
        
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
            print("解密后: " . $msg . "\n");
        } else {
            die('errcode:' . $errCode);
        }
    }
}

