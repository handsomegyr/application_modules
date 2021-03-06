<?php

namespace App\Backend\Submodules\Goods\Controllers;

use App\Backend\Submodules\Goods\Models\Ad;
use App\Backend\Submodules\Goods\Models\GoodsCommon;

/**
 * @title({name="商品广告位管理"})
 *
 * @name 商品广告位管理
 */
class AdController extends \App\Backend\Controllers\FormController
{

    private $modelGoodsAd;
    private $modelGoodsCommon;

    public function initialize()
    {
        $this->modelGoodsAd = new Ad();
        $this->modelGoodsCommon = new GoodsCommon();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['goods_id'] = array(
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
                'items' => function () {
                    return $this->modelGoodsCommon->getAll(array());
                },
                // 'select' => array(
                //     'is_remote_load' => true,
                //     'apiUrl' => "admin/goods/goodscommon/getgoodslist"
                // )
            ),
            'list' => array(
                'is_show' => true,
                'items' => function () {
                    return $this->modelGoodsCommon->getAll(array());
                }
            ),
            'search' => array(
                'is_show' => true
            )
        );

        $now = date('Y-m-d') . " 00:00:00";
        $now = strtotime($now);

        $schemas['start_time'] = array(
            'name' => '开始时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now)
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['end_time'] = array(
            'name' => '截止时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime($now + 3600 * 24 * 2 - 1)
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
                'length' => '1'
            ),
            'validation' => array(
                'required' => false
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

        $schemas['show_order'] = array(
            'name' => '排序',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => false
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
        return '商品广告位';
    }

    protected function getModel()
    {
        return $this->modelGoodsAd;
    }
}
