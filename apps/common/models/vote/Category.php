<?php
namespace App\Common\Models\Vote;

use App\Common\Models\Base;

class Category extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Vote\Category());
    }
}
