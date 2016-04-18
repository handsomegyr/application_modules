<?php
namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Submodules\Goods\Models\GoodsCommon;
use App\Backend\Submodules\Goods\Models\Fcode;

/**
 * @title({name="商品F码管理"})
 *
 * @name 商品F码管理
 */
class FcodeController extends \App\Backend\Controllers\FormController
{

    private $modelGoodsCommon;

    private $modelFcode;

    public function initialize()
    {
        $this->modelGoodsCommon = new GoodsCommon();
        $this->modelFcode = new Fcode();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
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
        $schemas['code'] = array(
            'name' => 'F码',
            'data' => array(
                'type' => 'string',
                'length' => 20
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
        // 状态 0未使用，1已使用
        $schemas['state'] = array(
            'name' => '状态',
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
        return '商品F码';
    }

    protected function getModel()
    {
        return $this->modelFcode;
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