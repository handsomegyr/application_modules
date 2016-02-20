<?php
namespace Webcms\Common\Models\Goods;

use Webcms\Common\Models\Base;

class CategoryTag extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Goods\CategoryTag());
    }
}