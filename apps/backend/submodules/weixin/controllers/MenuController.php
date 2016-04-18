<?php
namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\MenuType;
use App\Backend\Submodules\Weixin\Models\Menu;

/**
 * @title({name="微信自定义菜单管理"})
 *
 * @name 微信自定义菜单管理
 */
class MenuController extends \App\Backend\Controllers\FormController
{

    private $modelMenuType;

    private $modelMenu;

    public function initialize()
    {
        $this->modelMenuType = new MenuType();
        $this->modelMenu = new Menu();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;
        
        $schemas['parent'] = array(
            'name' => '上级菜单',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelMenu->getList4Tree('');
                }
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['name'] = array(
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
                'is_show' => true,
                'list_data_name' => 'show_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['type'] = array(
            'name' => '菜单类型',
            'data' => array(
                'type' => 'string',
                'length' => '30'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelMenuType->getAll();
                }
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'menu_type_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['url'] = array(
            'name' => '链接(类型为view时必填)',
            'data' => array(
                'type' => 'string',
                'length' => '100'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['key'] = array(
            'name' => '事件名称(英文、数字)',
            'data' => array(
                'type' => 'string',
                'length' => '30'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['priority'] = array(
            'name' => '权重',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => true
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
        return '微信自定义菜单';
    }

    protected function getModel()
    {
        return $this->modelMenu;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $menuTypeList = $this->modelMenuType->getAll();
        foreach ($list['data'] as &$item) {
            $item['menu_type_name'] = isset($menuTypeList[$item['type']]) ? $menuTypeList[$item['type']] : "--";
            $item['show_name'] = str_repeat('&nbsp;', $item['level'] * 4) . $item['name'];
        }
        return $list;
    }
}