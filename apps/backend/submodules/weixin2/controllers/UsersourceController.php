<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\User\Source;

/**
 * @title({name="用户渠道来源"})
 *
 * @name 用户渠道来源
 */
class UsersourceController extends \App\Backend\Controllers\FormController
{
    private $modelSource;

    public function initialize()
    {
        $this->modelSource = new Source();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['name'] = array(
            'name' => '用户渠道来源名',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
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
        $schemas['value'] = array(
            'name' => '用户渠道来源值',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
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
        return '用户渠道来源';
    }

    protected function getModel()
    {
        return $this->modelSource;
    }
}
