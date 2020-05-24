<?php

namespace App\Backend\Submodules\Database\Controllers;

use App\Backend\Submodules\Database\Models\Project\Collection\Mapping;
use App\Backend\Submodules\Company\Models\Project;
use App\Backend\Submodules\Database\Models\Project as DBProject;

/**
 * @title({name="表映射管理"})
 *
 * @name 表映射管理
 */
class ProjectcollectionmappingController extends \App\Backend\Controllers\FormController
{
    private $modelDbProject;
    private $modelProject;
    private $modelMapping;

    public function initialize()
    {
        $this->modelDbProject = new DBProject();
        $this->modelProject = new Project();
        $this->modelMapping = new Mapping();
        $this->projectList4Company = $this->modelProject->getAll();
        $this->projectList4Db = $this->modelDbProject->getAll();
        parent::initialize();
    }

    private $projectList4Company = null;
    private $projectList4Db = null;

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
        $schemas['collection_id'] = array(
            'name' => '所属数据库表',
            'data' => array(
                'type' => 'string',
                'length' => 255,
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
        $schemas['database'] = array(
            'name' => '数据库名',
            'data' => array(
                'type' => 'string',
                'length' => 255,
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
        $schemas['cluster'] = array(
            'name' => '集群名',
            'data' => array(
                'type' => 'string',
                'length' => 30,
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
        $schemas['active'] = array(
            'name' => '是否启用',
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
        return '表映射管理';
    }

    protected function getModel()
    {
        return $this->modelMapping;
    }
}
