<?php
namespace App\Common\Models\Weixin;

use App\Common\Models\Base\Base;

class MenuType extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixin\Mysql\MenuType());
    }
}