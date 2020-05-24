<?php

namespace App\Backend\Submodules\Database\Controllers;

use App\Backend\Submodules\Database\Models\Plugin;

/**
 * @title({name="插件管理"})
 *
 * @name 插件管理
 */
class PluginController extends \App\Backend\Controllers\FormController
{
    private $modelPlugin;

    public function initialize()
    {
        $this->modelPlugin = new Plugin();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['name'] = array(
            'name' => '插件名',
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
            'name' => '插件介绍',
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

        return $schemas;
    }

    protected function getName()
    {
        return '插件管理';
    }

    protected function getModel()
    {
        return $this->modelPlugin;
    }
}
