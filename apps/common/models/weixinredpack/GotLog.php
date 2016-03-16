<?php
namespace Webcms\Common\Models\Weixinredpack;

use Webcms\Common\Models\Base;

class GotLog extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Weixinredpack\GotLog());
    }
}