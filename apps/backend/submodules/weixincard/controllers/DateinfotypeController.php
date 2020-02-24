<?php

namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\DateInfoType;

/**
 * @title({name="使用时间类型"})
 *
 * @name 使用时间类型
 */
class DateinfotypeController extends \App\Backend\Controllers\FormController
{

    private $modelDateInfoType;

    public function initialize()
    {
        $this->modelDateInfoType = new DateInfoType();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();

        $schemas['code'] = array(
            'name' => '使用时间的类型值',
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
            'name' => '使用时间的类型名',
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
        return '使用时间类型';
    }

    protected function getModel()
    {
        return $this->modelDateInfoType;
    }
}
