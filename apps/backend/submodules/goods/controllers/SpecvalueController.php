<?php
namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Submodules\Goods\Models\Category;
use App\Backend\Submodules\Goods\Models\Spec;
use App\Backend\Submodules\Goods\Models\SpecValue;

/**
 * @title({name="商品规格值管理"})
 *
 * @name 商品规格值管理
 */
class SpecvalueController extends \App\Backend\Controllers\FormController
{

    private $modelCategory;

    private $modelSpec;

    private $modelSpecValue;

    public function initialize()
    {
        $this->modelCategory = new Category();
        $this->modelSpec = new Spec();
        $this->modelSpecValue = new SpecValue();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['name'] = array(
            'name' => '规格值名称',
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
                    return $this->modelCategory->getList4Tree();
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['store_id'] = array(
            'name' => '所属店铺',
            'data' => array(
                'type' => 'string',
                'length' => 24
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
        $schemas['color'] = array(
            'name' => '规格颜色',
            'data' => array(
                'type' => 'string',
                'length' => 10
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
        return '商品规格值';
    }

    protected function getModel()
    {
        return $this->modelSpecValue;
    }
}