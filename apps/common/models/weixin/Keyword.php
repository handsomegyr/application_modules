<?php
namespace Webcms\Common\Models\Weixin;

use Webcms\Common\Models\Base;

class Keyword extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Weixin\Keyword());
    }
}