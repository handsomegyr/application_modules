<?php

namespace App\Backend\Controllers;

/**
 * @title({name="创建中心"})
 *
 * @name 创建中心
 */
class BuilderController extends  \App\Backend\Controllers\ControllerBase
{
    public function initialize()
    {
        parent::initialize();
    }

    protected $shema_template = <<<'EOD'
    $schemas['%s'] = array(
        'name' => '%s',
        'data' => array(
            'type' => '%s',
            'length' => %d,
            'defaultValue' => %s
        ),
        'validation' => array(
            'required' => false
        ),
        'form' => array(
            'input_type' => '%s',
            'is_show' => true,
            'items' => %s
        ),
        'list' => array(
            'is_show' => %s,
            'list_type' => %s,
            'render' => '%s',
        ),
        'search' => array(
            'is_show' => true
        ),
        'export' => array(
            'is_show' => true
        )
    );
EOD;

    protected $file_template = <<<'EOD'
<?php
namespace App\Backend\Submodules\#_namespace_#\Controllers;

use #_namespacename_#\#_model_#;

/**
 * @title({name="#_title_#"})
 *
 * @name #_title_#
 */
class #_controllerName_#Controller extends \App\Backend\Controllers\FormController
{
    private $model#_model_#;

    public function initialize()
    {
        $this->model#_model_# = new #_model_#();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        #_schemas_#
        return $schemas;
    }

    protected function getName()
    {
        return '#_title_#';
    }

    protected function getModel()
    {
        return $this->model#_model_#;
    }    
}
EOD;

    /**
     * @title({name="创建后台菜单"})
     *
     * @name 创建后台菜单
     */
    public function createmenuAction()
    {
        // http://www.applicationmodule.com/admin/builder/createmenu?settings=App\Backend\Submodules\Weixin2\Settings\Menu
        try {
            $this->view->disable();
            $settings = $this->get('settings', '');
            if (empty($settings)) {
                throw new \Exception("菜单settings为空");
            }
            if (!class_exists($settings)) {
                throw new \Exception("菜单settings所对应的类不存在");
            }

            $menuSettings = new $settings();
            $tree = $menuSettings->getSettings();
            $this->createMenuBySettings($tree, true);

            $this->makeJsonResult("", "create OK");
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="创建实体schema"})
     *
     * @name 创建实体schema
     */
    public function createschemaAction()
    {
        // http://www.applicationmodule.com/admin/builder/createschema?model=\App\Backend\Submodules\Weixin2\Models\Language
        try {
            $this->view->disable();
            $model = $this->get('model', '');
            $table = $this->get('table', '');

            if (empty($model)) {
                throw new \Exception("MODEL类名为空");
            }
            if (!class_exists($model)) {
                throw new \Exception("model所对应的类不存在");
            }
            $obj = new $model();
            $table = $obj->getSource();

            if (empty($table)) {
                throw new \Exception("表名为空");
            }

            // 获取config内容
            echo "<pre>";
            $fileStr = $this->getConfigContent($table);
            foreach ($fileStr as  $item) {
                echo $item . "<br/>";
            }
            $this->makeJsonResult("", "create OK");
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="创建实体文件"})
     *
     * @name 创建实体文件
     */
    public function createfileAction()
    {
        // http://www.applicationmodule.com/admin/builder/createfile?model=\App\Backend\Submodules\Weixin2\Models\Language&title=语言
        try {
            $this->view->disable();
            $model = $this->get('model', '');
            $title = $this->get('title', '');
            if (empty($title)) {
                throw new \Exception("title为空");
            }
            if (empty($model)) {
                throw new \Exception("MODEL类名为空");
            }
            if (!class_exists($model)) {
                throw new \Exception("model所对应的类不存在");
            }
            $obj = new $model();
            $table = $obj->getSource();

            if (empty($table)) {
                throw new \Exception("表名为空");
            }
            echo "<pre><br/>";
            $fileStrRet = $this->createFileBySettings($model, $title);
            echo $fileStrRet['fileStr'] . "<br/>";
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }


    /**
     * @title({name="创建后台菜单和文件"})
     *
     * @name 创建后台菜单和文件
     */
    public function createmenuwithfilesAction()
    {
        // http://www.applicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Weixin2\Settings\Menu&is_create_menu=1
        // http://www.applicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Cronjob\Settings\Menu&is_create_menu=1
        // http://www.applicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Questionnaire\Settings\Menu&is_create_menu=1
        // http://www.applicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Activity\Settings\Menu&is_create_menu=1
        // http://www.applicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Alipay\Settings\Menu&is_create_menu=1
        // http://www.applicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Prize\Settings\Menu&is_create_menu=1
        // http://www.applicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Invitation\Settings\Menu&is_create_menu=1

        try {
            $this->view->disable();
            $settings = $this->get('settings', '');
            $is_create_menu = intval($this->get('is_create_menu', 1));

            if (empty($settings)) {
                throw new \Exception("菜单settings为空");
            }
            if (!class_exists($settings)) {
                throw new \Exception("菜单settings所对应的类不存在");
            }

            $menuSettings = new $settings();
            $tree = $menuSettings->getSettings();

            $this->createMenuBySettings($tree, $is_create_menu);

            $tmp = \tempnam(\sys_get_temp_dir(), 'zip_');
            $zip = new \ZipArchive();
            $res = $zip->open($tmp, \ZipArchive::CREATE);
            if ($res === true) {
                foreach ($tree as $item) {
                    if (empty($item['model'])) {
                        continue;
                    }

                    $menu = $item['menu_name'];
                    $model = $item['model'];

                    // 获取config内容
                    $fileStrRet = $this->createFileBySettings($model, $menu, $item);

                    $filename = tempnam(sys_get_temp_dir(), 'php_' . uniqid() . "_");
                    $fp = fopen($filename, 'w');
                    fwrite($fp,  $fileStrRet['fileStr']);
                    fclose($fp);
                    $zip->addFile($filename, $fileStrRet['fileName'] . '.php');
                    $zip->addEmptyDir($fileStrRet['folderName']);
                    // unlink($filename);
                }
            }
            $zip->close();

            ob_end_clean();
            header('Content-type: application/octet-stream;');
            header('Content-Disposition: attachment; filename="' .  'menusettings_' . date("YmdHis") . '.zip"');
            header("Content-Length:" . filesize($tmp));
            echo file_get_contents($tmp);
            unlink($tmp);
            exit();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getConfigContent($table)
    {
        // $tableInfo = DB::select("SHOW FULL COLUMNS FROM {$table}");
        $di = \Phalcon\DI::getDefault();
        $db = $di['db'];
        $result = $db->query("SHOW FULL COLUMNS FROM {$table}", array());
        $result->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $tableInfo = $result->fetchAll();
        return $this->getDisplayColumns($tableInfo);
    }

    protected function getDisplayColumns($tableInfo)
    {
        $columns = array();
        //$columns['tableInfo'] = $tableInfo;
        foreach ($tableInfo as $col) {
            // 某些字段不用输出显示
            if (in_array($col['Field'], array('_id', '__CREATE_TIME__', '__MODIFY_TIME__', '__REMOVED__'))) {
                continue;
            }

            $field = $col['Field'];
            $name = (empty($col['Comment']) ? $col['Field'] : $col['Comment']);

            // 字段类型为text 不用输出显示
            $dataType = "string";
            $dataLength = 255;
            $dataDefaultValue = "''";

            $formInputType = "text";
            $formItems = "''";

            $listIsShow = "true";
            $listListType = "''";
            $listRender = "";
            // text
            if ("text" == $col['Type']) {
                $dataType = "json";
                $dataLength = 1024;
                $formInputType = "textarea";
                $listIsShow = "false";
                //注释上如果有图的字的话 认为是图多张
                if (self::contains($col['Comment'], "图多张")) {
                    $dataType = "multifile";
                    $formInputType = "multipleImage";
                    $listRender = "img";
                }
                //注释上如果是备注
                if (self::contains($col['Comment'], "备注")) {
                    $dataDefaultValue = "'{}'";
                }
            }
            //int(11) unsigne
            elseif (self::startsWith($col['Type'], "int") || self::startsWith($col['Type'], "mediumint") || self::startsWith($col['Type'], "smallint")) {
                $dataType = "integer";
                $dataLength = 11;
                $formInputType = "number";
                $dataDefaultValue = "0";
            }
            //tinyint(1) unsigned
            elseif (self::startsWith($col['Type'], "tinyint")) {
                $dataType = "boolean";
                $dataLength = 1;
                $dataDefaultValue = "false";

                $formInputType = "radio";
                $formItems = '$this->trueOrFalseDatas';
                $listListType = "'1'";
            }
            //datetime
            elseif ($col['Type'] == 'datetime') {

                $dataType = "datetime";
                $dataLength = 19;
                $dataDefaultValue = 'getCurrentTime()';

                $formInputType = "datetimepicker";
            }
            //varchar(255)
            elseif (self::startsWith($col['Type'], "varchar")) {
                if (preg_match('/\d+/', $col['Type'], $arr)) {
                    $dataLength = $arr[0];
                }
                //注释上如果有图的字的话 认为是图片
                if (self::contains($col['Comment'], "图")) {
                    $formInputType = "image";
                    $listRender = "img";
                }
                //注释上如果有文件的字的话 认为是文件
                elseif (self::contains($col['Comment'], "文件")) {
                    $formInputType = "file";
                }
            }

            $schemaStr = sprintf($this->shema_template, $field, $name, $dataType, $dataLength, $dataDefaultValue, $formInputType, $formItems, $listIsShow, $listListType, $listRender);

            $columns[$col['Field']] = $schemaStr;
        }
        return $columns;
    }

    protected function createMenuBySettings($tree, $isCreate = true)
    {
        // 检查数据合法性
        foreach ($tree as $item) {

            if (!isset($item["menu_name"])) {
                throw new \Exception("字段 menu_name 为空");
            }

            $menu_name = $item["menu_name"];

            if (!isset($item["menu_model"])) {
                throw new \Exception("菜单名：{$menu_name}的字段 menu_model 为空");
            }

            if (!isset($item["level"])) {
                throw new \Exception("菜单名：{$menu_name}的字段 level 为空");
            }

            if (!isset($item["icon"])) {
                throw new \Exception("菜单名：{$menu_name}的字段 icon 为空");
            }
        }

        $modelMenu = new \App\Backend\Submodules\System\Models\Menu();

        // 事务处理
        if ($isCreate) {
            // db处理
            $order = 0;
            foreach ($tree as $item) {
                // 没有指定父菜单
                if (empty($item["level"])) {
                    $parent_id = "";
                } else {
                    // 找父菜单
                    $menuModel4Parent = $modelMenu->findOne(array('name' => $item["level"]));
                    if (empty($menuModel4Parent)) {
                        throw new \Exception("菜单名：{$item["level"]}的记录未找到");
                    }
                    $parent_id = $menuModel4Parent['_id'];
                }

                $menuModel = $modelMenu->findOne(array('pid' => $parent_id, 'name' => $item["menu_name"]));

                // 未找到的话
                if (empty($menuModel)) {
                    // 新增
                    $obj = array();
                    $obj['name'] = $item["menu_name"];
                    $obj['pid'] = $parent_id;
                    $obj['icon'] = empty($item["icon"]) ? "" : $item["icon"];
                    if (!empty($item["level"])) {
                        $order++;
                        $obj['show_order'] = $order;
                    }
                    $obj['is_show'] = true;
                    if (empty($item["menu_model"])) {
                        $obj['url'] = "";
                    } else {
                        $obj['url'] = "admin/" . str_replace('-', '/', $item["menu_model"]) . "/list";
                    }
                    $modelMenu->insert($obj);
                }
            }
        }
    }

    protected function createFileBySettings($model, $title, $menu = array())
    {
        $obj = new $model();
        $table = $obj->getSource();
        if (empty($table)) {
            throw new \Exception("model:{$model}的表名字为空");
        }
        $reflectionModel = new \ReflectionClass($model);
        // 获取namespace名字 App\Backend\Submodules\Weixin2\Models
        $namespaceName = $reflectionModel->getNamespaceName();
        $namespace = preg_replace('/App\\\Backend\\\Submodules\\\(.*?)\\\Models/i', '$1', $namespaceName);
        $namespaceArr = explode("\\", $namespace);
        $namespace = $namespaceArr[0];
        // 获取model名字
        $shortName4Class = $reflectionModel->getShortName();
        $modelName = $shortName4Class;
        $controllerName = ucfirst(strtolower($modelName));
        if (!empty($menu)) {
            $title = $menu['menu_name'];
            $menu_model_arr = explode('-', $menu["menu_model"]);
            if (empty($menu_model_arr[1])) {
                throw new \Exception('menu_model:' . $menu["menu_model"] . '格式不正确');
            }
            $controllerName = ucfirst(strtolower($menu_model_arr[1]));
        }

        // 获取config内容
        $fileStr = $this->getConfigContent($table);
        $schemas = "";
        foreach ($fileStr as  $item) {
            $schemas .= ($item . "\n");
        }
        $fileStr = str_replace("#_namespacename_#", $namespaceName, $this->file_template);
        $fileStr = str_replace("#_namespace_#", $namespace, $fileStr);
        $fileStr = str_replace("#_model_#", $modelName, $fileStr);
        $fileStr = str_replace("#_controllerName_#", $controllerName, $fileStr);
        $fileStr = str_replace("#_title_#", $title, $fileStr);
        $fileStr = str_replace("#_schemas_#", $schemas, $fileStr);
        return array('fileStr' => $fileStr, 'fileName' => $controllerName . 'Controller', 'folderName' => strtolower($controllerName));
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}
