<?php
namespace Webcms\Common\Models\Message;

use Webcms\Common\Models\Base;

class MsgStatistics extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Message\MsgStatistics());
    }
}