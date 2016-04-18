<?php
namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\MenuType;

/**
 * @title({name="微信菜单类型管理"})
 *
 * @name 微信菜单类型管理
 */
class MenuTypeController extends \App\Backend\Controllers\FormController
{

    private $modelMenuType;

    public function initialize()
    {
        $this->modelMenuType = new MenuType();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['key'] = array(
            'name' => '名称',
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
        
        $schemas['value'] = array(
            'name' => '值',
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
        return '微信菜单类型';
    }

    protected function getModel()
    {
        return $this->modelMenuType;
    }
}