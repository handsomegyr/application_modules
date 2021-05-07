<?php

namespace App\Backend\Plugins;

use Phalcon\Acl;
use Phalcon\Acl\Role as AclRole;
use Phalcon\Acl\Resource as AclResource;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;
use App\Backend\Models\User;
use App\Common\Models\Backend\Resource;
use App\Backend\Models\OperationLog;

/**
 * SecurityPlugin
 *
 * This is the security plugin which controls that users only have access to the modules they're assigned to
 */

// https://docs.phalcon.io/4.0/en/upgrade#upgrade-guide
// class SecurityPlugin extends \Phalcon\Mvc\User\Plugin
class SecurityPlugin extends \Phalcon\Di\Injectable
{
    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event            
     * @param Dispatcher $dispatcher            
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        if (isset($_SESSION['admin_id'])) {
            $moduleName = $dispatcher->getModuleName();
            $controllerName = $dispatcher->getControllerName();
            $actionName = $dispatcher->getActionName();

            if (($moduleName == 'admin/system' && $controllerName == 'operationlog') ||
                ($moduleName == 'admin' && $controllerName == 'index' && $actionName == 'keep-login')
            ) {
            } else {
                $method = $this->request->getMethod();
                $params = $this->request->get();
                $path = $this->request->getURI();
                $ip = getIp();
                // 记录操作日志log
                $modelOperationLog = new OperationLog();
                $modelOperationLog->log($_SESSION['admin_id'], $path, $method, $ip, $params);
            }
        }

        /* 验证管理员身份 */
        $this->validateAdminUserInfo($dispatcher);
    }

    /* 验证管理员身份 */
    private function validateAdminUserInfo(Dispatcher $dispatcher)
    {
        $module = $dispatcher->getModuleName();
        $controller = $dispatcher->getControllerName();
        $actionName = $dispatcher->getActionName();

        // 只要action是以下中的一个就取消检查
        if (in_array($actionName, array(
            'login',
            'signin',
            'forgetpwd',
            'resetpwd',
            'captcha',
            'keeplogin'
        ))) {
            return;
        }
        $adminUser = new User();
        try {
            // 如果会话不存在那么就检查cookie
            if (empty($_SESSION['admin_id'])) {
                /* cookie不存在 */
                if (empty($_COOKIE['backend']['admin_id'])) {
                    throw new \Exception('未登陆1');
                }

                // 验证cookie信息
                $user = $adminUser->getInfoById($_COOKIE['backend']['admin_id']);
                if (empty($user)) {
                    throw new \Exception('用户不存在');
                }

                // 检查密码是否正确
                if (md5($user['password']) == $_COOKIE['backend']['admin_pass']) {
                    $adminUser->login($user);
                } else {
                    throw new \Exception("密码不正确");
                }
            }
            if (empty($_SESSION['admin_id'])) {
                throw new \Exception('未登陆2');
            } else {
                // 权限判断
                $this->checkPrivilige($dispatcher);
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

            // 角色判断,当用户角色为非超级管理员时，进行权限判断
            if ($roleAlias != 'superAdmin') {
                // 获取权限列表
                $acl = $this->getAcl();

                $module = str_replace('/', '_', $dispatcher->getModuleName());
                $controller = $dispatcher->getControllerName();
                $actionName = $dispatcher->getActionName();
                $allowed = $acl->isAllowed($roleAlias, $module . '_' . $controller, $actionName);
                if ($allowed != Acl::ALLOW) {
                    //die($roleAlias . '_' . $module . '_' . $controller . '_' . $actionName);
                    throw new \Exception("很抱歉，您无权访问本资源!请重新登录、或者联系技术部开通管理权限");
                }
            }
        } catch (\Exception $e) {
            // die($e->getMessage());
            if ($this->request->isAjax()) {
                $this->makeJsonError($e->getMessage());
            } else {
                $url = $this->url->get("admin/error/show401");
                //$url = $this->url->get("admin/index/login");
                //die("很抱歉，您无权访问本资源!请重新登录、或者联系技术部开通管理权限");
                $this->response->redirect($url, true, 302)->send();
            }
        }
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

    //返回一个已存在的或新创建的acl列表
    protected $acl = null;
    private function getAcl()
    {
        if (empty($this->acl)) {
            $acl = new AclList();
            //设置默认访问级别为“拒绝”
            $acl->setDefaultAction(Acl::DENY);

            //添加角色
            $roles = array(
                'guest' => new AclRole('guest')
            );
            $roleAlias = "";
            if (isset($_SESSION['admin_id'])) {
                $roleAlias = $_SESSION['roleInfo']['alias'];
                $roles[$roleAlias] = new AclRole($roleAlias);
            }
            foreach ($roles as $role) {
                $acl->addRole($role);
            }

            //添加私有资源
            $adminResource = new Resource();
            $resourceList = $adminResource->findAll(array());
            // [module] => admin
            // [module_name] => 后台管理
            // [controller] => form
            // [controller_name] => 表管理
            // [action] => list
            // [action_name] => 显示列表页面
            $privateResources = array();
            foreach ($resourceList as $resource) {
                $key = strtolower($resource['module']) . '_' . strtolower(str_replace('-', '_', $resource['controller']));
                $privateResources[$key][] = strtolower($resource['action']);
            }
            foreach ($privateResources as $resource => $actions) {
                $acl->addResource(new AclResource($resource), $actions);
            }

            //添加公有资源
            $publicResources = array(
                //'admin_builder' => array('createmenu', 'createschema', 'createfile', 'createmenuwithfiles', 'downloadfile'),
                'admin_index' => array('index', 'logout', 'login', 'signin', 'keeplogin'),
                'admin_error' => array('show404', 'show401', 'show500', 'message'),
                'admin_form' => array('list', 'export', 'query', 'add', 'insert', 'edit', 'update', 'remove', 'removefile')
            );
            foreach ($publicResources as $resource => $actions) {
                $acl->addResource(new AclResource($resource), $actions);
            }

            //公有资源访问控制
            foreach ($roles as $role) {
                foreach ($publicResources as $resource => $actions) {
                    foreach ($actions as $action) {
                        $acl->allow($role->getName(), $resource, $action);
                    }
                }
            }

            //私有资源访问控制
            if (isset($_SESSION['admin_id'])) {
                $roleAlias = $_SESSION['roleInfo']['alias'];
                $permission = empty($_SESSION['roleInfo']['operation_list']) ? array() : $_SESSION['roleInfo']['operation_list'];
                $resources4Allow = array();
                foreach ($permission as $item) {
                    $permissionArr = explode("::", $item);
                    if (!empty($permissionArr[1])) {
                        $resources4Allow[strtolower(str_replace('-', '_', $permissionArr[0]))][] = strtolower($permissionArr[1]);
                    }
                }
                foreach ($resources4Allow as $resource => $actions) {
                    foreach ($actions as $action) {
                        $acl->allow($roleAlias, $resource, $action);
                    }
                }
            }
            $this->acl = $acl;
        }
        return $this->acl;
    }
}
