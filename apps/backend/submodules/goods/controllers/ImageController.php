<?php
namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Submodules\Goods\Models\GoodsCommon;
use App\Backend\Submodules\Goods\Models\SpecValue;
use App\Backend\Submodules\Goods\Models\Images;

/**
 * @title({name="商品图片管理"})
 *
 * @name 商品图片管理
 */
class ImageController extends \App\Backend\Controllers\FormController
{

    private $modelGoodsCommon;

    private $modelSpecValue;

    private $modelImages;

    public function initialize()
    {
        $this->modelGoodsCommon = new GoodsCommon();
        $this->modelSpecValue = new SpecValue();
        $this->modelImages = new Images();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {        $schemas['goods_commonid'] = array(
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
        $schemas['store_id'] = array(
            'name' => '所属店铺',
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
                    return $this->modelSpecValue->getAll();
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['color_id'] = array(
            'name' => '颜色规格值',
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
                    return $this->modelSpecValue->getAll();
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['image'] = array(
            'name' => '商品图片',
            'data' => array(
                'type' => 'file',
                'length' => 100,
                'file' => array(
                    'path' => $this->modelImages->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => 1
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
        $schemas['sort'] = array(
            'name' => '排序',
            'data' => array(
                'type' => 'integer',
                'length' => 3
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
        // 默认主题，1是，0否
        $schemas['is_default'] = array(
            'name' => '默认主题',
            'data' => array(
                'type' => 'boolean',
                'length' => 3
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
        return $schemas;
    }

    protected function getName()
    {
        return '商品图片';
    }

    protected function getModel()
    {
        return $this->modelImages;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $goodsList = $this->modelGoodsCommon->getAll();
        foreach ($list['data'] as &$item) {
            $item['goods_commonid'] = isset($goodsList[$item['goods_commonid']]) ? $goodsList[$item['goods_commonid']] : "--";
        }
        return $list;
    }
}