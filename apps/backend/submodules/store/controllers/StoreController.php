<?php
namespace App\Backend\Controllers\Store;

use App\Backend\Models\Store\Store;

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
                'length' => 50
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
        // 0关闭，1开启，2审核中
        $stateItems = array(
            array(
                'name' => '审核中',
                'value' => '2'
            ),
            array(
                'name' => '开启',
                'value' => '1'
            ),
            array(
                'name' => '关闭',
                'value' => '0'
            )
        );
        $schemas['state'] = array(
            'name' => '店铺状态',
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
                'items' => $stateItems
            ),
            'list' => array(
                'is_show' => true,
                'items' => $stateItems
            ),
            'search' => array(
                'is_show' => false
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