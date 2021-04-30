<?php

namespace App\Backend\Submodules\Company\Controllers;

use App\Backend\Submodules\Company\Models\ProjectUser;
use App\Backend\Submodules\Company\Models\Project;
use App\Backend\Submodules\Backend\Models\User;

/**
 * @title({name="项目用户管理"})
 *
 * @name 项目用户管理
 */
class ProjectuserController extends \App\Backend\Controllers\FormController
{
    private $modelProjectUser;
    private $modelProject;
    private $modelUser;

    public function initialize()
    {
        $this->modelProjectUser = new ProjectUser();
        $this->modelProject = new Project();
        $this->modelUser = new User();
        
        $this->projectList = $this->modelProject->getAll();
        $this->userList = $this->modelUser->getAll();
        parent::initialize();
    }

    private $projectList = null;
    private $userList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['project_id'] = array(
            'name' => '项目ID',
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
                'items' => $this->projectList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->projectList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->projectList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['user_id'] = array(
            'name' => '用户ID',
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
                'items' => $this->userList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->userList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->userList
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
