<?php
namespace Webcms\Common\Models\Weixin;

use Webcms\Common\Models\Base;

class Menu extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Weixin\Menu());
    }
}