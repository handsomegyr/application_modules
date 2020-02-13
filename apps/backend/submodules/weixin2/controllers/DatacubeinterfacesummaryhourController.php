<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\DataCube\InterfaceSummaryHour;
use App\Backend\Submodules\Weixin2\Models\Authorize\Authorizer;
use App\Backend\Submodules\Weixin2\Models\Component\Component;
use App\Backend\Submodules\Weixin2\Models\RefHour;
/**
 * @title({name="接口分析分时数据"})
 *
 * @name 接口分析分时数据
 */
class DatacubeinterfacesummaryhourController extends \App\Backend\Controllers\FormController
{
    private $modelInterfaceSummaryHour;
    private $modelAuthorizer;
    private $modelComponent;
    private $modelRefHour;
    public function initialize()
    {
        $this->modelInterfaceSummaryHour = new InterfaceSummaryHour();
        $this->modelAuthorizer = new Authorizer();
        $this->modelComponent = new Component();
        $this->modelRefHour = new RefHour();

        $this->componentItems = $this->modelComponent->getAll();
        $this->authorizerItems = $this->modelAuthorizer->getAll();
        $this->refHourItems = $this->modelRefHour->getAll();
        parent::initialize();
    }
    protected $componentItems = null;
    protected $authorizerItems = null;

    protected function getSchemas()
    {
        $schemas = parent::getSchemas();
        $schemas['component_appid'] = array(
            'name' => '第三方平台应用ID',
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
        $schemas['ref_date'] = array(
            'name' => '数据的日期',
            'data' => array(
                'type' => 'datetime',
                'length' => 19,
                'defaultValue' => getCurrentTime()
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'datetimepicker',
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
        $schemas['ref_hour'] = array(
            'name' => '数据的小时',
            'data' => array(
                'type' => 'integer',
                'length' => 11,
                'defaultValue' => 0
            ),
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->refHourItems,
                'help' => '数据的小时，包括从000到2300，分别代表的是[000,100)到[2300,2400)，即每日的第1小时和最后1小时',
            ),
            'list' => array(
                'is_show' => true,
                'list_type' => '',
                'render' => '',
                'items' => $this->refHourItems,
            ),
            'search' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->refHourItems,
            ),
            'export' => array(
                'is_show' => true
            )
        );
        $schemas['callback_count'] = array(
            'name' => '被动回复用户消息的次数',
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
                'items' => '',
                'help' => '通过服务器配置地址获得消息后，被动回复用户消息的次数',
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
        $schemas['fail_count'] = array(
            'name' => '上述动作的失败次数',
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
        $schemas['total_time_cost'] = array(
            'name' => '平均耗时',
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
                'items' => '',
                'help' => '总耗时，除以callback_count即为平均耗时',
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
        $schemas['max_time_cost'] = array(
            'name' => '最大耗时',
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
        return '接口分析分时数据';
    }

    protected function getModel()
    {
        return $this->modelInterfaceSummaryHour;
    }
}
