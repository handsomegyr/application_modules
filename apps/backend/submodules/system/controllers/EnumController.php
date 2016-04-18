<?php
namespace App\Backend\Submodules\System\Controllers;

use App\Backend\Submodules\System\Models\Enum;

/**
 * @title({name="枚举管理"})
 *
 * @name 枚举管理
 */
class EnumController extends \App\Backend\Controllers\FormController
{

    private $modelEnum;

    public function initialize()
    {
        $this->modelEnum = new Enum();
        parent::initialize();
    }

    protected function getDefaultOrder()
    {
        return array(
            'show_order' => 'desc'
        );
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;
        
        $schemas['name'] = array(
            'name' => '枚举名',
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
        
        $schemas['code'] = array(
            'name' => '枚举值',
            'data' => array(
                'type' => 'integer',
                'length' => '2'
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
        
        $schemas['pid'] = array(
            'name' => '上级枚举',
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
                    return $this->modelEnum->getList4Tree('');
                }
            ),
            'list' => array(
                'is_show' => false
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
        return '枚举';
    }

    protected function getModel()
    {
        return $this->modelEnum;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        foreach ($list['data'] as &$item) {
            $item['show_name'] = str_repeat('&nbsp;', $item['level'] * 4) . $item['name'];
        }
        return $list;
    }
}