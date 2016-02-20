<?php
namespace Webcms\Common\Models\Points;

use Webcms\Common\Models\Base;

class User extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Points\User());
    }

}