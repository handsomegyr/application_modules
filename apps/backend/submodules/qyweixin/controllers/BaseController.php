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
}
