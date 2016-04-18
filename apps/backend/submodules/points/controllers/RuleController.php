<?php
namespace App\Backend\Submodules\Points\Controllers;

use App\Backend\Models\Points\Rule;
use App\Backend\Models\Points\Category;

/**
 * @title({name="积分规则管理"})
 *
 * @name 积分规则管理
 */
class RuleController extends \App\Backend\Controllers\FormController
{

    private $modelRule;

    private $modelCategory;

    public function initialize()
    {
        $this->modelRule = new Rule();
        $this->modelCategory = new Category();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['code'] = array(
            'name' => '规则码',
            'data' => array(
                'type' => 'string',
                'length' => 10
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
        $schemas['item'] = array(
            'name' => '项目',
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
        $schemas['item_category'] = array(
            'name' => '项目分类',
            'data' => array(
                'type' => 'string',
                'length' => 30
            ),
            'validation' => array(
                'required' => 0
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
        $schemas['category'] = array(
            'name' => '积分分类',
            'data' => array(
                'type' => 'integer',
                'length' => '1'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelCategory->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'category_name'
            ),
            'search' => array(
                'is_show' => false
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
                'is_show' => false
            )
        );
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 0
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
        return '积分规则';
    }

    protected function getModel()
    {
        return $this->modelRule;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $categoryList = $this->modelCategory->getAll();
        foreach ($list['data'] as &$item) {
            $item['category_name'] = isset($categoryList[$item['category']]) ? $categoryList[$item['category']] : "--";
        }
        
        return $list;
    }
}