<?php
namespace App\Common\Models\Exchange;

use App\Common\Models\Base;

class Rule extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Exchange\Rule());
    }
}