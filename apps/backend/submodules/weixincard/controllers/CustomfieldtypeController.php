<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\CustomFieldType;

/**
 * @title({name="会员信息卡类型管理"})
 *
 * @name 会员信息卡类型管理
 */
class CustomfieldtypeController extends \App\Backend\Controllers\FormController
{

    private $modelCustomFieldType;

    public function initialize()
    {
        $this->modelCustomFieldType = new CustomFieldType();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['type'] = array(
            'name' => '类型值',
            'data' => array(
                'type' => 'string',
                'length' => '30'
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
                'is_show' => false
            )
        );
        
        $schemas['name'] = array(
            'name' => '类型名称',
            'data' => array(
                'type' => 'string',
                'length' => '30'
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
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '会员信息卡类型';
    }

    protected function getModel()
    {
        return $this->modelCustomFieldType;
    }
}