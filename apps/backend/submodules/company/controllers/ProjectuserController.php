<?php

namespace App\Backend\Submodules\Company\Controllers;

use App\Backend\Submodules\Company\Models\ProjectUser;

/**
 * @title({name="项目用户管理"})
 *
 * @name 项目用户管理
 */
class ProjectuserController extends \App\Backend\Controllers\FormController
{
    private $modelProjectUser;

    public function initialize()
    {
        $this->modelProjectUser = new ProjectUser();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['project_id'] = array(
            'name' => '项目ID',
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
        $schemas['user_id'] = array(
            'name' => '用户ID',
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

        return $schemas;
    }

    protected function getName()
    {
        return '项目用户管理';
    }

    protected function getModel()
    {
        return $this->modelProjectUser;
    }
}
