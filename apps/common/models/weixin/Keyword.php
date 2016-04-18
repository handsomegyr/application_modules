<?php
namespace App\Common\Models\Weixin;

use App\Common\Models\Base;

class Keyword extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Weixin\Keyword());
    }
}