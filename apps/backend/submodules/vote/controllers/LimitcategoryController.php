<?php

namespace App\Backend\Submodules\Vote\Controllers;

use App\Backend\Submodules\Vote\Models\LimitCategory;

/**
 * @title({name="投票限制类别管理"})
 *
 * @name 投票限制类别管理
 */
class LimitcategoryController extends \App\Backend\Controllers\FormController
{

    private $modelLimitCategory;

    public function initialize()
    {
        $this->modelLimitCategory = new LimitCategory();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();

        $schemas['category'] = array(
            'name' => '限制类别值',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
            'name' => '名称',
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
        return '投票限制类别';
    }

    protected function getModel()
    {
        return $this->modelLimitCategory;
    }
}
