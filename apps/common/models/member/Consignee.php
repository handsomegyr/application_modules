<?php
namespace Webcms\Common\Models\Member;

use Webcms\Common\Models\Base;

class Consignee extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Member\Consignee());
    }
}