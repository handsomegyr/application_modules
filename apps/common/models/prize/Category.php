<?php
namespace Webcms\Common\Models\Prize;

use Webcms\Common\Models\Base;

class Category extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Prize\Category());
    }
}