<?php
namespace App\Common\Models\Message;

use App\Common\Models\Base;

class MsgStatistics extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Message\MsgStatistics());
    }
}