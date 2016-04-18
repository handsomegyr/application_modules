<?php
namespace App\Backend\Controllers\Task;

use App\Backend\Models\Task\Log;

/**
 * @title({name="任务日志管理"})
 *
 * @name 任务日志管理
 */
class LogController extends \App\Backend\Controllers\FormController
{

    private $modelLog;

    public function initialize()
    {
        $this->modelLog = new Log();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['name'] = array(
            'name' => '公司名称',
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
                'is_show' => false
            )
        );
        
        $schemas['code'] = array(
            'name' => '编号',
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
                'is_show' => false
            )
        );
        
        $schemas['letter'] = array(
            'name' => '首字母',
            'data' => array(
                'type' => 'string',
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function ()
                {
                    return array_column($this->firstLetterDatas, 'value', 'name');
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['url'] = array(
            'name' => '链接',
            'data' => array(
                'type' => 'string',
                'length' => 100
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
        
        $schemas['state'] = array(
            'name' => '状态',
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
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['is_order'] = array(
            'name' => '是否常用',
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
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['zt_state'] = array(
            'name' => '是否支持服务站配送',
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
                'list_type' => 1
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        return $schemas;
    }

    protected function getName()
    {
        return '任务日志';
    }

    protected function getModel()
    {
        return $this->modelLog;
    }
}