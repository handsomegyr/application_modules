<?php
namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Submodules\Goods\Models\Brand;
use App\Backend\Submodules\Goods\Models\Type;
use App\Backend\Submodules\Goods\Models\TypeBrand;

/**
 * @title({name="商品类型品牌管理"})
 *
 * @name 商品类型品牌管理
 */
class TypebrandController extends \App\Backend\Controllers\FormController
{

    private $modelBrand;

    private $modelType;

    private $modelTypeBrand;

    public function initialize()
    {
        $this->modelBrand = new Brand();
        $this->modelType = new Type();
        $this->modelTypeBrand = new TypeBrand();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
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
        $schemas['brand_id'] = array(
            'name' => '所属品牌',
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
                    return $this->modelBrand->getAll();
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
        return '商品类型品牌';
    }

    protected function getModel()
    {
        return $this->modelTypeBrand;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $typeList = $this->modelType->getAll();
        $brandList = $this->modelBrand->getAll();
        foreach ($list['data'] as &$item) {
            $item['type_id'] = isset($typeList[$item['type_id']]) ? $typeList[$item['type_id']] : "--";
            $item['brand_id'] = isset($brandList[$item['brand_id']]) ? $brandList[$item['brand_id']] : "--";
        }
        
        return $list;
    }
}