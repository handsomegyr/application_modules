<?php
namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Submodules\Goods\Models\Category;
use App\Backend\Submodules\Goods\Models\Attribute;
use App\Backend\Submodules\Goods\Models\Type;

/**
 * @title({name="商品属性管理"})
 *
 * @name 商品属性管理
 */
class AttributeController extends \App\Backend\Controllers\FormController
{

    private $modelCategory;

    private $modelAttribute;

    private $modelType;

    public function initialize()
    {
        $this->modelCategory = new Category();
        $this->modelAttribute = new Attribute();
        $this->modelType = new Type();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['name'] = array(
            'name' => '属性名称',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['type_id'] = array(
            'name' => '所属类型',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelType->getAll();
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['attr_value'] = array(
            'name' => '属性可选值',
            'data' => array(
                'type' => 'string',
                'length' => 0
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
        $schemas['is_show'] = array(
            'name' => '是否显示',
            'data' => array(
                'type' => 'boolean',
                'length' => '1',
                'defaultValue' => false
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['sort'] = array(
            'name' => '排序',
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '商品属性';
    }

    protected function getModel()
    {
        return $this->modelAttribute;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $typeList = $this->modelType->getAll();
        foreach ($list['data'] as &$item) {
            $item['type_id'] = isset($typeList[$item['type_id']]) ? $typeList[$item['type_id']] : "--";
        }
        return $list;
    }
}