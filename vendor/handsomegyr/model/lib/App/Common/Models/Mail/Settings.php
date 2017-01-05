<?php
namespace App\Common\Models\Mail;

use App\Common\Models\Base\Base;

class Settings extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mail\Mysql\Settings());
    }
}