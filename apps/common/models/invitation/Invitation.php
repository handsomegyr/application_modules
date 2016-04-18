<?php
namespace App\Common\Models\Invitation;

use App\Common\Models\Base;

class Invitation extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Invitation\Invitation());
    }
}