<?php

namespace App\Backend\Submodules\Backend\Controllers;

use App\Backend\Submodules\Backend\Models\OperationLog;
use App\Backend\Submodules\Backend\Models\User;

/**
 * @title({name="操作日志管理"})
 *
 * @name 操作日志管理
 */
class OperationlogController extends \App\Backend\Controllers\FormController
{

    private $modelOperationLog;
    private $modelUser = NULL;
    public function initialize()
    {
        $this->modelOperationLog = new OperationLog();
        $this->modelUser = new User();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['user_id'] = array(
            'name' => '操作用户',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelUser->getAll();
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            )
        );

        $schemas['method'] = array(
            'name' => '请求方式',
            'data' => array(
                'type' => 'string',
                'length' => '10'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->methodDatas
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->methodDatas
            )
        );

        $schemas['path'] = array(
            'name' => '请求URL',
            'data' => array(
                'type' => 'string',
                'length' => '191'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            )
        );

        $schemas['ip'] = array(
            'name' => 'IP',
            'data' => array(
                'type' => 'string',
                'length' => '15'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'ip',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            )
        );

        $schemas['params'] = array(
            'name' => '请求参数',
            'data' => array(
                'type' => 'json',
                'length' => '1024'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['happen_time'] = array(
            'name' => '发生时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
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
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '操作日志';
    }

    protected function getModel()
    {
        return $this->modelOperationLog;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $userList = $this->modelUser->getAll();
        foreach ($list['data'] as &$item) {
            $item['user_id'] = $userList[$item['user_id']];
            $item['method'] = '<span class="badge bg-blue">' . $item['method'] . '</span>';
            $item['path'] = '<span class="label label-info">' . $item['path'] . '</span>';
            $item['ip'] = '<span class="label label-primary">' . $item['ip'] . '</span>';
            // $item['params'] = '<pre>' . \App\Common\Utils\Helper::myJsonEncode($item['params']) . '</pre>';
            $item['params'] = \App\Common\Utils\Helper::myJsonEncode($item['params']);
        }
        return $list;
    }
}
