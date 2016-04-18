<?php
namespace App\Common\Models\Weixin;

use App\Common\Models\Base;

class Reply extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin\Mysql\Reply());
    }
}