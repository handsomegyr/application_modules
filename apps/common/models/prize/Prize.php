<?php
namespace Webcms\Common\Models\Prize;

use Webcms\Common\Models\Base;

class Prize extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Prize\Prize());
    }
}