<?php
namespace App\Backend\Controllers\Goods;

use App\Backend\Models\Goods\Spec;
use App\Backend\Models\Goods\Type;
use App\Backend\Models\Goods\TypeSpec;

/**
 * @title({name="商品类型规格管理"})
 *
 * @name 商品类型规格管理
 */
class TypespecController extends \App\Backend\Controllers\FormController
{

    private $modelSpec;

    private $modelType;

    private $modelTypeSpec;

    public function initialize()
    {
        $this->modelSpec = new Spec();
        $this->modelType = new Type();
        $this->modelTypeSpec = new TypeSpec();
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
        $schemas['sp_id'] = array(
            'name' => '所属规格',
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
                    return $this->modelSpec->getAll();
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
        return '商品类型规格';
    }

    protected function getModel()
    {
        return $this->modelTypeSpec;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $typeList = $this->modelType->getAll();
        $specList = $this->modelSpec->getAll();
        foreach ($list['data'] as &$item) {
            $item['type_id'] = isset($typeList[$item['type_id']]) ? $typeList[$item['type_id']] : "--";
            $item['sp_id'] = isset($specList[$item['sp_id']]) ? $specList[$item['sp_id']] : "--";
        }
        
        return $list;
    }
}