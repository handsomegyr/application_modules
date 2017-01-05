<?php
namespace App\Common\Models\Weixin;

use App\Common\Models\Base\Base;

class Page extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin\Mysql\Page());
    }
}