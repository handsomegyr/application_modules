<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\SubscribeScene;

/**
 * @title({name="用户关注渠道来源"})
 *
 * @name 用户关注渠道来源
 */
class SubscribesceneController extends \App\Backend\Controllers\FormController
{
    private $modelSubscribeScene;

    public function initialize()
    {
        $this->modelSubscribeScene = new SubscribeScene();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['name'] = array(
            'name' => '渠道来源名',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['value'] = array(
            'name' => '渠道来源值',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '用户关注渠道来源';
    }

    protected function getModel()
    {
        return $this->modelSubscribeScene;
    }
}
