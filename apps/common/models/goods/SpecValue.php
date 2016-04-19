<?php
namespace App\Common\Models\Goods;

use App\Common\Models\Base\Base;

class SpecValue extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Goods\Mysql\SpecValue());
    }
}