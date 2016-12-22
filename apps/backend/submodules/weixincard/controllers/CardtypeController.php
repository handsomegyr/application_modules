<?php
namespace App\Backend\Submodules\Weixincard\Controllers;

use App\Backend\Submodules\Weixincard\Models\CardType;

/**
 * @title({name="微信卡券类别管理"})
 *
 * @name 微信卡券类别管理
 */
class CardtypeController extends \App\Backend\Controllers\FormController
{

    private $modelCardType;

    public function initialize()
    {
        $this->modelCardType = new CardType();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['code'] = array(
            'name' => '值',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
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
        
        $schemas['name'] = array(
            'name' => '名称',
            'data' => array(
                'type' => 'string',
                'length' => '30'
            ),
            'validation' => array(
                'required' => true
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
        return '微信卡券类别';
    }

    protected function getModel()
    {
        return $this->modelCardType;
    }
}