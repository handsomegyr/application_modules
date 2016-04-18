<?php
namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\Qrcode;

/**
 * @title({name="微信二维码推广场景管理"})
 *
 * @name 微信二维码推广场景管理
 */
class QrcodeController extends \App\Backend\Controllers\FormController
{

    private $modelQrcode;

    public function initialize()
    {
        $this->modelQrcode = new Qrcode();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['scene_id'] = array(
            'name' => '场景编号',
            'data' => array(
                'type' => 'string',
                'length' => '24'
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
        
        $schemas['openid'] = array(
            'name' => '微信用户ID',
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
                'is_show' => false
            )
        );
        
        $schemas['Event'] = array(
            'name' => '事件类型',
            'data' => array(
                'type' => 'string',
                'length' => '20'
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
        
        $schemas['EventKey'] = array(
            'name' => '事件KEY值',
            'data' => array(
                'type' => 'string',
                'length' => '20'
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
        
        $schemas['Ticket'] = array(
            'name' => '二维码的ticket',
            'data' => array(
                'type' => 'string',
                'length' => '150'
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
        
        return $schemas;
    }

    protected function getName()
    {
        return '微信二维码推广场景';
    }

    protected function getModel()
    {
        return $this->modelQrcode;
    }
}