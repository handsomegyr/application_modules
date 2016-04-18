<?php
namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Models\Goods\Type;
use App\Backend\Models\Goods\Category;
use App\Backend\Models\Goods\CategoryTag;

/**
 * @title({name="商品分类TAG管理"})
 *
 * @name 商品分类TAG管理
 */
class CategorytagController extends \App\Backend\Controllers\FormController
{

    private $modelType;

    private $modelCategory;

    private $modelCategoryTag;

    public function initialize()
    {
        $this->modelType = new Type();
        $this->modelCategory = new Category();
        $this->modelCategoryTag = new CategoryTag();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['gc_id_1'] = array(
            'name' => '一级分类',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['gc_id_2'] = array(
            'name' => '二级分类',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['gc_id_3'] = array(
            'name' => '三级分类',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['tag_name'] = array(
            'name' => 'TAG名称',
            'data' => array(
                'type' => 'string',
                'length' => 255
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
        $schemas['tag_value'] = array(
            'name' => 'TAG值',
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
        $schemas['gc_id'] = array(
            'name' => '商品分类',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['type_id'] = array(
            'name' => '类型',
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
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        return $schemas;
    }

    protected function getName()
    {
        return '商品分类TAG';
    }

    protected function getModel()
    {
        return $this->modelCategoryTag;
    }
}