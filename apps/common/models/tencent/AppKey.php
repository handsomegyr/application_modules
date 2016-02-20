<?php
namespace Webcms\Common\Models\Tencent;

use Webcms\Common\Models\Base;

class AppKey extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Tencent\AppKey());
    }
}