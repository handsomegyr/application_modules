<?php

namespace App\Backend\Submodules\System\Controllers;

use App\Backend\Submodules\System\Models\Resource;

/**
 * @title({name="资源管理"})
 *
 * @name 资源管理
 */
class ResourceController extends \App\Backend\Controllers\FormController
{

    private $modelResource;

    public function initialize()
    {
        $this->modelResource = new Resource();
        parent::initialize();
    }

    protected function getHeaderTools2($tools)
    {
        $tools['resourcecreate'] = array(
            'title' => '生成资源',
            'action' => 'create',
            'is_show' => true,
            'is_export' => false,
            'process_without_modal' => true,
            'icon' => '',
        );
        return $tools;
    }

    /**
     * @title({name="生成"})
     *
     * @name 生成
     */
    public function createAction()
    {
        // http://www.applicationmodule.com/admin/system/resource/create
        try {
            $module = "admin";
            $moduleName = "后台管理";
            $resourceList = \App\Backend\Tags\MyTags::getList($module);
            if (empty($resourceList)) {
                throw new \Exception('没有任何资源1');
            }
            if (empty($resourceList[$module])) {
                throw new \Exception('没有任何资源2');
            }
            $resources = $resourceList[$module];

            $this->modelResource->remove(array());
            foreach ($resources as $key => $items) {
                // $key : admin_form||表管理
                $controllerArr = explode('||', $key);
                $controllerArr[0] = explode('_', $controllerArr[0]);
                foreach ($items as $resource) {
                    // [name] => 数据导出
                    // [method] => export
                    // [key] => admin_form::export
                    $datas = array();
                    $datas['module'] = trim($module);
                    $datas['module_name'] = trim($moduleName);
                    $datas['controller'] = trim($controllerArr[0][1]);
                    $datas['controller_name'] = trim($controllerArr[1]);
                    $datas['action'] = trim($resource['method']);
                    $datas['action_name'] = trim($resource['name']);
                    $datas['name'] = $datas['controller_name'] . "之" . $datas['action_name'];
                    $this->modelResource->insert($datas);
                }
            }
            $this->makeJsonResult(array('then' => array('action' => 'refresh')), '生成资源成功');
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getSchemas2($schemas)
    {
        $schemas['name'] = array(
            'name' => '资源名',
            'data' => array(
                'type' => 'string',
                'length' => '255'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true,
                'placeholder' => '资源名...'
            )
        );

        $schemas['module'] = array(
            'name' => '模块',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true,
                'placeholder' => '模块...'
            )
        );

        $schemas['module_name'] = array(
            'name' => '模块名',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true,
                'placeholder' => '模块名...'
            )
        );

        $schemas['controller'] = array(
            'name' => '控制器',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true,
                'placeholder' => '控制器...'
            )
        );

        $schemas['controller_name'] = array(
            'name' => '控制器名',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true,
                'placeholder' => '控制器名...'
            )
        );

        $schemas['action'] = array(
            'name' => '动作',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true,
                'placeholder' => '动作...'
            )
        );

        $schemas['action_name'] = array(
            'name' => '动作名',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true,
                'placeholder' => '动作名...'
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '资源';
    }

    protected function getModel()
    {
        return $this->modelResource;
    }
}
