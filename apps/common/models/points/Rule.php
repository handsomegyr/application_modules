<?php
namespace App\Common\Models\Points;

use App\Common\Models\Base;

class Rule extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Points\Rule());
    }
}