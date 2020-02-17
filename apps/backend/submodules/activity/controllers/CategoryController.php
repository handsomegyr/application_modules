<?php

namespace App\Backend\Submodules\Activity\Controllers;

use App\Backend\Submodules\Activity\Models\Category;

/**
 * @title({name="活动分类管理"})
 *
 * @name 活动分类管理
 */
class CategoryController extends \App\Backend\Controllers\FormController
{

    private $modelCategory;

    public function initialize()
    {
        $this->modelCategory = new Category();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;

        $schemas['code'] = array(
            'name' => '标识码',
            'data' => array(
                'type' => 'integer',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                // // 扩展设置
                // 'extensionSettings' => function ($formItem, $form) {
                //     //设置autofocus
                //     $formItem->autofocus();
                //     $formItem->placeholder('请输入数字型标识码');
                // }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true,
                // // 扩展设置
                // 'extensionSettings' => function ($filterItem, $filter) {
                //     //设置placeholder
                //     $filterItem->placeholder('请输入。。。');
                //     //限制用户输入格式
                //     $filterItem->integer();
                // }
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['name'] = array(
            'name' => '分类名称',
            'data' => array(
                'type' => 'string',
                'length' => 50
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
                'list_data_name' => 'show_name',
                // // 扩展设置
                // 'extensionSettings' => function ($column, $Grid) {
                //     //display()方法来通过传入的回调函数来处理当前列的值：
                //     $column->display(function () {
                //         $value = $this->name;
                //         return "<span style='color:blue'>$value</span>";
                //     });
                // }
            ),
            'search' => array(
                'is_show' => true
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
                'is_show' => false
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '活动分类';
    }

    protected function getModel()
    {
        return $this->modelCategory;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        foreach ($list['data'] as &$item) {
            $item['show_name'] = str_repeat('&nbsp;', 0 * 4) . $item['name'];
        }
        return $list;
    }
}
