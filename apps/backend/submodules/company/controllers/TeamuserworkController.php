<?php

namespace App\Backend\Submodules\Company\Controllers;

use App\Backend\Submodules\Company\Models\TeamUserWork;
use App\Backend\Submodules\Company\Models\Team;
use App\Backend\Submodules\Backend\Models\User;
use App\Backend\Submodules\Company\Models\Project;

/**
 * @title({name="团队用户工作管理"})
 *
 * @name 团队用户工作管理
 */
class TeamuserworkController extends \App\Backend\Controllers\FormController
{
    private $modelTeamUserWork;
    private $modelTeam;
    private $modelUser;
    private $modelProject;

    public function initialize()
    {
        $this->modelTeamUserWork = new TeamUserWork();
        $this->modelTeam = new Team();
        $this->modelUser = new User();
        $this->modelProject = new Project();

        $this->projectList = $this->modelProject->getAll();
        $this->teamList = $this->modelTeam->getAll();
        $this->userList = $this->modelUser->getAll();
        parent::initialize();
    }

    private $projectList = null;
    private $teamList = null;
    private $userList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['team_id'] = array(
            'name' => '团队ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->teamList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->teamList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->teamList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
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
        $schemas['project_id'] = array(
            'name' => '项目ID',
            'data' => array(
                'type' => 'string',
                'length' => 24,
                'defaultValue' => ''
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
        $schemas['work_users'] = array(
            'name' => '工作相关人员信息',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '多个用逗号间隔',
                // 'extensionSettings' => function ($column, $Grid) {
                //     $settings = array();
                //     $row = $column->getRow();
                //     if (empty($row->_id)) {
                //         // 新增的时候不显示
                //         $settings['is_show'] = false;
                //     }
                //     return $settings;
                // }
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                // 'is_editable' => Admin::user()->isRole('administrator')
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        //工作状态:0开发中,1已完成,2已暂停,3已取消
        $workStatusOptions = array();
        $workStatusOptions[0] = '开发中';
        $workStatusOptions[1] = '已完成';
        $workStatusOptions[2] = '已暂停';
        $workStatusOptions[3] = '已取消';
        $schemas['work_status'] = array(
            'name' => '工作状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $workStatusOptions
            ),
            'list' => array(
                'is_show' => true,
                'render' => '',
                'items' => $workStatusOptions,
                'editable_type' => 'select',
                // 'is_editable' => Admin::user()->isRole('administrator')
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'select',
                'items' => $workStatusOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['schedule_start_time'] = array(
            'name' => '计划开始时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        $schemas['schedule_end_time'] = array(
            'name' => '计划结束时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime(time() + 5 * 3600 * 24)
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        $schemas['work_description'] = array(
            'name' => '工作内容',
            'data' => array(
                'type' => 'string',
                'length' => 1024,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
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
        $schemas['work_complete_time'] = array(
            'name' => '完成时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime(time() + 10 * 3600 * 24)
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        $schemas['work_complete_description'] = array(
            'name' => '完成情况',
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
        $schemas['ext_description'] = array(
            'name' => '额外说明',
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
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{}'
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
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '团队用户工作管理';
    }

    protected function getModel()
    {
        return $this->modelTeamUserWork;
    }
}
