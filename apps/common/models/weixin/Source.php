<?php
namespace App\Common\Models\Weixin;

use App\Common\Models\Base;

class Source extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin\Mysql\Source());
    }
}