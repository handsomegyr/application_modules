<?php
namespace Webcms\Common\Models\Tencent;

use Webcms\Common\Models\Base;

class User extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Tencent\User());
    }
}
