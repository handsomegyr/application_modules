<?php
namespace App\Common\Models\Tencent;

use App\Common\Models\Base\Base;

class AppKey extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Tencent\Mysql\AppKey());
    }
}