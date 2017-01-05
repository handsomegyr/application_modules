<?php
namespace App\Common\Models\Article;

use App\Common\Models\Base\Base;

class Article extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Article\Mysql\Article());
    }
}