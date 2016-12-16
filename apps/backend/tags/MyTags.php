<?php
namespace App\Backend\Tags;

use App\Backend\Submodules\System\Models\Menu;

class MyTags extends \Phalcon\Tag
{

    /**
     *
     * @return string
     */
    static public function showPrivilege($menu_list, $operation_list)
    {
        $privList = array();
        /* 获取菜单数据 */
        $modelMenu = new Menu();
        die('showPrivilege');
        $menu_priv_arr = $modelMenu->getPrivilege($menu_list);
        $privList['菜单设置'] = array(
            'values' => $menu_priv_arr,
            'field' => 'menu_list'
        );
        /* 获取操作数据 */
        $operation_priv_arr = self::getPrivilege($operation_list);
        $privList['操作设置'] = array(
            'values' => $operation_priv_arr,
            'field' => 'operation_list'
        );
        return $privList;
    }

    private static function getPrivilege($operation_list)
    {
        $moduleName = "admin";
        $resources = self::getList($moduleName);
        $resources = $resources[$moduleName];
        
        /* 获取权限的分组数据 */
        $priv_arr = array();
        foreach (array_keys($resources) as $rows) {
            $infoArr = explode("||", $rows);
            $priv_arr[$rows] = array(
                'name' => $infoArr[1],
                'relevance' => "",
                'method' => "",
                'key' => $infoArr[0]
            );
        }
        
        /* 按权限组查询底级的权限名称 */
        foreach ($resources as $key => $item) {
            foreach ($item as $priv) {
                $priv['relevance'] = "";
                $priv_arr[$key]["priv"][$priv['key']] = array(
                    'name' => $priv['name'],
                    'relevance' => $priv['relevance'],
                    'method' => $priv['method'],
                    'key' => $priv['key']
                );
            }
        }
        
        // 将同一组的权限使用 "," 连接起来，供JS全选
        foreach ($priv_arr as $action_id => $action_group) {
            $priv_arr[$action_id]['priv_list'] = join(',', @array_keys($action_group['priv']));
            foreach ($action_group['priv'] as $key => $val) {
                $priv_arr[$action_id]['priv'][$key]['cando'] = in_array($key, $operation_list) ? 1 : 0;
            }
            // 去掉错误模块
            $infoArr = explode("||", $action_id);
            if (in_array($infoArr[0], array(
                "admin_error",
                "admin_form",
                "admin_index"
            ))) {
                unset($priv_arr[$action_id]);
            }
        }
        ksort($priv_arr);
        return $priv_arr;
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
        
        self::includeControllerFiles($dirbasepath);
        
        $reader = new \Phalcon\Annotations\Adapter\Memory();
        
        foreach (get_declared_classes() as $class) {
            
            if (is_subclass_of($class, 'App\Backend\Controllers\ControllerBase') && substr($class, - 10) == 'Controller') {
                
                $c = str_ireplace("App\\Backend\\Controllers\\", 'Admin_', $class);
                $c = substr($c, 0, strpos($c, "Controller"));
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
                                    if (! empty($titleAnnotations) && key_exists("name", $titleAnnotations[0])) {
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
                                                    if (! empty($titleAnnotations) && key_exists("name", $titleAnnotations[0])) {
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
        return $resources;
    }

    private static function methodToRouter($name)
    {
        $name = str_ireplace("\\", '-', $name);
        $name = preg_replace("/([a-z0-9])([A-Z])/", "$1-$2", trim($name));
        return strtolower($name);
    }

    private static function includeControllerFiles($dirpath)
    {
//         $arrayList = array(
//             '/home/wwwroot/webcms/apps/backend/submodules/goods/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/lottery/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/mail/controllers',
//             //'/home/wwwroot/webcms/apps/backend/submodules/member/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/Member/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/payment/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/points/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/shop4b2c/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/sms/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/system/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/tencent/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/message/controllers',
//             '/home/wwwroot/webcms/apps/backend/submodules/weixin/controllers'
//         );
//         if (in_array($dirpath, $arrayList)) {
//            return; 
//         }
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