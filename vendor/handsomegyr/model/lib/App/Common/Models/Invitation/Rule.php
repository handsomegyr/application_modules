<?php
namespace App\Common\Models\Invitation;

use App\Common\Models\Base\Base;

class Rule extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Invitation\Mysql\Rule());
    }
}