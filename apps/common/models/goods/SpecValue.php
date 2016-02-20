<?php
namespace Webcms\Common\Models\Goods;

use Webcms\Common\Models\Base;

class SpecValue extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Goods\SpecValue());
    }
}