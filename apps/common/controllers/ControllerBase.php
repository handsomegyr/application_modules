<?php
namespace App\Common\Controllers;

use Respect\Validation\Validator as v;
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{

    protected $moduleName = '';

    protected $controllerName = '';

    protected $actionName = '';

    protected $baseUrl = '';

    protected $webUrl = '';

    protected function initialize()
    {
        $scheme = $this->request->getScheme();
        $httpHost = $this->request->getHttpHost();
        $this->baseUrl = $this->url->getBaseUri();
        $this->view->setVar("baseUrl", $this->baseUrl);
        $this->webUrl = "{$scheme}://{$httpHost}{$this->baseUrl}";
        $this->view->setVar("webUrl", $this->webUrl);
        $this->moduleName = $this->router->getModuleName();
        $this->controllerName = $this->router->getControllerName();
        $this->actionName = $this->router->getActionName();
        
        $this->view->setVar("commonResourceUrl", "/common/");
        
        $this->view->setVar("moduleName", $this->moduleName);
        $this->view->setVar("controllerName", $this->controllerName);
        $this->view->setVar("actionName", $this->actionName);
    }

    protected function getSelfUrl()
    {
        return $this->getUrl($this->actionName);
    }

    protected function getUrl($action)
    {
        return $this->url->get("{$this->moduleName}/{$this->controllerName}/{$action}");
    }

    public function get($string, $defaultParam = null, $defaultType = "string")
    {
        return $this->request->get($string, $defaultType, $defaultParam);
    }

    public function result($msg = '', $result = '')
    {
        $jsonpcallback = trim($this->get('jsonpcallback'));
        return jsonpcallback($jsonpcallback, true, $msg, $result);
    }

    public function error($code, $msg)
    {
        $jsonpcallback = trim($this->get('jsonpcallback'));
        return jsonpcallback($jsonpcallback, false, $msg, $code);
    }

    public function _redirect($location = null, $externalRedirect = true, $statusCode = 302)
    {
        $this->response->redirect($location, $externalRedirect, $statusCode)->send();
    }

    public function forward($uri)
    {
        $uriParts = explode('/', $uri);
        $itemCount = count($uriParts);
        $params = array_slice($uriParts, 3);
        return $this->dispatcher->forward(array(
            'module' => $uriParts[0],
            'controller' => $uriParts[1],
            'action' => $uriParts[2],
            'params' => $params
        ));
    }

    public function assign($key, $value)
    {
        $this->view->setVar($key, $value);
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 微信openid校验
     *
     * @param string $FromUserName            
     * @param string $timestamp            
     * @param string $secretKey            
     * @param string $signature            
     * @return boolean
     */
    public function validateOpenid($FromUserName, $timestamp, $secretKey, $signature)
    {
        $secret = sha1($FromUserName . "|" . $secretKey . "|" . $timestamp);
        if ($signature != $secret) {
            return false;
        } else {
            return true;
        }
    }

    public function refreshPage($seconds = 1)
    {
        // sleep($seconds); 禁止在服务端等待，必须在客户端等待刷新
        $scheme = $this->getRequest()->getScheme();
        $url = "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        // $this->_redirect($url);
        // 输出在客户端，等待页面刷新
        echo "<!DOCTYPE html><html><head><meta http-equiv=\"refresh\" content=\"{$seconds}\" url=\"{$url}\"></head><body>Loading……</body></html>";
        exit();
    }

    protected function validateEmail($email)
    {
        $isOk = v::notEmpty()->email()->validate($email);
        if (! $isOk) {
            return $this->errors['e506'];
        }
        
        return $this->errors['none'];
    }

    protected function validateMobile($mobile)
    {
        // $isOk = v::notEmpty()->mobile()->validate($mobile);
        if (empty($mobile) && ! isValidMobile($mobile)) {
            return $this->errors['e508'];
        }
        
        return $this->errors['none'];
    }

    protected function validateVcode($vcode, $vkey)
    {
        $vcodeValidator = v::notEmpty()->noWhitespace();
        $isOk = $vcodeValidator->validate($vcode);
        if (! $isOk) {
            return $this->errors['e513'];
        }
        if (empty($_SESSION['vcode']) || time() > $_SESSION['vcode']['expire_time']) {
            return $this->errors['e513'];
        }
        
        if (! ($_SESSION['vcode']['vkey'] == $vkey && $_SESSION['vcode']['vcode'] == $vcode)) {
            return $this->errors['e513'];
        }
        return $this->errors['none'];
    }
}
