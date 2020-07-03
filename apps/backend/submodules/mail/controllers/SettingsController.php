<?php

namespace App\Backend\Submodules\Mail\Controllers;

use App\Backend\Submodules\Mail\Models\Settings;

/**
 * @title({name="邮件设置管理"})
 *
 * @name 邮件设置管理
 */
class SettingsController extends \App\Backend\Controllers\FormController
{

    private $modelSettings;

    public function initialize()
    {
        $this->modelSettings = new Settings();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['host'] = array(
            'name' => '服务器',
            'data' => array(
                'type' => 'string',
                'length' => 100
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
        $schemas['port'] = array(
            'name' => '端口',
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
        $schemas['address_from'] = array(
            'name' => '发信人邮箱地址',
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
        $schemas['name_from'] = array(
            'name' => '发信人姓名',
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
        $schemas['username'] = array(
            'name' => '邮箱用户名',
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
        $schemas['password'] = array(
            'name' => '邮箱密码',
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

        $tlsOrSslDatas = array(
            array(
                'name' => 'tls',
                'value' => 'tls'
            ),
            array(
                'name' => 'ssl',
                'value' => 'ssl'
            ),
            array(
                'name' => '无',
                'value' => ''
            )
        );
        $schemas['secure'] = array(
            'name' => '加密方式',
            'data' => array(
                'type' => 'string',
                'length' => 10,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => 1
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $tlsOrSslDatas
            ),
            'list' => array(
                'is_show' => true,
                'items' => $tlsOrSslDatas
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['is_smtp'] = array(
            'name' => '是否使用SMTP',
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
        $schemas['is_auth'] = array(
            'name' => '是否SMTP认证',
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

        return $schemas;
    }

    protected function getName()
    {
        return '邮件设置';
    }

    protected function getModel()
    {
        return $this->modelSettings;
    }
}
