<?php

namespace App\Backend\Submodules\Company\Controllers;

use App\Backend\Submodules\Company\Models\Team;

/**
 * @title({name="团队管理"})
 *
 * @name 团队管理
 */
class TeamController extends \App\Backend\Controllers\FormController
{
    private $modelTeam;

    public function initialize()
    {
        $this->modelTeam = new Team();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['name'] = array(
            'name' => '团队名称',
            'data' => array(
                'type' => 'string',
                'length' => 100,
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
        return '团队管理';
    }

    protected function getModel()
    {
        return $this->modelTeam;
    }
}
