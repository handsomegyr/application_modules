<?php

namespace App\Backend\Submodules\Points\Controllers;

use App\Backend\Submodules\Points\Models\Category;

/**
 * @title({name="积分分类"})
 *
 * @name 积分分类
 */
class CategoryController extends \App\Backend\Controllers\FormController
{

    private $modelCategory;

    public function initialize()
    {
        $this->modelCategory = new Category();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['code'] = array(
            'name' => '分类值',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['name'] = array(
            'name' => '分类名称',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['sort'] = array(
            'name' => '排序',
            'data' => array(
                'type' => 'integer',
                'length' => 1
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
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '积分分类';
    }

    protected function getModel()
    {
        return $this->modelCategory;
    }
}
