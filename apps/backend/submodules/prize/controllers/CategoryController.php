<?php
namespace App\Backend\Submodules\Prize\Controllers;

use App\Backend\Submodules\Prize\Models\Category;

/**
 * @title({name="邀请管理"})
 *
 * @name 邀请管理
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
            'name' => '分类编码',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
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
                'is_show' => false
            )
        );
        
        $schemas['name'] = array(
            'name' => '分类名称',
            'data' => array(
                'type' => 'string',
                'length' => '30'
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
                'is_show' => false
            )
        );
        return $schemas;
    }

    protected function getName()
    {
        return '奖品分类';
    }

    protected function getModel()
    {
        return $this->modelCategory;
    }
}