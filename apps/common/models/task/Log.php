<?php
namespace App\Common\Models\Task;

use App\Common\Models\Base;

class Log extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Task\Log());
    }
}