<?php

namespace App\Backend\Submodules\Member\Controllers;

use App\Backend\Submodules\Member\Models\Bind;

/**
 * @title({name="会员绑定"})
 *
 * @name 会员绑定
 */
class BindController extends \App\Backend\Controllers\FormController
{
    private $modelBind;

    public function initialize()
    {
        $this->modelBind = new Bind();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['mobile'] = array(
            'name' => '手机号',
            'data' => array(
                'type' => 'string',
                'length' => 20,
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
        $schemas['openid'] = array(
            'name' => '微信用户ID',
            'data' => array(
                'type' => 'string',
                'length' => 190,
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
        return '会员绑定';
    }

    protected function getModel()
    {
        return $this->modelBind;
    }
}
