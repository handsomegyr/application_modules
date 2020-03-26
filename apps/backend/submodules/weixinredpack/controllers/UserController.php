<?php
namespace App\Backend\Submodules\Weixinredpack\Controllers;

use App\Backend\Submodules\Weixinredpack\Models\User;

/**
 * @title({name="红包用户管理"})
 *
 * @name 红包用户管理
 */
class UserController extends \App\Backend\Controllers\FormController
{

    private $modelUser;

    public function initialize()
    {
        $this->modelUser = new User();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {        
        $schemas['FromUserName'] = array(
            'name' => '活动用户openid',
            'data' => array(
                'type' => 'string',
                'length' => '50'
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
        
        $schemas['re_openid'] = array(
            'name' => '红包用户openid',
            'data' => array(
                'type' => 'string',
                'length' => '50'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['withdraw_date'] = array(
            'name' => '提现日期',
            'data' => array(
                'type' => 'string',
                'length' => '8'
            ),
            'validation' => array(
                'required' => false
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
        
        $schemas['withdraw_money'] = array(
            'name' => '提现金额(分)',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'number',
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
        return '红包用户';
    }

    protected function getModel()
    {
        return $this->modelUser;
    }
}