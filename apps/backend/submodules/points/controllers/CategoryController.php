<?php
namespace Webcms\Backend\Controllers\Points;

use Webcms\Backend\Models\Points\Category;

/**
 * @title({name="积分分类管理"})
 *
 * @name 积分分类管理
 */
class CategoryController extends \Webcms\Backend\Controllers\FormController
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
                'required' => 1
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
            'name' => '分类名',
            'data' => array(
                'type' => 'string',
                'length' => 30
            ),
            'validation' => array(
                'required' => 1
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
        return '积分分类';
    }

    protected function getModel()
    {
        return $this->modelCategory;
    }
}