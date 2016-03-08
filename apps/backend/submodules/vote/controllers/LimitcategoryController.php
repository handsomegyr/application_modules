<?php
namespace Webcms\Backend\Controllers\Vote;

use Webcms\Backend\Models\Vote\LimitCategory;

/**
 * @title({name="投票限制类别管理"})
 *
 * @name 投票限制类别管理
 */
class LimitcategoryController extends \Webcms\Backend\Controllers\FormController
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
                'is_show' => false
            )
        );
        $schemas['name'] = array(
            'name' => '限制名',
            'data' => array(
                'type' => 'string',
                'length' => 30
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
        return '投票限制类别';
    }

    protected function getModel()
    {
        return $this->modelLimitCategory;
    }
}