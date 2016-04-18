<?php
namespace App\Common\Models\Message;

use App\Common\Models\Base;

class SysMsg extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Message\SysMsg());
    }
}