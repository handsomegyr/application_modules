<?php

namespace App\Backend\Submodules\Points\Controllers;

use App\Backend\Submodules\Points\Models\Rule;
use App\Backend\Submodules\Points\Models\Category;

/**
 * @title({name="积分规则"})
 *
 * @name 积分规则
 */
class RuleController extends \App\Backend\Controllers\FormController
{

    private $modelRule;

    private $modelCategory;

    public function initialize()
    {
        $this->modelRule = new Rule();
        $this->modelCategory = new Category();
        $this->categoryList = $this->modelCategory->getAll();
        parent::initialize();
    }
    private $categoryList = null;

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['category'] = array(
            'name' => '积分分类',
            'data' => array(
                'type' => 'integer',
                'length' => 1
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
        $schemas['code'] = array(
            'name' => '规则码',
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['item'] = array(
            'name' => '项目',
            'data' => array(
                'type' => 'string',
                'length' => 100
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['item_category'] = array(
            'name' => '项目分类',
            'data' => array(
                'type' => 'string',
                'length' => 100
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
        $schemas['points'] = array(
            'name' => '获得积分',
            'data' => array(
                'type' => 'integer',
                'length' => 11
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['desc'] = array(
            'name' => '描述',
            'data' => array(
                'type' => 'string',
                'length' => 255
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
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '积分规则';
    }

    protected function getModel()
    {
        return $this->modelRule;
    }
}
