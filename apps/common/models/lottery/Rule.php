<?php
namespace App\Common\Models\Lottery;

use App\Common\Models\Base\Base;

class Rule extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Lottery\Mysql\Rule());
    }
}