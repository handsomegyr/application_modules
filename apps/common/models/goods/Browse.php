<?php
namespace Webcms\Common\Models\Goods;

use Webcms\Common\Models\Base;

class Browse extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Goods\Browse());
    }
}