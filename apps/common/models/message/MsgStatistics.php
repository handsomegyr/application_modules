<?php
namespace App\Common\Models\Message;

use App\Common\Models\Base\Base;

class MsgStatistics extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Message\Mysql\MsgStatistics());
    }
}