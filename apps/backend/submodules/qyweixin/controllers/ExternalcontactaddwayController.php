<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\AddWay;

/**
 * @title({name="企业微信"})
 *
 * @name 企业微信
 */
class ExternalcontactaddwayController extends \App\Backend\Controllers\FormController
{
    private $modelExternalcontactAddWay;

    public function initialize()
    {
        $this->modelExternalcontactAddWay = new AddWay();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {
        $schemas['name'] = array(
            'name' => '客户来源名',
            'data' => array(
                'type' => 'string',
                'length' => 20,
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
        $schemas['value'] = array(
            'name' => '客户来源值',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0,
            ),
            'validation' => array(
                'required' => false,
            ),
            'form' => array(
                'input_type' => 'number',
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

        return $schemas;
    }

    protected function getName()
    {
        return '企业微信';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactAddWay;
    }
}
