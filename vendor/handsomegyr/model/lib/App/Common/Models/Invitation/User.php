<?php
namespace App\Common\Models\Invitation;

use App\Common\Models\Base\Base;

class User extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Invitation\Mysql\User());
    }
}