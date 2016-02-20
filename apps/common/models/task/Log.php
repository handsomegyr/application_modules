<?php
namespace Webcms\Common\Models\Task;

use Webcms\Common\Models\Base;

class Log extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Task\Log());
    }
}