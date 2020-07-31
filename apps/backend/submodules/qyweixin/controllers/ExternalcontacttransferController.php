<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\Transfer;

/**
 * @title({name="企业微信"})
 *
 * @name 企业微信
 */
class ExternalcontacttransferController extends \App\Backend\Controllers\FormController
{
    private $modelExternalcontactTransfer;

    public function initialize()
    {
        $this->modelExternalcontactTransfer = new Transfer();
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
        $schemas['external_userid'] = array(
            'name' => '外部联系人的userid，注意不是企业成员的帐号',
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
        $schemas['handover_userid'] = array(
            'name' => '离职成员的userid',
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
        $schemas['takeover_userid'] = array(
            'name' => '接替成员的userid',
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
        $schemas['is_transfered'] = array(
            'name' => '是否已分配',
            'data' => array(
                'type' => 'boolean',
                'length' => 1,
                'defaultValue' => false,
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas,
                'help' => '',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '1',
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
        $schemas['transfer_time'] = array(
            'name' => '分配时间',
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
        return $this->modelExternalcontactTransfer;
    }
}
