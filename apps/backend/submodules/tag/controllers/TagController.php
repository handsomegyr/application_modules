<?php

namespace App\Backend\Submodules\Tag\Controllers;

use App\Backend\Submodules\Tag\Models\Tag;
use App\Backend\Submodules\Tag\Models\Category;

/**
 * @title({name="标签"})
 *
 * @name 标签
 */
class TagController extends \App\Backend\Controllers\FormController
{
    private $modelTag;

    private $modelCategory;

    public function initialize()
    {
        $this->modelTag = new Tag();
        $this->modelCategory = new Category();

        $this->categoryList = $this->modelCategory->getAll();
        parent::initialize();
    }
    private $categoryList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['category'] = array(
            'name' => '标签分类码',
            'data' => array(
                'type' => 'string',
                'length' => 30,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->categoryList
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['name'] = array(
            'name' => '标签',
            'data' => array(
                'type' => 'string',
                'length' => 190,
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
        return '标签';
    }

    protected function getModel()
    {
        return $this->modelTag;
    }
}
