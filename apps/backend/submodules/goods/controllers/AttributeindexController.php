<?php
namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Submodules\Goods\Models\Goods;
use App\Backend\Submodules\Goods\Models\GoodsCommon;
use App\Backend\Submodules\Goods\Models\Category;
use App\Backend\Submodules\Goods\Models\Type;
use App\Backend\Submodules\Goods\Models\Attribute;
use App\Backend\Submodules\Goods\Models\AttributeValue;
use App\Backend\Submodules\Goods\Models\AttrIndex;

/**
 * @title({name="商品属性对应管理"})
 *
 * @name 商品属性对应管理
 */
class AttributeindexController extends \App\Backend\Controllers\FormController
{

    private $modelGoods;

    private $modelGoodsCommon;

    private $modelCategory;

    private $modelType;

    private $modelAttribute;

    private $modelAttributeValue;

    private $modelAttrIndex;

    public function initialize()
    {
        $this->modelGoods = new Goods();
        $this->modelGoodsCommon = new GoodsCommon();
        $this->modelCategory = new Category();
        $this->modelType = new Type();
        $this->modelAttribute = new Attribute();
        $this->modelAttributeValue = new AttributeValue();
        $this->modelAttrIndex = new AttrIndex();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {        
        $schemas['goods_id'] = array(
            'name' => '所属商品sku',
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
                    return $this->modelGoods->getAll();
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['goods_commonid'] = array(
            'name' => '所属商品',
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
                    return $this->modelGoodsCommon->getAll();
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['gc_id'] = array(
            'name' => '所属分类',
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
                    return $this->modelCategory->getList4Tree('');
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
                'required' => 0
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
        $schemas['attr_id'] = array(
            'name' => '所属属性',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 0
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
        $schemas['attr_value_id'] = array(
            'name' => '所属属性值',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelAttributeValue->getAll();
                }
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
        return '商品属性对应';
    }

    protected function getModel()
    {
        return $this->modelAttrIndex;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $skuList = $this->modelGoods->getAll();
        $goodsList = $this->modelGoodsCommon->getAll();
        $categoryList = $this->modelCategory->getList4Tree('');
        $typeList = $this->modelType->getAll();
        $attributeList = $this->modelAttribute->getAll();
        $attributeValueList = $this->modelAttributeValue->getAll();
        foreach ($list['data'] as &$item) {
            $item['goods_id'] = isset($skuList[$item['goods_id']]) ? $skuList[$item['goods_id']] : "--";
            $item['goods_commonid'] = isset($goodsList[$item['goods_commonid']]) ? $goodsList[$item['goods_commonid']] : "--";
            $item['gc_id'] = isset($categoryList[$item['gc_id']]) ? $categoryList[$item['gc_id']] : "--";
            $item['type_id'] = isset($typeList[$item['type_id']]) ? $typeList[$item['type_id']] : "--";
            $item['attr_id'] = isset($attributeList[$item['attr_id']]) ? $attributeList[$item['attr_id']] : "--";
            $item['attr_value_id'] = isset($attributeValueList[$item['attr_value_id']]) ? $attributeValueList[$item['attr_value_id']] : "--";
        }
        return $list;
    }
}