<?php
namespace App\Backend\Submodules\Message\Controllers;

use App\Backend\Submodules\Message\Models\Msg;

/**
 * @title({name="私信管理"})
 *
 * @name 私信管理
 */
class MsgController extends \App\Backend\Controllers\FormController
{

    private $modelMsg;

    public function initialize()
    {
        $this->modelMsg = new Msg();
        parent::initialize();
    }

    protected function getSchemas2($schemas)
    {        
        $schemas['from_user_id'] = array(
            'name' => '发起者',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
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
        $schemas['to_user_id'] = array(
            'name' => '接受者',
            'data' => array(
                'type' => 'string',
                'length' => 24
            ),
            'validation' => array(
                'required' => 1
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
        $schemas['content'] = array(
            'name' => '消息内容',
            'data' => array(
                'type' => 'string',
                'length' => 1000
            ),
            'validation' => array(
                'required' => 1
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
        return $schemas;
    }

    protected function getName()
    {
        return '私信';
    }

    protected function getModel()
    {
        return $this->modelMsg;
    }
}