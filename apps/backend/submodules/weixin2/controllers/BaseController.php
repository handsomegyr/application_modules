<?php

namespace App\Backend\Submodules\Weixin2\Controllers;

use App\Backend\Submodules\Weixin2\Models\Authorize\Authorizer;
use App\Backend\Submodules\Weixin2\Models\Component\Component;
use App\Backend\Submodules\Weixin2\Models\Agent\Agent;

/**
 * @title({name="微信基础类"})
 *
 * @name 微信基础类
 */
class BaseController extends \App\Backend\Controllers\FormController
{
    private $modelAuthorizer;
    private $modelComponent;
    private $modelAgent;

    public function initialize()
    {
        $this->modelAgent = new Agent();
        $this->modelAuthorizer = new Authorizer();
        $this->modelComponent = new Component();

        $this->componentItems = $this->modelComponent->getAll();
        $this->authorizerItems = $this->modelAuthorizer->getAll();
        $this->agentItems = $this->modelAgent->getAll();
        parent::initialize();
    }
    protected $componentItems = null;
    protected $authorizerItems = null;
    protected $agentItems = null;
}
