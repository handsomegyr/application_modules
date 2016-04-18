<?php
namespace App\Backend\Submodules\System\Controllers;

use App\Backend\Submodules\System\Models\Role;

/**
 * @title({name="用户角色管理"})
 *
 * @name 用户角色管理
 */
class RoleController extends \App\Backend\Controllers\FormController
{
    private $modelRole;
    
    public function initialize()
    {
        $this->modelRole = new Role();
        parent::initialize();
    }
    
    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['name'] = array(
            'name' => '角色名',
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
                'is_show' => true,
                'placeholder' => '角色名...'
            )
        );
        
        $schemas['alias'] = array(
            'name' => '角色别名',
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
                'is_show' => true,
                'placeholder' => '角色别名...'
            )
        );
        
        $schemas['desc'] = array(
            'name' => '角色描述',
            'data' => array(
                'type' => 'string',
                'length' => '100'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['menu_list'] = array(
            'name' => '菜单设置',
            'data' => array(
                'type' => 'array',
                'length' => '1000'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'is_show' => true,
                'partial' => '../submodules/system/views/role/partials/settings'
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['operation_list'] = array(
            'name' => '操作设置',
            'data' => array(
                'type' => 'array',
                'length' => '1000'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'is_show' => true,
                'partial' => 'partials/empty'
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '角色';
    }

    protected function getModel()
    {
        return $this->modelRole;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        foreach ($list['data'] as &$item) {}
        return $list;
    }

    protected function validate4Insert(\App\Backend\Models\Input $input, $row)
    {
        // do other validation
        $this->getModel()->checkName($input->id, $input->name);
    }

    protected function validate4Update(\App\Backend\Models\Input $input, $row)
    {}

    protected function validate4Delete(\App\Backend\Models\Input $input, $row)
    {}
}