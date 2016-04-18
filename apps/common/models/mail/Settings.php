<?php
namespace App\Common\Models\Mail;

use App\Common\Models\Base;

class Settings extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Mail\Settings());
    }
}