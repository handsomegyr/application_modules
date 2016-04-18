<?php
namespace App\Backend\Submodules\System\Controllers;

use App\Backend\Submodules\System\Models\Area;

/**
 * @title({name="全国行政区划管理"})
 *
 * @name 全国行政区划管理
 */
class AreaController extends \App\Backend\Controllers\FormController
{

    private $modelArea;

    public function initialize()
    {
        $this->modelArea = new Area();
        parent::initialize();
    }

    protected function getDefaultOrder()
    {
        return array(
            'level' => 'asc'
        );
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;
        
        $schemas['name'] = array(
            'name' => '地区名称',
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
            'name' => '地区编码',
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
        $schemas['level'] = array(
            'name' => '级别',
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
        $schemas['parent_code'] = array(
            'name' => '上级地区',
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
                    return $this->modelArea->getList4Tree('');
                }
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '全国行政区划';
    }

    protected function getModel()
    {
        return $this->modelArea;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        foreach ($list['data'] as &$item) {
            $item['show_name'] = str_repeat('&nbsp;', $item['level'] * 4) . $item['name'];
        }
        return $list;
    }
}