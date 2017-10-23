<?php
namespace App\Backend\Submodules\Alipay\Controllers;

use App\Backend\Submodules\Alipay\Models\User;
use App\Backend\Submodules\Alipay\Models\Application;

/**
 * @title({name="支付宝用户管理"})
 *
 * @name 支付宝用户管理
 */
class UserController extends \App\Backend\Controllers\FormController
{

    private $modelApplication;

    private $modelUser;

    public function initialize()
    {
        $this->modelApplication = new Application();
        $this->modelUser = new User();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['app_id'] = array(
            'name' => '应用ID',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 32
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelApplication->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'app_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelApplication->getAll()
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['user_id'] = array(
            'name' => '用户ID',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 16
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
                'input_type' => 'text',
                'condition_type' => '',
                'defaultValues' => array(),
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['nick_name'] = array(
            'name' => '昵称',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
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
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['avatar'] = array(
            'name' => '头像',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 400
            ),
            'validation' => array(
                'required' => true
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['province'] = array(
            'name' => '省',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 20
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['city'] = array(
            'name' => '市',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 20
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['is_student_certified'] = array(
            'name' => '是否是学生(T/F)',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 1
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['user_type'] = array(
            'name' => '用户类型（1/2）',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 1
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['user_status'] = array(
            'name' => '用户状态（Q/T/B/W）',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 1
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['is_certified'] = array(
            'name' => '是否通过实名认证(T/F)',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 1
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['gender'] = array(
            'name' => '性别(F/M)',
            'data' => array(
                'type' => 'string',
                'defaultValue' => '',
                'length' => 1
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['access_token'] = array(
            'name' => 'Access Token',
            'data' => array(
                'type' => 'json',
                'defaultValue' => '',
                'length' => 1024
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '支付宝用户';
    }

    protected function getModel()
    {
        return $this->modelUser;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $appliactionList = $this->modelApplication->getAll();
        foreach ($list['data'] as &$item) {
            $item['app_name'] = isset($appliactionList[$item['app_id']]) ? $appliactionList[$item['app_id']] : "--";
        }
        return $list;
    }
}