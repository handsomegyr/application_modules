<?php

namespace App\Backend\Submodules\Database\Controllers;

use App\Backend\Submodules\Database\Models\Project as DBProject;
use App\Backend\Submodules\Company\Models\Project;

/**
 * @title({name="数据库管理"})
 *
 * @name 数据库管理
 */
class ProjectController extends \App\Backend\Controllers\FormController
{
    private $modelDbProject;
    private $modelProject;

    public function initialize()
    {
        $this->modelDbProject = new DBProject();
        $this->modelProject = new Project();
        $this->projectList4Company = $this->modelProject->getAll();
        parent::initialize();
    }

    private $projectList4Company = null;

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
        $schemas['name'] = array(
            'name' => '数据库名',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['sn'] = array(
            'name' => '密钥',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['desc'] = array(
            'name' => '介绍',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['isSystem'] = array(
            'name' => '是否是系统用',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '1',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '数据库管理';
    }

    protected function getModel()
    {
        return $this->modelDbProject;
    }
}
