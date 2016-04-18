<?php
namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Submodules\Goods\Models\Type;
use App\Backend\Submodules\Goods\Models\Attribute;
use App\Backend\Submodules\Goods\Models\AttributeValue;

/**
 * @title({name="商品属性值表管理"})
 *
 * @name 商品属性值表管理
 */
class AttributevalueController extends \App\Backend\Controllers\FormController
{

    private $modelType;

    private $modelAttribute;

    private $modelAttributeValue;

    public function initialize()
    {
        $this->modelType = new Type();
        $this->modelAttribute = new Attribute();
        $this->modelAttributeValue = new AttributeValue();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['name'] = array(
            'name' => '属性可选值',
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
        $schemas['attr_id'] = array(
            'name' => '所属属性',
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
                    return $this->modelAttribute->getAll();
                }
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
        return '商品属性值表';
    }

    protected function getModel()
    {
        return $this->modelAttributeValue;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $typeList = $this->modelType->getAll();
        $attrList = $this->modelAttribute->getAll();
        foreach ($list['data'] as &$item) {
            $item['type_id'] = isset($typeList[$item['type_id']]) ? $typeList[$item['type_id']] : "--";
            $item['attr_id'] = isset($attrList[$item['attr_id']]) ? $attrList[$item['attr_id']] : "--";
        }
        
        return $list;
    }
}