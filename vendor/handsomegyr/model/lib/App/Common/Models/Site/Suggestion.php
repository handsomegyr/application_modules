<?php
namespace App\Common\Models\Site;

use App\Common\Models\Base\Base;

class Suggestion extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Site\Mysql\Suggestion());
    }
}