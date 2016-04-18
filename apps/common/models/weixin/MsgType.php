<?php
namespace App\Common\Models\Weixin;

use App\Common\Models\Base;

class MsgType extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Weixin\MsgType());
    }
}