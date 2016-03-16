<?php
namespace Webcms\Common\Models\Weixinredpack;

use Webcms\Common\Models\Base;

class Limit extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Weixinredpack\Limit());
    }
}