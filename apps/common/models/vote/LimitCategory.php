<?php
namespace Webcms\Common\Models\Vote;

use Webcms\Common\Models\Base;

class LimitCategory extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Vote\LimitCategory());
    }
}