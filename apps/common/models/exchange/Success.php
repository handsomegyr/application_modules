<?php
namespace Webcms\Common\Models\Exchange;

use Webcms\Common\Models\Base;

class Success extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Exchange\Success());
    }
}