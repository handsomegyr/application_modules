<?php

namespace App\Backend\Submodules\System\Controllers;

use App\Backend\Submodules\System\Models\User;
use App\Backend\Submodules\System\Models\Role;

/**
 * @title({name="用户管理"})
 *
 * @name 用户管理
 */
class UserController extends \App\Backend\Controllers\FormController
{

    private $modelUser = NULL;

    private $modelRole = NULL;

    public function initialize()
    {
        $this->modelUser = new User();
        $this->modelRole = new Role();

        $this->roleList = $this->modelRole->getAll();

        parent::initialize();
    }
    private $roleList = null;

    protected function getSchemas2($schemas)
    {
        $schemas['username'] = array(
            'name' => '用户名称',
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
                'is_show' => true
            )
        );

        $schemas['headimgurl'] = array(
            'name' => '头像',
            'data' => array(
                'type' => 'file',
                'length' => 255,
                'file' => array(
                    'path' => $this->modelUser->getUploadPath()
                )
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'image',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true,
                'render' => 'img'
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['password'] = array(
            'name' => '密码',
            'data' => array(
                'type' => 'string',
                'length' => '20'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'password',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );

        $schemas['role'] = array(
            'name' => '角色',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->roleList
            ),
            'list' => array(
                'is_show' => true,
                'items' => $this->roleList
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->roleList
            )
        );

        $schemas['lastip'] = array(
            'name' => '最新登录IP',
            'data' => array(
                'type' => 'string',
                'length' => '20'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => false
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            )
        );

        $schemas['lasttime'] = array(
            'name' => '最新登录时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => true
            )
        );

        $schemas['times'] = array(
            'name' => '登陆次数',
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
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '用户';
    }

    protected function getModel()
    {
        return $this->modelUser;
    }
}
