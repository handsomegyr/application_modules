<?php
namespace App\Common\Models\Lottery;

use App\Common\Models\Base;

class Limit extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Lottery\Limit());
    }
}