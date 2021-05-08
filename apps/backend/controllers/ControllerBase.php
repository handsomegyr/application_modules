<?php

namespace App\Backend\Controllers;

use Phalcon\Mvc\View;

class ControllerBase extends \App\Common\Controllers\ControllerBase
{
    // 是否在iframe展示
    protected $__SHOWBYIFRAME__ = 0;

    protected function initialize()
    {

        parent::initialize();

        // $this->tag->prependTitle('INVO | ');
        $this->tag->prependTitle('AdminLTE | ');

        try {
            $adminConfig = $this->getDI()->get('adminConfig');
            $this->view->setVar("adminConfig", $adminConfig);
        } catch (\Exception $th) {
            die($th->getMessage());
        }

        // $this->view->setVar("resourceUrl", "/backend/metronic.bootstrap/");        
        // $this->view->setVar("resourceUrl", "/backend2/AdminLTE/");
        $this->view->setVar("resourceUrl", $adminConfig->admin->resourcePath);

        $viewClass = array();
        $viewClass['form-group'] = "form-group";
        $viewClass['label'] = "col-sm-2";
        $viewClass['field'] = "col-sm-8";
        $this->view->setVar("viewClass", $viewClass);

        // 是否在iframe展示
        $this->__SHOWBYIFRAME__ = intval($this->request->get('__SHOWBYIFRAME__'));
        $this->view->setVar("__SHOWBYIFRAME__", $this->__SHOWBYIFRAME__);

        // 不是ajax请求的话
        if (!$this->request->isAjax()) {
            // 构建菜单
            $this->buildMenus();
        }
    }

    protected function buildMenus()
    {
        $requestUrl = $this->moduleName . '/' . $this->controllerName;
        $is_active4index = ($requestUrl == 'admin/index') ? true : false;
        $this->view->setVar('is_active4index', $is_active4index);

        // 角色判断,当用户角色为非超级管理员时，进行权限判断
        if (isset($_SESSION['roleInfo'])) {
            $roleAlias = $_SESSION['roleInfo']['alias'];
        } else {
            $roleAlias = 'guest';
        }
        $this->view->setVar('roleAlias', $roleAlias);

        // 不在IFRAME中显示的话需要获取菜单信息
        if (empty($this->__SHOWBYIFRAME__)) {
            $menu_list = !empty($_SESSION['roleInfo']) ? $_SESSION['roleInfo']['menu_list'] : array();
            $modelMenu = new \App\Backend\Submodules\Backend\Models\Menu();
            $menus = $modelMenu->getPrivilege($menu_list, $requestUrl);
            // $menus2 = $modelMenu->buildPrivilegeTree($menu_list, $requestUrl);
            // print_r($menus);
            // print_r($menus2);
            // die($requestUrl);
            $this->view->setVar('menus', $menus);
        }
    }

    protected function _getValidationMessage($input)
    {
        $errorMsgList = array();
        $messageInfo = "";
        foreach ($input->getMessages() as $messageID => $message) {
            if (is_array($message)) {
                $messageInfo .= "Validation failure '$messageID':<br/>";
                foreach ($message as $key => $value) {
                    $messageInfo .= "Validation failure '$key': $value<br/>";
                    $errorMsgList[] = '' . $value;
                }
            } else {
                $messageInfo .= "Validation failure '$messageID': $message<br/>";
                $errorMsgList[] = '' . $message;
            }
        }
        $messageInfo = implode('||', $errorMsgList);
        return $messageInfo;
    }

    public function sysMsg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = true)
    {
        if (count($links) == 0) {
            $links[0]['text'] = '返回上一页';
            $links[0]['href'] = 'javascript:history.go(-1)';
        }

        $this->view->setVar('ur_here', '系统信息');
        $this->view->setVar('msg_detail', $msg_detail);
        $this->view->setVar('msg_type', $msg_type);
        $this->view->setVar('links', $links);
        $this->view->setVar('default_url', $links[0]['href']);
        $this->view->setVar('auto_redirect', $auto_redirect);
        // $this->view->pick("error/message");
        $this->view->partial("error/message");
    }

    public function makeJsonResult($content = '', $message = '', $append = array())
    {
        $this->makeJsonResponse($content, 0, $message, $append);
    }

    public function makeJsonError($msg)
    {
        unset($_SESSION['toastr']);
        // $_SESSION['toastr']['type'] = "success";
        // $_SESSION['toastr']['message'] = $msg;
        // $_SESSION['toastr']['options'] = array();
        $this->makeJsonResponse('', 1, $msg);
    }

    /**
     * 创建一个JSON格式的数据
     *
     * @access public
     * @param string $content            
     * @param integer $error            
     * @param string $message            
     * @param array $append            
     * @return void
     */
    public function makeJsonResponse($content = '', $error = "0", $message = '', $append = array())
    {
        $res = array(
            'error' => $error,
            'message' => $message,
            'content' => $content
        );

        if (!empty($append)) {
            foreach ($append as $key => $val) {
                $res[$key] = $val;
            }
        }

        //https://docs.phalcon.io/4.0/en/controllers#overview
        if (version_compare(PHALCON_VERSION, '4.0.0') < 0) {
            $this->response->setJsonContent($res)->send();
        } else {
            $response = new \Phalcon\Http\Response();
            $response->setStatusCode(200, "OK");
            $response->setJsonContent($res)->send();
        }
    }

    public function disableLayout()
    {
        $this->view->disableLevel(array(
            View::LEVEL_LAYOUT => true,
            View::LEVEL_MAIN_LAYOUT => true
        ));
    }

    protected function checkToken($token)
    {
        return true;

        if (empty($token)) {
            throw new \Exception("_token is empty");
        }

        if (empty($_SESSION['csrf_token'])) {
            throw new \Exception("session token is empty");
        }

        if ($_SESSION['csrf_token'] != $token) {
            throw new \Exception("token is not correct current_token:" . $_SESSION['csrf_token']);
        }

        return true;
    }

    protected function getUrl($action, $controllerName = "", $moduleName = "")
    {
        $url = parent::getUrl($action, $controllerName, $moduleName);

        if (!empty($this->__SHOWBYIFRAME__)) {
            return  $url . "?__SHOWBYIFRAME__=1";
        } else {
            return  $url;
        }
    }
}
