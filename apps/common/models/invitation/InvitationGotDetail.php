<?php
namespace App\Common\Models\Invitation;

use App\Common\Models\Base;

class InvitationGotDetail extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Mysql\Invitation\InvitationGotDetail());
    }
}