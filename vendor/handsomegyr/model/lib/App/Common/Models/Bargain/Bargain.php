<?php
namespace App\Common\Models\Bargain;

use App\Common\Models\Base\Base;

class Bargain extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Bargain\Mysql\Bargain());
    }
}
