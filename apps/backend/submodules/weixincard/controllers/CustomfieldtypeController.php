<?php

namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\CustomFieldType;

/**
 * @title({name="会员信息卡类型"})
 *
 * @name 会员信息卡类型
 */
class CustomfieldtypeController extends \App\Backend\Controllers\FormController
{

    private $modelCustomFieldType;

    public function initialize()
    {
        $this->modelCustomFieldType = new CustomFieldType();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();

        $schemas['type'] = array(
            'name' => '类型值',
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
        $schemas['name'] = array(
            'name' => '类型名称',
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
        return '会员信息卡类型';
    }

    protected function getModel()
    {
        return $this->modelCustomFieldType;
    }
}
