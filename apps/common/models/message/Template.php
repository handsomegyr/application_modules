<?php
namespace Webcms\Common\Models\Message;

use Webcms\Common\Models\Base;

class Template extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Message\Template());
    }
}