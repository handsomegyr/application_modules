<?php
namespace Webcms\Common\Models\System;

use Webcms\Common\Models\Base;

class Role extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\System\Role());
    }
}