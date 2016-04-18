<?php
namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\SubscribeUser;

/**
 * @title({name="微信关注用户管理"})
 *
 * @name 微信关注用户管理
 */
class SubscribeUserController extends \App\Backend\Controllers\FormController
{

    private $modelSubscribeUser;

    public function initialize()
    {
        $this->modelSubscribeUser = new SubscribeUser();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['openid'] = array(
            'name' => '微信用户ID',
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
        return '微信关注用户';
    }

    protected function getModel()
    {
        return $this->modelSubscribeUser;
    }
}