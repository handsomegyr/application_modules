<?php
namespace Webcms\Common\Models\Article;

use Webcms\Common\Models\Base;

class Category extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Article\Category());
    }
}