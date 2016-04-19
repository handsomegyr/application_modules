<?php
namespace App\Common\Models\Weixinredpack;

use App\Common\Models\Base\Base;

class Rule extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Weixinredpack\Mysql\Rule());
    }
}