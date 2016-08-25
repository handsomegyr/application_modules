<?php
namespace App\Backend\Submodules\Weixin\Controllers;

use App\Backend\Submodules\Weixin\Models\Gender;
use App\Backend\Submodules\Weixin\Models\ConditionalMenuMatchRule;

/**
 * @title({name="微信个性化菜单匹配规则管理"})
 *
 * @name 微信个性化菜单匹配规则管理
 */
class ConditionalmenumatchruleController extends \App\Backend\Controllers\FormController
{

    private $modelGender;

    private $modelConditionalMenuMatchRule;

    public function initialize()
    {
        $this->modelGender = new Gender();
        $this->modelConditionalMenuMatchRule = new ConditionalMenuMatchRule();
        parent::initialize();
    }

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        
        $schemas['_id']['list']['is_show'] = false;
        $schemas['_id']['search']['is_show'] = false;
        
        $schemas['matchrule_name'] = array(
            'name' => '名称',
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
        
        $schemas['group_id'] = array(
            'name' => '用户分组id',
            'data' => array(
                'type' => 'string',
                'length' => '5'
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
        
        $schemas['sex'] = array(
            'name' => '性别',
            'data' => array(
                'type' => 'integer',
                'length' => '10'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => function () {
                    return $this->modelGender->getAll();
                }
            ),
            'list' => array(
                'is_show' => true
            ),
            'search' => array(
                'is_show' => false
            )
        );
        
        $schemas['client_platform_type'] = array(
            'name' => '客户端版本',
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
                'is_show' => false
            )
        );
        
        $schemas['country'] = array(
            'name' => '国家',
            'data' => array(
                'type' => 'string',
                'length' => '10'
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
        
        $schemas['province'] = array(
            'name' => '省份',
            'data' => array(
                'type' => 'string',
                'length' => '10'
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
        
        $schemas['city'] = array(
            'name' => '城市',
            'data' => array(
                'type' => 'string',
                'length' => '10'
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
        
        $schemas['language'] = array(
            'name' => '语言',
            'data' => array(
                'type' => 'string',
                'length' => '5'
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
        return '微信个性化菜单匹配规则';
    }

    protected function getModel()
    {
        return $this->modelConditionalMenuMatchRule;
    }

    protected function getList4Show(\App\Backend\Models\Input $input, array $list)
    {
        $genderList = $this->modelGender->getAll();
        foreach ($list['data'] as &$item) {
            $item['sex'] = isset($genderList[$item['sex']]) ? $genderList[$item['sex']] : "--";
        }
        return $list;
    }
}