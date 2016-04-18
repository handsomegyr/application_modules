<?php
namespace App\Common\Models\Goods;

use App\Common\Models\Base;

class AttrIndex extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Goods\Mysql\AttrIndex());
    }
}