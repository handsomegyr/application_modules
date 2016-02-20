<?php
namespace Webcms\Common\Models\Freight;

use Webcms\Common\Models\Base;

class Express extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Freight\Express());
    }
}