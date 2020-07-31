<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\FollowUser;

/**
 * @title({name="企业微信"})
 *
 * @name 企业微信
 */
class ExternalcontactfollowuserController extends \App\Backend\Controllers\FormController
{
    private $modelExternalcontactFollowUser;

    public function initialize()
    {
        $this->modelExternalcontactFollowUser = new FollowUser();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['provider_appid'] = array(
            'name' => '第三方服务商应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 32,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['authorizer_appid'] = array(
            'name' => '授权方应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 32,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['follow_user'] = array(
            'name' => '配置了客户联系功能的成员userid',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['get_time'] = array(
            'name' => '同步时间',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime(),
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '',
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '',
            ),
            'list' => array(
                'is_show' => false,
                'list_type' => '',
                'render' => '',
                'items' => '',
            ),
            'search' => array(
                'is_show' => true,
                'input_type' => 'text',
                'items' => '',
            ),
            'export' => array(
                'is_show' => true,
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '企业微信';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactFollowUser;
    }
}
