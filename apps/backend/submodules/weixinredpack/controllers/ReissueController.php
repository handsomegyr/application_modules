<?php
namespace Webcms\Backend\Controllers\WeixinRedpack;

use Webcms\Backend\Models\WeixinRedpack\Reissue;

/**
 * @title({name="补发日志管理"})
 *
 * @name 补发日志管理
 */
class ReissueController extends \Webcms\Backend\Controllers\FormController
{

    private $modelReissue;

    public function initialize()
    {
        $this->modelReissue = new Reissue();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['logid'] = array(
            'name' => '红包日志ID',
            'data' => array(
                'type' => 'string',
                'length' => 24
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
        
        $schemas['redpack'] = array(
            'name' => '红包日志',
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
                'is_show' => true
            )
        );
        return $schemas;
    }

    protected function getName()
    {
        return '补发日志';
    }

    protected function getModel()
    {
        return $this->modelReissue;
    }
}