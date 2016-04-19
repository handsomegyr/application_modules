<?php
namespace App\Common\Models\Sms;

use App\Common\Models\Base\Base;

class Settings extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Sms\Mysql\Settings());
    }
}