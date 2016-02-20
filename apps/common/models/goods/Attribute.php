<?php
namespace Webcms\Common\Models\Goods;

use Webcms\Common\Models\Base;

class Attribute extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Goods\Attribute());
    }
}