<?php
namespace Webcms\Common\Models\Invitation;

use Webcms\Common\Models\Base;

class InvitationGotDetail extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Invitation\InvitationGotDetail());
    }
}