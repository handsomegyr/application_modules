<?php
namespace Webcms\Common\Models\Prize;

use Webcms\Common\Models\Base;

class Code extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Prize\Code());
    }
}