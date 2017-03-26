<?php
namespace App\Backend\Submodules\Bargain\Controllers;

use App\Backend\Submodules\Bargain\Models\BlackUser;

/**
 * @title({name="用户惩罚系数管理"})
 *
 * @name 用户惩罚系数管理
 */
class BlackuserController extends \App\Backend\Controllers\FormController
{

    private $modelBlackUser;

    public function initialize()
    {
        $this->modelBlackUser = new BlackUser();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'length' => 50
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
                'is_show' => true
            )
        );
        $schemas['user_name'] = array(
            'name' => '用户名',
            'data' => array(
                'type' => 'string',
                'length' => 50
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
        $schemas['user_headimgurl'] = array(
            'name' => '用户头像',
            'data' => array(
                'type' => 'string',
                'length' => 300
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        $schemas['alpha'] = array(
            'name' => '阿尔法系数',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => 1
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
        return '用户惩罚系数';
    }

    protected function getModel()
    {
        return $this->modelBlackUser;
    }
}