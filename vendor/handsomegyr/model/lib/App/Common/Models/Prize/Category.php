<?php
namespace App\Common\Models\Prize;

use App\Common\Models\Base\Base;

class Category extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Prize\Mysql\Category());
    }
}