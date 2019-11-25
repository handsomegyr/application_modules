<?php

namespace App\Backend\Submodules\Store\Controllers;

use App\Backend\Submodules\Store\Models\Store;

/**
 * @title({name="店铺管理"})
 *
 * @name 店铺管理
 */
class StoreController extends \App\Backend\Controllers\FormController
{

    private $modelStore;

    public function initialize()
    {
        $this->modelStore = new Store();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['name'] = array(
            'name' => '店铺名',
            'data' => array(
                'type' => 'string',
                'length' => 100
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['desc'] = array(
            'name' => '说明',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => false
            )
        );

        $schemas['logo'] = array(
            'name' => 'LOGO图',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img',
                // // 扩展设置
                // 'extensionSettings' => function ($column, $Grid) {
                //     //display()方法来通过传入的回调函数来处理当前列的值：
                //     $column->display(function () {
                //         return "<img src=" .  "/" . $this->logo . " height='50'/>";
                //     });
                // }
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );

        // 0关闭，1开启，2审核中
        // $stateItems = array(
        //     array(
        //         'name' => '审核中',
        //         'value' => '2'
        //     ),
        //     array(
        //         'name' => '开启',
        //         'value' => '1'
        //     ),
        //     array(
        //         'name' => '关闭',
        //         'value' => '0'
        //     )
        // );
        $stateItems = array();
        $stateItems['2'] = '审核中';
        $stateItems['1'] = '开启';
        $stateItems['0'] = '关闭';

        $schemas['state'] = array(
            'name' => '店铺状态',
            'data' => array(
                'type' => 'integer',
                'length' => 1,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $stateItems
            ),
            'list' => array(
                'is_show' => true,
                'items' => $stateItems
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['sort'] = array(
            'name' => '排序',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'is_editable' => true
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '店铺';
    }

    protected function getModel()
    {
        return $this->modelStore;
    }
}
