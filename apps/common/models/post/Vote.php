<?php
namespace Webcms\Common\Models\Post;

use Webcms\Common\Models\Base;

class Vote extends Base
{

    function __construct()
    {
        $this->setModel(new \Webcms\Common\Models\Mysql\Post\Vote());
    }
}