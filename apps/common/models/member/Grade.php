<?php
namespace App\Common\Models\Member;

use App\Common\Models\Base;

class Grade extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Member\Grade());
    }
}