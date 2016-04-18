<?php
namespace App\Common\Models\Goods;

use App\Common\Models\Base;

class Category extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Goods\Category());
    }
}