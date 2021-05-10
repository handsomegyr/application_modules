<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Miniprogram\SubscribeMsg\Template\Template;
use App\Backend\Submodules\Weixin2\Models\Miniprogram\SubscribeMsg\Msg;

/**
 * @title({name="订阅消息"})
 *
 * @name 订阅消息
 */
class MiniprogramsubscribemsgController extends BaseController
{
    private $modelSubscribemsg;
    private $modelTemplate;
    public function initialize()
    {
        $this->modelSubscribemsg = new Msg();
        $this->modelTemplate = new Template();

        $this->templateItems = $this->modelTemplate->getAll();
        parent::initialize();
    }
    protected $templateItems = null;

    protected function getSchemas2($schemas)
    {
        $schemas['component_appid'] = array(
            'name' => '第三方平台应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->componentItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->componentItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->componentItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['authorizer_appid'] = array(
            'name' => '授权方应用ID',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->authorizerItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->authorizerItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->authorizerItems
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['name'] = array(
            'name' => '模板消息名称',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'text',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['template_id'] = array(
            'name' => '模板ID',
            'data' => array(
                'type' => 'string',
                'length' => 100,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->templateItems
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->templateItems
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->templateItems
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['data'] = array(
            'name' => '模板内容',
            'data' => array(
                'type' => 'json',
                'length' => 1024,
                'defaultValue' => '{ "key1": { "value": "" }, "key2": { "value": "" } }'
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'textarea',
                'is_show' => true,
                'items' => '',
                'help' => '模板内容，格式形如 { "key1": { "value": "" }, "key2": { "value": "" } }',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                // 扩展设置
                'extensionSettings' => function ($column, $Grid) {
                    $column->style('width:10%;word-break:break-all;');
                }
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['pageurl'] = array(
            'name' => '跳转链接',
            'data' => array(
                'type' => 'string',
                'length' => 255,
                'defaultValue' => ''
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'text',
                'content_type' => 'url',
                'is_show' => true,
                'items' => '',
                'help' => '点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $miniprogramStateOptions = array();
        $miniprogramStateOptions['developer'] = '开发版';
        $miniprogramStateOptions['trial'] = '体验版';
        $miniprogramStateOptions['formal'] = '正式版';

        $schemas['miniprogram_state'] = array(
            'name' => '跳转小程序类型',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => 'formal'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $miniprogramStateOptions,
                'help' => '跳转小程序类型：developer为开发版；trial为体验版；formal为正式版；默认为正式版',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $miniprogramStateOptions
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $miniprogramStateOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $langOptions = array();
        $langOptions['zh_CN'] = '简体中文';
        $langOptions['en_US'] = '英文';
        $langOptions['zh_HK'] = '繁体HK中文';
        $langOptions['zh_TW'] = '繁体TW中文';

        $schemas['lang'] = array(
            'name' => '语言类型',
            'data' => array(
                'type' => 'string',
                'length' => 50,
                'defaultValue' => 'zh_CN'
            ),
            'validation' => array(
                'required' => false
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $langOptions,
                'help' => '进入小程序查看”的语言类型，支持zh_CN(简体中文)、en_US(英文)、zh_HK(繁体中文)、zh_TW(繁体中文)，默认为zh_CN返回值',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $langOptions
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $langOptions
            ),
            'export' => array(
                'is_show' => true
            )
        );

        $schemas['priority'] = array(
            'name' => '权重',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'number',
                'is_show' => true,
                'items' => ''
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
            ),
            'search' => array(
                'is_show' => true
            ),
            'export' => array(
                'is_show' => true
            )
        );

        return $schemas;
    }

    protected function getName()
    {
        return '订阅消息';
    }

    protected function getModel()
    {
        return $this->modelSubscribemsg;
    }
}
