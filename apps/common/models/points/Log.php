<?php
namespace Webcms\Common\Models\Points;

use Webcms\Common\Models\Base;

class Log extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Points\Log());
    }
}