<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\ExternalContact\AddWay;

/**
 * @title({name="客户来源"})
 *
 * @name 客户来源
 */
class ExternalcontactaddwayController extends BaseController
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
            'name' => '客户来源值',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
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
        return '客户来源';
    }

    protected function getModel()
    {
        return $this->modelExternalcontactAddWay;
    }
}
