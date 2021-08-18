<?php

namespace App\Backend\Submodules\Company\Controllers;

use App\Backend\Submodules\Company\Models\TeamUser;
use App\Backend\Submodules\Company\Models\Team;
use App\Backend\Submodules\Backend\Models\User;

/**
 * @title({name="团队用户管理"})
 *
 * @name 团队用户管理
 */
class TeamuserController extends \App\Backend\Controllers\FormController
{
    private $modelTeamUser;
    private $modelTeam;
    private $modelUser;

    public function initialize()
    {
        $this->modelTeamUser = new TeamUser();
        $this->modelTeam = new Team();
        $this->modelUser = new User();

        $this->teamList = $this->modelTeam->getAll();
        $this->userList = $this->modelUser->getAll();

        parent::initialize();
    }

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
        $schemas['is_team_manager'] = array(
            'name' => '是否是团队负责人',
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
        return '团队用户管理';
    }

    protected function getModel()
    {
        return $this->modelTeamUser;
    }
}
