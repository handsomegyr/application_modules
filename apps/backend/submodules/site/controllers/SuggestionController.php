<?php
namespace App\Backend\Submodules\Site\Controllers;

use App\Backend\Submodules\Site\Models\Suggestion;

/**
 * @title({name="网站投诉与建议管理"})
 *
 * @name 网站投诉与建议管理
 */
class SuggestionController extends \App\Backend\Controllers\FormController
{

    private $modelSuggestion;

    public function initialize()
    {
        $this->modelSuggestion = new Suggestion();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['theme'] = array(
            'name' => '主题',
            'data' => array(
                'type' => 'string',
                'length' => '100'
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
        
        $schemas['name'] = array(
            'name' => '昵称',
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
        
        $schemas['telephone'] = array(
            'name' => '电话',
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
        
        $schemas['email'] = array(
            'name' => 'E-mail',
            'data' => array(
                'type' => 'string',
                'length' => '100'
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
        
        $schemas['content'] = array(
            'name' => '反馈内容',
            'data' => array(
                'type' => 'string',
                'length' => '1000'
            ),
            'validation' => array(
                'required' => true
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
        $schemas['log_time'] = array(
            'name' => '记录时间',
            'data' => array(
                'type' => 'datetime',
                'length' => '19',
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        return '投诉与建议';
    }

    protected function getModel()
    {
        return $this->modelSuggestion;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        foreach ($list['data'] as &$item) {
            $item['log_time'] = date("Y-m-d H:i:s", $item['log_time']->sec);
        }
        
        return $list;
    }
}