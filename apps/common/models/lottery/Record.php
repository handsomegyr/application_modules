<?php
namespace App\Common\Models\Lottery;

use App\Common\Models\Base;

class Record extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Lottery\Record());
    }
}