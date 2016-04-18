<?php
namespace App\Backend\Submodules\Tencent\Controllers;

use App\Backend\Submodules\Tencent\Models\AppKey;
use App\Backend\Submodules\Tencent\Models\Application;

/**
 * @title({name="腾讯-应用设置管理"})
 *
 * @name 腾讯-应用设置管理
 */
class ApplicationController extends \App\Backend\Controllers\FormController
{

    private $modelAppKey;

    private $modelApplication;

    public function initialize()
    {
        $this->modelAppKey = new AppKey();
        $this->modelApplication = new Application();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['name'] = array(
            'name' => '应用名',
            'data' => array(
                'type' => 'string',
                'length' => 30
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
        $schemas['appKeyId'] = array(
            'name' => '应用密钥',
            'data' => array(
                'type' => 'string',
                'length' => 50
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelAppKey->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'appkey_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['secretKey'] = array(
            'name' => '秘钥',
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
        return $schemas;
    }

    protected function getName()
    {
        return '腾讯-应用设置';
    }

    protected function getModel()
    {
        return $this->modelApplication;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $appKeyList = $this->modelAppKey->getAll();
        foreach ($list['data'] as &$item) {
            $item['appkey_name'] = isset($appKeyList[$item['appKeyId']]) ? $appKeyList[$item['appKeyId']] : "--";
        }
        return $list;
    }
}