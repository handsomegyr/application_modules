<?php
namespace App\Common\Models\Weixin;

use App\Common\Models\Base\Base;

class NotKeyword extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin\Mysql\NotKeyword());
    }
}