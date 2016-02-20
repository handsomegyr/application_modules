<?php
namespace Webcms\Common\Models\Site;

use Webcms\Common\Models\Base;

class Suggestion extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Site\Suggestion());
    }
}