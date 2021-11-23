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
            'items' => %s,
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

    protected function getSchemas2($schemas)
    {
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

    protected $common_model_mysql_file_template = <<<'EOD'
<?php

namespace App\Common\Models\#_namespacename_#\Mysql;

use App\Common\Models\Base\Mysql\Base;

class #_model_# extends Base
{
    /**
     * #_tablecomment_#管理
     * This model is mapped to the table #_tablename_#
     */
    public function getSource()
    {
        return '#_tablename_#';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        #_schemas_#
        return $data;
    }
}
EOD;

    protected $common_model_file_template = <<<'EOD'
<?php

namespace App\Common\Models\#_namespacename_#;

use App\Common\Models\Base\Base;

class #_model_# extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\#_namespacename_#\Mysql\#_model_#());
    }
}
EOD;

    protected $backend_model_file_template = <<<'EOD'
<?php

namespace App\Backend\Submodules\#_namespacename_#\Models;

class #_model_# extends \App\Common\Models\#_namespacename_#\#_model_#
{

    use \App\Backend\Models\Base;
}
EOD;

    protected $model_file_template = <<<'EOD'
<?php

namespace App\#_namespacename_#\Models;

class #_model_# extends \App\Common\Models\#_namespacename_#\#_model_#
{
}
EOD;

    protected $reorganize_shema_template = <<<'EOD'
$data['#_field_#'] = $this->#_fieldop_#($data['#_field_#']);
EOD;

    /**
     * @title({name="创建实体schema"})
     *
     * @name 创建实体schema
     */
    public function getalltablesAction()
    {
        // http://www.myapplicationmodule.com/admin/builder/getalltables
        try {
            $this->view->disable();
            $tables = $this->getAllTables();
            $this->makeJsonResult("", "create OK", $tables);
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="创建后台菜单"})
     *
     * @name 创建后台菜单
     */
    public function createmenuAction()
    {
        // http://www.myapplicationmodule.com/admin/builder/createmenu?settings=App\Backend\Submodules\Weixin2\Settings\Menu
        // http://www.myapplicationmodule.com/admin/builder/createmenu?settings=App\Backend\Submodules\Qyweixin\Settings\Menu
        // http://www.myapplicationmodule.com/admin/builder/createmenu?settings=App\Backend\Submodules\Company\Settings\Menu
        // http://www.myapplicationmodule.com/admin/builder/createmenu?settings=App\Backend\Submodules\Database\Settings\Menu
        try {
            $this->view->disable();
            resetTimeMemLimit();
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

            $this->makeJsonResult("", "create OK", $tree);
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
        // http://www.myapplicationmodule.com/admin/builder/createschema?model=\App\Backend\Submodules\Weixin2\Models\Language
        try {
            $this->view->disable();
            resetTimeMemLimit();
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
        // http://www.myapplicationmodule.com/admin/builder/createfile?model=\App\Backend\Submodules\Weixin2\Models\Language&title=语言
        try {
            $this->view->disable();
            resetTimeMemLimit();

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
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Weixin2\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Cronjob\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Questionnaire\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Activity\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Alipay\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Prize\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Invitation\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Sign\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Lottery\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Exchange\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Vote\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Store\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Bargain\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Weixincard\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Game\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Points\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Payment\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Banner\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Search\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Qyweixin\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Company\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Database\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Tag\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Lexiangla\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Member\Settings\Menu&is_create_menu=1
        // http://www.myapplicationmodule.com/admin/builder/createmenuwithfiles?settings=App\Backend\Submodules\Task\Settings\Menu&is_create_menu=1
        try {
            $this->view->disable();
            resetTimeMemLimit();

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


    /**
     * @title({name="创建后台菜单和文件"})
     *
     * @name 创建后台菜单和文件
     */
    public function createmodelsAction()
    {
        // http://www.myapplicationmodule.com/admin/builder/createmodels?module=lexiangla
        try {
            $this->view->disable();
            resetTimeMemLimit();

            $module = $this->get('module', '');
            if (empty($module)) {
                throw new \Exception("module is empty");
            }

            $di = \Phalcon\DI::getDefault();
            $db = $di['db'];
            $result = $db->query("SHOW TABLE STATUS LIKE 'i{$module}_%'", array());
            $result->setFetchMode(MYDB_FETCH_ASSOC);
            $tables = $result->fetchAll();
            if (empty($tables)) {
                throw new \Exception("table is no found");
            }
            // print_r($tables);
            // die('xxxxxxxxxx');
            $tmp = \tempnam(\sys_get_temp_dir(), 'zip_');
            $zip = new \ZipArchive();
            $res = $zip->open($tmp, \ZipArchive::CREATE);
            if ($res === true) {
                $namespacename = ucfirst($module);
                // lib\App\Lexiangla\Models
                // lib\App\Common\Models\Lexiangla\Models
                // lib\App\Common\Models\Lexiangla\Mysql
                // lib\App\Backend\Submodules\Lexiangla\Models
                $dir1 = "lib/App/{$namespacename}/Models";
                $dir2 = "lib/App/Common/Models/{$namespacename}/Models";
                $dir3 = "lib/App/Common/Models/{$namespacename}/Mysql";
                $dir4 = "lib/App/Backend/Submodules/{$namespacename}/Models";
                $zip->addEmptyDir($dir1);
                $zip->addEmptyDir($dir2);
                $zip->addEmptyDir($dir3);
                $zip->addEmptyDir($dir4);

                $items = array(
                    array('file_template' => $this->model_file_template, 'dir' => $dir1),
                    array('file_template' => $this->backend_model_file_template, 'dir' => $dir4),
                    array('file_template' => $this->common_model_file_template, 'dir' => $dir2),
                    array('file_template' => $this->common_model_mysql_file_template, 'dir' => $dir3)
                );

                foreach ($tables as $tableInfo) {
                    // [Name] => ilexiangla_department_sync
                    // [Engine] => InnoDB
                    // [Version] => 10
                    // [Row_format] => Compact
                    // [Rows] => 0
                    // [Avg_row_length] => 0
                    // [Data_length] => 16384
                    // [Max_data_length] => 0
                    // [Index_length] => 32768
                    // [Data_free] => 0
                    // [Auto_increment] => 
                    // [Create_time] => 2021-10-14 17:50:47
                    // [Update_time] => 
                    // [Check_time] => 
                    // [Collation] => utf8mb4_unicode_ci
                    // [Checksum] => 
                    // [Create_options] => 
                    // [Comment] => 乐享-通讯录管理-部门同步
                    $table = $tableInfo['Name'];
                    $tableComment = empty($tableInfo['Comment']) ? $table : $tableInfo['Comment'];
                    $model = (str_replace("i{$module}_", '', $table));
                    $model = ucwords($model, '_');
                    $model = (str_replace("_", '', $model));

                    // 获取config内容
                    $fileStr = $this->getReorganizeContent($table);
                    $schemas = "";
                    foreach ($fileStr as $item) {
                        $schemas .= ($item . "\n");
                    }
                    foreach ($items as $item) {
                        $fileStr = str_replace("#_namespacename_#", $namespacename, $item['file_template']);
                        $fileStr = str_replace("#_model_#", $model, $fileStr);
                        $fileStr = str_replace("#_tablename_#", $table, $fileStr);
                        $fileStr = str_replace("#_tablecomment_#", $tableComment, $fileStr);
                        $fileStr = str_replace("#_schemas_#", $schemas, $fileStr);
                        $fileStrRet = array('fileStr' => $fileStr, 'fileName' => $model);

                        $filename = tempnam(sys_get_temp_dir(), 'php_' . uniqid() . "_");
                        $fp = fopen($filename, 'w');
                        fwrite($fp,  $fileStrRet['fileStr']);
                        fclose($fp);
                        $zip->addFile($filename, $item['dir'] . "/" . $fileStrRet['fileName'] . '.php');
                        // unlink($filename);
                    }
                }
            }
            $zip->close();

            ob_end_clean();
            header('Content-type: application/octet-stream;');
            header('Content-Disposition: attachment; filename="' .  $module . '_' . date("YmdHis") . '.zip"');
            header("Content-Length:" . filesize($tmp));
            echo file_get_contents($tmp);
            unlink($tmp);
            exit();
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getAllTables()
    {
        // $tableInfo = DB::select("SHOW FULL COLUMNS FROM {$table}");
        $di = \Phalcon\DI::getDefault();
        $db = $di['db'];
        $result = $db->query("SHOW TABLES", array());
        $result->setFetchMode(MYDB_FETCH_ASSOC);
        $tables = $result->fetchAll();
        $list = array();
        $time = date('Y-m-d H:i:s');
        foreach ($tables as $key => $tableInfo) {
            foreach ($tableInfo as $key2 => $value) {
                $result = $db->query("UPDATE {$value} SET __CREATE_TIME__ = '{$time}',__CREATE_USER_ID__ = '5639d567bbcb269f108b4567',__CREATE_USER_NAME__='admin',__MODIFY_TIME__ = '{$time}',__MODIFY_USER_ID__ = '5639d567bbcb269f108b4567',__MODIFY_USER_NAME__='admin'", array());
                // $result = $db->query("UPDATE {$value} SET __CREATE_USER_ID__ = '5639d567bbcb269f108b4567',__CREATE_USER_NAME__='admin',__MODIFY_USER_ID__ = '5639d567bbcb269f108b4567',__MODIFY_USER_NAME__='admin',__REMOVE_USER_ID__ = '',__REMOVE_USER_NAME__='' where __REMOVED__=0", array());
                // $result = $db->query("UPDATE {$value} SET __CREATE_USER_ID__ = '5639d567bbcb269f108b4567',__CREATE_USER_NAME__='admin',__MODIFY_USER_ID__ = '5639d567bbcb269f108b4567',__MODIFY_USER_NAME__='admin',__REMOVE_USER_ID__ = '5639d567bbcb269f108b4567',__REMOVE_USER_NAME__='admin',__REMOVE_USER_NAME__='admin' where __REMOVED__=1", array());
                $list[] = $value;
            }
        }
        return $list;
    }

    // 获取表的字段
    protected function getFields4Table($table)
    {
        // $tableInfo = DB::select("SHOW FULL COLUMNS FROM {$table}");
        $di = \Phalcon\DI::getDefault();
        $db = $di['db'];
        $result = $db->query("SHOW FULL COLUMNS FROM {$table}", array());
        $result->setFetchMode(MYDB_FETCH_ASSOC);
        $tableInfo = $result->fetchAll();
        return $tableInfo;
    }

    protected function getConfigContent($table)
    {
        $tableInfo = $this->getFields4Table($table);
        return $this->getDisplayColumns($tableInfo);
    }

    protected function getDisplayColumns($tableInfo)
    {
        $columns = array();
        //$columns['tableInfo'] = $tableInfo;
        foreach ($tableInfo as $col) {
            // 某些字段不用输出显示
            if (in_array($col['Field'], array(
                '_id', '__CREATE_TIME__',  '__CREATE_USER_ID__', '__CREATE_USER_NAME__',
                '__MODIFY_TIME__', '__MODIFY_USER_ID__', '__MODIFY_USER_NAME__',
                '__REMOVED__', '__REMOVE_TIME__', '__REMOVE_USER_ID__', '__REMOVE_USER_NAME__'
            ))) {
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

    protected function getReorganizeContent($table)
    {
        $tableInfo = $this->getFields4Table($table);
        return $this->getReorganizeColumns($tableInfo);
    }

    protected function getReorganizeColumns($tableInfo)
    {
        $columns = array();
        foreach ($tableInfo as $col) {
            // 某些字段不用输出显示
            if (in_array($col['Field'], array(
                '_id', '__CREATE_TIME__',  '__CREATE_USER_ID__', '__CREATE_USER_NAME__',
                '__MODIFY_TIME__', '__MODIFY_USER_ID__', '__MODIFY_USER_NAME__',
                '__REMOVED__', '__REMOVE_TIME__', '__REMOVE_USER_ID__', '__REMOVE_USER_NAME__', 'memo'
            ))) {
                continue;
            }

            $field = $col['Field'];
            // text
            if ("text" == $col['Type']) {
                $schemaStr = str_replace("#_field_#", $field, $this->reorganize_shema_template);
                $schemaStr = str_replace("#_fieldop_#", 'changeToArray', $schemaStr);
                $columns[$field] = $schemaStr;
            }
            //tinyint(1) unsigned
            elseif (self::startsWith($col['Type'], "tinyint")) {
                $schemaStr = str_replace("#_field_#", $field, $this->reorganize_shema_template);
                $schemaStr = str_replace("#_fieldop_#", 'changeToBoolean', $schemaStr);
                $columns[$field] = $schemaStr;
            }
            //datetime
            elseif ($col['Type'] == 'datetime') {
                $schemaStr = str_replace("#_field_#", $field, $this->reorganize_shema_template);
                $schemaStr = str_replace("#_fieldop_#", 'changeToValidDate', $schemaStr);
                $columns[$field] = $schemaStr;
            }
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

        $modelMenu = new \App\Backend\Submodules\Backend\Models\Menu();

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
