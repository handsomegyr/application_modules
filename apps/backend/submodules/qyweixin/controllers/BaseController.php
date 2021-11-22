<?php

namespace App\Backend\Submodules\Qyweixin\Controllers;

use App\Backend\Submodules\Qyweixin\Models\Authorize\Authorizer;
use App\Backend\Submodules\Qyweixin\Models\Provider\Provider;
use App\Backend\Submodules\Qyweixin\Models\Agent\Agent;

/**
 * @title({name="微信基础类"})
 *
 * @name 微信基础类
 */
class BaseController extends \App\Backend\Controllers\FormController
{
    private $modelAuthorizer;
    private $modelProvider;
    private $modelAgent;

    public function initialize()
    {
        $this->modelAgent = new Agent();
        $this->modelAuthorizer = new Authorizer();
        $this->modelProvider = new Provider();

        $this->providerItems = $this->modelProvider->getAll();
        $this->authorizerItems = $this->modelAuthorizer->getAll();
        $this->agentItems = $this->modelAgent->getAll();
        parent::initialize();
    }
    protected $providerItems = null;
    protected $authorizerItems = null;
    protected $agentItems = null;   

    protected function getFields4FormTool()
    {
        $fields = array();
        $fields['provider_appid'] = array(
            'name' => '第三方服务商应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->componentItems,
                'readonly' => true
            ),
        );
        $fields['authorizer_appid'] = array(
            'name' => '授权方应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->authorizerItems,
                'readonly' => true
            ),
        );
        $fields['agent_agentid'] = array(
            'name' => '微信企业应用ID',
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'input_type' => 'select',
                'is_show' => true,
                'items' => $this->agentItems,
            ),
        );
        return $fields;
    }
}
