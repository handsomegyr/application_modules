<?php
namespace App\Common\Models\Prize;

use App\Common\Models\Base;

class Prize extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Prize\Prize());
    }
}