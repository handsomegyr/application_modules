<?php
namespace Webcms\Common\Models\Weixinredpack;

use Webcms\Common\Models\Base;

class Rule extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Weixinredpack\Rule());
    }
}