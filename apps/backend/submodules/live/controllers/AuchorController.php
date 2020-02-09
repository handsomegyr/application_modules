<?php
namespace App\Backend\Submodules\Live\Controllers;

use App\Backend\Submodules\Live\Models\Auchor;

/**
 * @title({name="直播主播管理"})
 *
 * @name 直播主播管理
 */
class AuchorController extends \App\Backend\Controllers\FormController
{

    private $modelAuchor;

    public function initialize()
    {
        $this->modelAuchor = new Auchor();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['name'] = array(
            'name' => '主播名',
            'data' => array(
                'type' => 'string',
                'length' => 30
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
        
        $schemas['openid'] = array(
            'name' => '主播ID',
            'data' => array(
                'type' => 'string',
                'length' => 50
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
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['nickname'] = array(
            'name' => '主播昵称',
            'data' => array(
                'type' => 'string',
                'length' => 30
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
        $schemas['headimgurl'] = array(
            'name' => '主播头像',
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['worth'] = array(
            'name' => '价值',
            'data' => array(
                'type' => 'integer',
                'length' => 10
            ),
            'validation' => array(
                'required' => true
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['worth2'] = array(
            'name' => '价值2',
            'data' => array(
                'type' => 'integer',
                'length' => 10
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['redpack_user'] = array(
            'name' => '微信红包账号',
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['thirdparty_user'] = array(
            'name' => '第3方账号',
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['contact_name'] = array(
            'name' => '联系主播',
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
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['contact_mobile'] = array(
            'name' => '联系手机',
            'data' => array(
                'type' => 'string',
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
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['contact_address'] = array(
            'name' => '联系地址',
            'data' => array(
                'type' => 'string',
                'length' => 200
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
                
        $schemas['is_vip'] = array(
            'name' => '是否是VIP',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => false,
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'list_type' => '1',
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'condition_type' => '',
                'defaultValues' => array(),
                'cascade' => '',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['is_test'] = array(
            'name' => '是否是测试主播',
            'data' => array(
                'type' => 'boolean',
                'defaultValue' => false,
                'length' => 1
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'radio',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'list' => array(
                'list_type' => '1',
                'is_show' => true
            ),
            'search' => array(
                'input_type' => 'select',
                'condition_type' => '',
                'defaultValues' => array(),
                'cascade' => '',
                'items' => $this->trueOrFalseDatas,
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );
        
        $schemas['memo'] = array(
            'name' => '备注',
            'data' => array(
                'type' => 'json',
                'length' => '1000'
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
            )
        );
        return $schemas;
    }

    protected function getName()
    {
        return '直播主播';
    }

    protected function getModel()
    {
        return $this->modelAuchor;
    }

}