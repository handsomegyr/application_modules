<?php
namespace Webcms\Common\Models\Member;

use Webcms\Common\Models\Base;

class Friend extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Member\Friend());
    }
}