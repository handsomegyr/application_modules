<?php
namespace App\Common\Models\Lottery;

use App\Common\Models\Base;

class Exchange extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Lottery\Exchange());
    }
}