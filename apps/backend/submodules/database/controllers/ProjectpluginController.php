<?php

namespace App\Backend\Submodules\Database\Controllers;

use App\Backend\Submodules\Database\Models\Project\Plugin as ProjectPlugin;
use App\Backend\Submodules\Company\Models\Project;
use App\Backend\Submodules\Database\Models\Project as DBProject;
use App\Backend\Submodules\Database\Models\Plugin;

/**
 * @title({name="数据库插件对应管理"})
 *
 * @name 数据库插件对应管理
 */
class ProjectpluginController extends \App\Backend\Controllers\FormController
{
    private $modelDbProject;
    private $modelProject;
    private $modelProjectPlugin;
    private $modelPlugin;

    public function initialize()
    {
        $this->modelDbProject = new DBProject();
        $this->modelProject = new Project();
        $this->modelPlugin = new Plugin();
        $this->modelProjectPlugin = new ProjectPlugin();
        $this->projectList4Company = $this->modelProject->getAll();
        $this->projectList4Db = $this->modelDbProject->getAll();
        $this->pluginList = $this->modelPlugin->getAll();
        parent::initialize();
    }

    private $projectList4Company = null;
    private $projectList4Db = null;
    private $pluginList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['company_project_id'] = array(
            'name' => '所属项目',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->projectList4Company
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->projectList4Company
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->projectList4Company
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['project_id'] = array(
            'name' => '所属数据库',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->projectList4Db
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->projectList4Db
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->projectList4Db
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['plugin_id'] = array(
            'name' => '所属插件',            
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->pluginList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->pluginList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->pluginList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        return $schemas;
    }

    protected function getName()
    {
        return '数据库插件对应管理';
    }

    protected function getModel()
    {
        return $this->modelProjectPlugin;
    }
}
