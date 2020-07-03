<?php

namespace App\Backend\Submodules\Live\Controllers;

use App\Backend\Submodules\Live\Models\Resource;

/**
 * @title({name="直播资源管理"})
 *
 * @name 直播资源管理
 */
class ResourceController extends \App\Backend\Controllers\FormController
{

    private $modelResource;

    public function initialize()
    {
        $this->modelResource = new Resource();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['contentType'] = array(
            'name' => '资源类型',
            'data' => array(
                'type' => 'integer',
                'defaultValue' => 1,
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->getTypeList()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'type_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->getTypeList()
            )
        );

        $schemas['content'] = array(
            'name' => '资源内容',
            'data' => array(
                'type' => 'string',
                'length' => 255
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
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
        return '直播资源';
    }

    protected function getModel()
    {
        return $this->modelResource;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $typeList = $this->getTypeList();
        foreach ($list['data'] as &$item) {
            $item['type_name'] = isset($typeList[$item['contentType']]) ? $typeList[$item['contentType']] : "--";
        }
        return $list;
    }

    private function getTypeList()
    {
        return array(
            1 => '昵称',
            2 => '头像',
            3 => '话语',
            4 => '欢迎语'
        );
    }
}
