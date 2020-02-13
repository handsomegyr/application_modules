<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\ReplyMsg\Type;

/**
 * @title({name="回复消息类型"})
 *
 * @name 回复消息类型
 */
class ReplymsgtypeController extends \App\Backend\Controllers\FormController
{
    private $modelType;

    public function initialize()
    {
        $this->modelType = new Type();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['name'] = array(
            'name' => '消息类型名',
            'data' => array(
                'type' => 'string',
                'length' => 20,
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
            'name' => '消息类型值',
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
        return '回复消息类型';
    }

    protected function getModel()
    {
        return $this->modelType;
    }
}
