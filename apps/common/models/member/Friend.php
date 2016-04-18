<?php
namespace App\Common\Models\Member;

use App\Common\Models\Base;

class Friend extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Member\Friend());
    }
}