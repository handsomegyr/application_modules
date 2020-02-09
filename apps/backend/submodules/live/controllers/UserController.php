<?php
namespace App\Backend\Submodules\Live\Controllers;

use App\Backend\Submodules\Live\Models\User;
use App\Backend\Submodules\Live\Models\Room;
use App\Backend\Submodules\Live\Models\Auchor;

/**
 * @title({name="直播用户管理"})
 *
 * @name 直播用户管理
 */
class UserController extends \App\Backend\Controllers\FormController
{

    private $modelUser;

    private $modelRoom;

    private $modelAuchor;

    public function initialize()
    {
        $this->modelUser = new User();
        $this->modelRoom = new Room();
        $this->modelAuchor = new Auchor();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['room_id'] = array(
            'name' => '直播间名称',
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
                'items' => $this->modelRoom->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'room_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelRoom->getAll()
            )
        );
        $schemas['openid'] = array(
            'name' => '用户ID',
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
            'name' => '用户名',
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
            'name' => '联系用户',
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
        
        $schemas['is_auchor'] = array(
            'name' => '是否是主播',
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
        
        $schemas['auchor_id'] = array(
            'name' => '主播名称',
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
                'items' => $this->modelAuchor->getAll()
            ),
            'list' => array(
                'is_show' => true,
                'list_data_name' => 'auchor_name'
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->modelAuchor->getAll()
            )
        );
        
        $schemas['is_vip'] = array(
            'name' => '是否是VIP用户',
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
            'name' => '是否是测试人员',
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
        return '直播用户';
    }

    protected function getModel()
    {
        return $this->modelUser;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $roomList = $this->modelRoom->getAll();
        $auchorList = $this->modelAuchor->getAll();
        foreach ($list['data'] as &$item) {
            $item['room_name'] = isset($roomList[$item['room_id']]) ? $roomList[$item['room_id']] : "--";
            $item['auchor_name'] = isset($roomList[$item['auchor_id']]) ? $roomList[$item['auchor_id']] : "--";
        }
        
        return $list;
    }
}