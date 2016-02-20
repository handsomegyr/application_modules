<?php
namespace Webcms\Common\Models\Message;

use Webcms\Common\Models\Base;

class ReplyMsg extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Message\ReplyMsg());
    }
}