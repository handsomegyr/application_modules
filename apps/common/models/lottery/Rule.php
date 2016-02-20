<?php
namespace Webcms\Common\Models\Lottery;

use Webcms\Common\Models\Base;

class Rule extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Lottery\Rule());
    }
}