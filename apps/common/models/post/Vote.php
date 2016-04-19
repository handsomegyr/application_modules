<?php
namespace App\Common\Models\Post;

use App\Common\Models\Base\Base;

class Vote extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Post\Mysql\Vote());
    }
}