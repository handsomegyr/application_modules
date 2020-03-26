<?php

namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\CodeType;

/**
 * @title({name="code码展示类型"})
 *
 * @name code码展示类型
 */
class CodetypeController extends \App\Backend\Controllers\FormController
{

    private $modelCodeType;

    public function initialize()
    {
        $this->modelCodeType = new CodeType();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['code_type'] = array(
            'name' => '展示类型值',
            'data' => array(
                'type' => 'string',
                'length' => 16,
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
        $schemas['name'] = array(
            'name' => '展示类型名称',
            'data' => array(
                'type' => 'string',
                'length' => 30,
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

        return $schemas;
    }

    protected function getName()
    {
        return 'code码展示类型';
    }

    protected function getModel()
    {
        return $this->modelCodeType;
    }
}
