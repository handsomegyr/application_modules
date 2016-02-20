<?php
namespace Webcms\Common\Models\Lottery;

use Webcms\Common\Models\Base;

class Record extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Lottery\Record());
    }
}