<?php
namespace App\Common\Models\Site;

use App\Common\Models\Base;

class Suggestion extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Site\Suggestion());
    }
}