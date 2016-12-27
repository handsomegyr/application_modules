<?php
namespace App\Common\Models\Activity;

use App\Common\Models\Base\Base;

class Category extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Activity\Mysql\Category());
    }
}