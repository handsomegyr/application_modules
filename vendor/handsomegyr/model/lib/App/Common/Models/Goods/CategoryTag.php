<?php
namespace App\Common\Models\Goods;

use App\Common\Models\Base\Base;

class CategoryTag extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Goods\Mysql\CategoryTag());
    }
}