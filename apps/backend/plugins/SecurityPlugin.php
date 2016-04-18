<?php
namespace App\Backend\Plugins;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;
use App\Backend\Models\User;

/**
 * SecurityPlugin
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */
class SecurityPlugin extends Plugin
{

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event            
     * @param Dispatcher $dispatcher            
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        /* 验证管理员身份 */
        $this->validateAdminUserInfo($dispatcher);
        // 权限判断
        // $this->checkPrivilige($dispatcher);
    }
    
    /* 验证管理员身份 */
    private function validateAdminUserInfo(Dispatcher $dispatcher)
    {
        $actionName = $dispatcher->getActionName();
        
        if (! empty($_SESSION['admin_id']) || in_array($actionName, array(
            'login',
            'signin',
            'forgetpwd',
            'resetpwd',
            'captcha'
        ))) {
            return;
        }
        
        $adminUser = new User();
        try {
            /* cookie不存在 */
            if (empty($_COOKIE['ECSCP']['admin_id'])) {
                throw new \Exception('未登陆');
            }
            
            // 验证cookie信息
            $user = $adminUser->getInfoById($_COOKIE['ECSCP']['admin_id']);
            if (empty($user)) {
                throw new \Exception('用户不存在');
            }
            
            // 检查密码是否正确
            if (md5($user['password']) == $_COOKIE['ECSCP']['admin_pass']) {
                $adminUser->login($user);
            } else {
                throw new \Exception("密码不正确");
            }
        } catch (\Exception $e) {
            /* 清除cookie */
            $adminUser->clearCookies();
            if ($this->request->isAjax()) {
                $this->makeJsonError($e->getMessage());
            } else {
                $url = $this->url->get("admin/index/login");
                $this->response->redirect($url, true, 302)->send();
            }
        }
    }

    private function checkPrivilige(Dispatcher $dispatcher)
    {
        try {
            if (isset($_SESSION['admin_id'])) {
                $roleAlias = $_SESSION['roleInfo']['alias'];
            } else {
                $roleAlias = 'guest';
            }
            $module = "admin";
            
            if (empty($_SESSION[$roleAlias])) {
                // 获取权限列表
                $acl = new AclList();
                $acl->setDefaultAction(Acl::DENY);
                $acl->addRole(new Role($roleAlias));
                
                // 将controller添加为资源
                $privateResources = array();
                $controllerDirectory = $this->config->application->controllersDir;
                $diritem = new \DirectoryIterator($controllerDirectory);
                foreach ($diritem as $item) {
                    if ($item->isFile()) {
                        if (strstr($item->getFilename(), 'Controller.php') != FALSE) {
                            $controller = str_ireplace('Controller.php', '', $item->getFilename());
                            $controller = preg_replace("/([a-z0-9])([A-Z])/", "$1-$2", $controller);
                            $controller = strtolower($controller);
                            $resource = $module . '_' . $controller;
                            $privateResources[$resource] = null;
                        }
                    }
                }
                
                // 添加相应的权限给到相应的角色
                $defaultPermission = array(
                    "admin_index::login",
                    "admin_index::signin",
                    "admin_index::logout",
                    "admin_index::",
                    "admin_error::"
                );
                $permission = array();
                if (isset($_SESSION['admin_id'])) {
                    $permission = empty($_SESSION['roleInfo']['operation_list']) ? array() : $_SESSION['roleInfo']['operation_list'];
                }
                $permission = array_merge($defaultPermission, $permission);
                
                foreach ($permission as $item) {
                    $permissionArr = explode("::", $item);
                    if (! empty($permissionArr[1])) {
                        $privateResources[$resource][] = $permissionArr[1];
                    }
                }
                
                foreach ($privateResources as $resource => $actions) {
                    $acl->addResource(new Resource($resource), $actions);
                    if (empty($actions)) {
                        $acl->allow($roleAlias, $resource, '*');
                    } else {
                        foreach ($actions as $action) {
                            $acl->allow($roleAlias, $resource, $action);
                        }
                    }
                }
                $_SESSION[$roleAlias] = $acl;
            } else {
                $acl = $_SESSION[$roleAlias];
            }
            // 角色判断,当用户角色为非超级管理员时，进行权限判断
            if ($roleAlias != 'superAdmin') {
                $controller = $dispatcher->getControllerName();
                $action = $this->convert($dispatcher->getActionName());
                
                $allowed = $acl->isAllowed($roleAlias, $module . '_' . $controller, $action);
                if ($allowed != Acl::ALLOW) {
                    die($roleAlias . '_' . $module . '_' . $controller . '_' . $action);
                    throw new \Exception("很抱歉，您无权访问本资源!请重新登录、或者联系技术部开通管理权限");
                }
            }
        } catch (\Exception $e) {
            // die($e->getMessage());
            if ($this->request->isAjax()) {
                $this->makeJsonError($e->getMessage());
            } else {
                $url = $this->url->get("admin/error/show401");
                $this->response->redirect($url, true, 302)->send();
            }
        }
    }

    private function convert($name)
    {
        $newName = '';
        $tmp = preg_split("[\.|\-]", $name);
        $i = 0;
        foreach ($tmp as $cell) {
            if ($i > 0)
                $cell = ucfirst($cell);
            $newName .= $cell;
            $i ++;
        }
        return $newName;
    }

    private function makeJsonError($msg)
    {
        $res = array(
            'error' => 1,
            'message' => $msg,
            'content' => ''
        );
        $this->response->setJsonContent($res)->send();
    }
}
