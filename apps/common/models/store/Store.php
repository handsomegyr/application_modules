<?php
namespace Webcms\Common\Models\Store;

use Webcms\Common\Models\Base;

class Store extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Store\Store());
    }
}