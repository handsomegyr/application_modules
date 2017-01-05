<?php
namespace App\Common\Models\Weixinredpack;

use App\Common\Models\Base\Base;

class Limit extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixinredpack\Mysql\Limit());
    }
}