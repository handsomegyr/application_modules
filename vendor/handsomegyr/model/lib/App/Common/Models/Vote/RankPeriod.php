<?php
namespace App\Common\Models\Vote;

use App\Common\Models\Base\Base;

class RankPeriod extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Vote\Mysql\RankPeriod());
    }
}
