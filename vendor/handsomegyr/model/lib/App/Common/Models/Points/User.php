<?php
namespace App\Common\Models\Points;

use App\Common\Models\Base\Base;

class User extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Points\Mysql\User());
    }

}