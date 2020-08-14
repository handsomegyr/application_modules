<?php

namespace App\Backend\Tags;

use App\Backend\Submodules\Backend\Models\Menu;
use App\Backend\Submodules\Backend\Models\Resource;

class MyTags extends \Phalcon\Tag
{

    static public function getUploadFilePath($path, $fileName = "")
    {
        $arr = array();
        $arr[] = 'upload';

        $path = trim($path, '/');
        if (!empty($path)) {
            $arr[] = $path;
        }
        if (!empty($fileName)) {
            $arr[] = $fileName;
        }
        return "/" . implode('/', $arr);
    }

    static public function getMenuTree($priv, $field)
    {
        $sub_menus = "";
        if (!empty($priv['priv'])) {
            foreach ($priv['priv'] as $priv_list => $list) {
                $sub_menus .= self::getMenuTree($list, $field);
            }
        }
        // print_r($priv);
        $priv_list = empty($priv['_id']) ? "" : $priv['_id'];

        $checked = "";
        if ($priv['cando'] == 1) {
            $checked = "checked";
        }
        if (!empty($sub_menus)) {
            $checkBox = <<<EOT
<input name="chkGroup" type="checkbox" value="{$priv_list}" id="{$priv_list}" {$checked} onclick="check('{$priv["priv_list"]}',this);" />
EOT;
        } else {
            $checkBox = <<<EOT
<input name="{$field}[]" type="checkbox" value="{$priv_list}" id="{$priv_list}" {$checked} onclick="checkrelevance('{$priv['relevance']}', '{$priv_list}')" title="{$priv['relevance']}" />
EOT;
        }


        // <button data-action="collapse" type="button" style="display: block;">Collapse</button>
        // <button data-action="expand" type="button" style="display: none;">Expand</button>

        $str = <<<EOT
<ol class="dd-list">
    <li class="dd-item" data-id="{$priv_list}">
        <div class="dd-handle">
            <i class="fa fa-tasks"></i>&nbsp;<strong>{$priv['name']}</strong>
            <span class="pull-right dd-nodrag">
                {$checkBox}
            </span>
        </div>
    </li>
    {$sub_menus}
</ol>
EOT;
        return $str;
    }

    static public function isCanDo($module, $controller, $action)
    {
        // 角色判断,当用户角色为非超级管理员时，进行权限判断
        if (isset($_SESSION['admin_id'])) {
            $roleAlias = $_SESSION['roleInfo']['alias'];
            $operation_list = $_SESSION['roleInfo']['operation_list'];
        } else {
            $roleAlias = 'guest';
            $operation_list = array();
        }
        if ($roleAlias == 'superAdmin') {
            return true;
        }

        if (empty($operation_list)) {
            return false;
        }

        //admin_activity-activity::query
        $operation = strtolower(str_replace('/', '_', $module) . '-' . $controller . '::' . $action);
        // print_r($operation_list);
        // die($operation);
        if (in_array($operation, $operation_list)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取权限列表
     * 
     * @return string
     */
    static public function showPrivilege($menu_list, $operation_list)
    {
        $privList = array();
        /* 获取菜单数据 */
        $modelMenu = new Menu();
        $menu_priv_arr = $modelMenu->getPrivilege($menu_list);
        $privList['菜单设置'] = array(
            'values' => $menu_priv_arr,
            'field' => 'menu_list'
        );
        /* 获取操作数据 */
        $modelResource = new Resource();
        $operation_priv_arr = $modelResource->getPrivilege("admin", $operation_list);
        // print_r($operation_priv_arr);
        // die('xxx');
        $privList['操作设置'] = array(
            'values' => $operation_priv_arr,
            'field' => 'operation_list'
        );
        return $privList;
    }

    /**
     * 对于注释中，使用@name 的变量内容，自动读取为方法名
     *
     * @return array
     */
    public static function getList($module = "")
    {
        $resources = array();
        $dirbasepath = APP_PATH . "apps/backend";
        require_once($dirbasepath . '/submodules/qyweixin/controllers/BaseController.php');
        require_once($dirbasepath . '/submodules/weixin2/controllers/BaseController.php');
        self::includeControllerFiles($dirbasepath);

        $reader = new \Phalcon\Annotations\Adapter\Memory();

        $subclassList = array();
        foreach (get_declared_classes() as $class) {

            if (is_subclass_of($class, 'App\Backend\Controllers\ControllerBase') && substr($class, -10) == 'Controller') {

                // App\Backend\Submodules\Activity\Controllers\ActivityController
                $c = preg_replace('/Submodules\\\(.*?)\\\Controllers\\\/i', 'Controllers\\\$1', $class);
                $c = str_ireplace("App\\Backend\\Controllers\\", 'Admin_', $c);
                $c = substr($c, 0, strpos($c, "Controller"));
                $subclassList[] = $c;
                $c = self::methodToRouter($c);
                $c = strtolower($c);

                // if($class == 'App\Backend\Submodules\Weixin\Controllers\ReplyTypeController'){
                // die($c);
                // }

                if (strpos($c, $module) === 0) {
                    try {
                        $className = $c;
                        // 反射在Example类的注释
                        $reflector = $reader->get($class);
                        // 读取类中注释块中的注释
                        $annotations = $reflector->getClassAnnotations();
                        // 读取类的所有方法中注释块中的注释
                        $methodsAnnotations = $reflector->getMethodsAnnotations();

                        if ($annotations) {
                            // 遍历注释
                            foreach ($annotations as $annotation) {
                                // 打印注释名称
                                if (strtolower($annotation->getName()) == "title") {
                                    $titleAnnotations = $annotation->getArguments();
                                    if (!empty($titleAnnotations) && key_exists("name", $titleAnnotations[0])) {
                                        $className = $titleAnnotations[0]['name'];
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        $className = $c;
                    }

                    $functions = array();
                    foreach (get_class_methods($class) as $method) {
                        if (strstr($method, 'Action') != false) {
                            try {
                                $method = substr($method, 0, strpos($method, "Action"));
                                $method = self::methodToRouter($method);
                                $name = $c . '::' . $method;
                                $key = $c . '::' . $method;

                                if ($methodsAnnotations) {
                                    $methodkey = $method . "Action";
                                    if (key_exists($methodkey, $methodsAnnotations)) {
                                        $methodAnnotations = $methodsAnnotations[$methodkey];
                                        if ($methodAnnotations) {
                                            // 遍历注释
                                            foreach ($methodAnnotations as $annotation) {
                                                // 打印注释名称
                                                if (strtolower($annotation->getName()) == "title") {
                                                    $titleAnnotations = $annotation->getArguments();
                                                    if (!empty($titleAnnotations) && key_exists("name", $titleAnnotations[0])) {
                                                        $name = $titleAnnotations[0]['name'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } catch (\Exception $e) {
                                $method = substr($method, 0, strpos($method, "Action"));
                                $method = self::methodToRouter($method);
                                $name = $c . '::' . $method;
                                $key = $c . '::' . $method;
                            }

                            $function = array(
                                'name' => $name,
                                'method' => $method,
                                'key' => $key
                            );
                            array_push($functions, $function);
                        }
                    }

                    $resources[$module][$c . "||" . $className] = $functions;
                }
            }
        }

        // print_r($subclassList);
        // die('xxx222333');

        return $resources;
    }

    static public function getUrl($view, $actionName, $params = array(), $controllerName = '', $moduleName = '', $baseUrl = '')
    {
        if (empty($baseUrl)) {
            $baseUrl = $view->baseUrl;
        }
        if (empty($moduleName)) {
            $moduleName = $view->moduleName;
        }
        if (empty($controllerName)) {
            $controllerName = $view->controllerName;
        }

        if (empty($params)) {
            $params = array();
        }
        if (!empty($view->__SHOWBYIFRAME__)) {
            $params['__SHOWBYIFRAME__'] = $view->__SHOWBYIFRAME__;
        }

        if (!empty($params)) {
            $params = http_build_query($params);
        }

        if (empty($actionName)) {
            $url = $baseUrl . $moduleName . '/' . $controllerName;
        } else {
            $url = $baseUrl . $moduleName . '/' . $controllerName . '/' . $actionName;
        }
        if (!empty($params)) {
            $url = $url . '?' . $params;
        }
        return $url;
    }

    private static function methodToRouter($name)
    {
        $name = str_ireplace("\\", '-', $name);
        $name = preg_replace("/([a-z0-9])([A-Z])/", "$1-$2", trim($name));
        return strtolower($name);
    }

    private static function includeControllerFiles($dirpath)
    {
        // $arrayList = array(
        // '/home/wwwroot/webcms/apps/backend/submodules/goods/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/lottery/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/mail/controllers',
        // //'/home/wwwroot/webcms/apps/backend/submodules/member/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/Member/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/payment/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/points/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/shop4b2c/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/sms/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/system/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/tencent/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/message/controllers',
        // '/home/wwwroot/webcms/apps/backend/submodules/weixin/controllers'
        // );
        // if (in_array($dirpath, $arrayList)) {
        // return;
        // }
        // if (in_array($dirpath, $arrayList)) {
        // $files = glob("{$dirpath}/*Controller.php");
        // $fileList = array_merge($fileList,$files);
        // foreach ($files as $file) {
        // include_once $file;
        // }
        // print_r($files);
        // ob_flush();
        // }
        $files = glob("{$dirpath}/*Controller.php");
        foreach ($files as $file) {
            include_once $file;
        }
        $diritem = new \DirectoryIterator($dirpath);

        foreach ($diritem as $item) {
            if ($item->isDot() || $item->isFile()) {
                continue;
            }
            $subdirpath = $item->getRealPath();
            self::includeControllerFiles($subdirpath);
        }
    }
}
