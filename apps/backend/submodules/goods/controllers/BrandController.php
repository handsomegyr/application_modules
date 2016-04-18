<?php
namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Models\Goods\Category;
use App\Backend\Models\Goods\Brand;

/**
 * @title({name="商品品牌管理"})
 *
 * @name 商品品牌管理
 */
class BrandController extends \App\Backend\Controllers\FormController
{

    private $modelCategory;

    private $modelBrand;

    public function initialize()
    {
        $this->modelCategory = new Category();
        $this->modelBrand = new Brand();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['name'] = array(
            'name' => '品牌名称',
            'data' => array(
                'type' => 'string',
                'length' => 100
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
        $schemas['initial'] = array(
            'name' => '名称首字母',
            'data' => array(
                'type' => 'string',
                'length' => 1
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
        
        $schemas['category_id'] = array(
            'name' => '所属分类',
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
                    return $this->modelCategory->getList4Tree();
                }
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['category_name'] = array(
            'name' => '类别名称',
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
        $schemas['pic'] = array(
            'name' => '品牌图片标识',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelBrand->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'file',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 品牌展示类型 0表示图片 1表示文字
        $showtypeItems = array(
            array(
                'name' => '文字',
                'value' => '1'
            ),
            array(
                'name' => '图片',
                'value' => '0'
            )
        );
        $schemas['show_type'] = array(
            'name' => '展示方式',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $showtypeItems
            ),
            'list' => array(
                'is_show' => true,
                'items' => $showtypeItems
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 推荐0为否，1为是，默认为0
        $schemas['recommend'] = array(
            'name' => '是否推荐',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => 0
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '1'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['sort'] = array(
            'name' => '排序',
            'data' => array(
                'type' => 'integer',
                'length' => 3
            ),
            'validation' => array(
                'required' => 0
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
        $schemas['store_id'] = array(
            'name' => '店铺ID',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        // 品牌申请，0为申请中，1为通过，默认为1，申请功能是会员使用，系统后台默认为1
        $applyItems = array(
            array(
                'name' => '通过',
                'value' => '1'
            ),
            array(
                'name' => '申请中',
                'value' => '0'
            )
        );
        $schemas['apply'] = array(
            'name' => '品牌申请',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 1
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $applyItems
            ),
            'list' => array(
                'is_show' => true,
                'items' => $applyItems
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '商品品牌';
    }

    protected function getModel()
    {
        return $this->modelBrand;
    }
}