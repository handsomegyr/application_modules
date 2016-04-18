<?php
namespace App\Backend\Submodules\System\Controllers;

use App\Backend\Submodules\System\Models\Menu;

/**
 * @title({name="菜单管理"})
 *
 * @name 菜单管理
 */
class MenuController extends \App\Backend\Controllers\FormController
{

    private $modelMenu;

    public function initialize()
    {
        $this->modelMenu = new Menu();
        parent::initialize();
    }

    /**
     * @title({name="切换显示状态"})
     *
     * @name 切换显示状态
     */
    public function toggleisshowAction()
    {
        try {
            $this->view->disable();
            $input = $this->getFilterInput();
            
            if ($input->isValid("id") && $input->isValid("is_show")) {} else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            
            $is_show = intval($input->is_show);
            $this->modelMenu->update(array(
                '_id' => $input->id
            ), array(
                '$set' => array(
                    'is_show' => empty($is_show) ? false : true
                )
            ));
            
            $this->makeJsonResult(stripslashes($is_show));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    /**
     * @title({name="编辑排序序号"})
     *
     * @name 编辑排序序号
     */
    public function editshoworderAction()
    {
        try {
            $this->view->disable();
            
            $input = $this->getFilterInput();
            
            if ($input->isValid("id") && $input->isValid("show_order")) {} else {
                $messageInfo = $this->_getValidationMessage($input);
                throw new \Exception($messageInfo);
            }
            
            $show_order = intval($input->show_order);
            $this->modelMenu->update(array(
                '_id' => $input->id
            ), array(
                '$set' => array(
                    'show_order' => $show_order
                )
            ));
            $this->makeJsonResult(stripslashes($show_order));
        } catch (\Exception $e) {
            $this->makeJsonError($e->getMessage());
        }
    }

    protected function getDefaultOrder()
    {
        return array(
            'show_order' => 'desc'
        );
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;
        
        $schemas['name'] = array(
            'name' => '菜单名称',
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
                'is_show' => true,
                'list_data_name' => 'show_name'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['url'] = array(
            'name' => '菜单地址',
            'data' => array(
                'type' => 'string',
                'length' => '100'
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
        
        $schemas['pid'] = array(
            'name' => '上级菜单',
            'data' => array(
                'type' => 'string',
                'length' => '24'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return $this->modelMenu->getList4Tree('');
                }
            ),
            'list' => array(
                'is_show' => false
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['is_show'] = array(
            'name' => '是否显示',
            'data' => array(
                'type' => 'boolean',
                'length' => '1'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'radio',
                'is_show' => true,
                'items' => $this->trueOrFalseDatas
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => 1,
                'ajax' => 'toggleisshow'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['show_order'] = array(
            'name' => '排序',
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
                'is_show' => true,
                'ajax' => 'editshoworder'
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '菜单';
    }

    protected function getModel()
    {
        return $this->modelMenu;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        foreach ($list['data'] as &$item) {
            $item['show_name'] = str_repeat('&nbsp;', $item['level'] * 4) . $item['name'];
        }
        return $list;
    }

    protected function validate4Insert(\App\Backend\Models\Input $input, $row)
    {
        // do other validation
        $this->getModel()->checkName($input->id, $input->pid, $input->name);
    }

    protected function validate4Update(\App\Backend\Models\Input $input, $row)
    {
        // do other validation
        $this->getModel()->checkName($input->id, $input->pid, $input->name);
        
        /* 还有子菜单，不能更改 */
        if ($input->pid != $row['pid']) {
            $this->getModel()->checkIsLeaf($input->id);
        }
    }

    protected function validate4Delete(\App\Backend\Models\Input $input, $row)
    {
        // do other validation
        /* 还有子菜单，不能删除 */
        $this->getModel()->checkIsLeaf($input->id);
    }
}