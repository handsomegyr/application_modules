<?php
namespace Webcms\Common\Models\Mysql\System;

use Webcms\Common\Models\Mysql\Base;

class Source extends Base
{

    /**
     * 访问来源
     * This model is mapped to the table source
     */
    public function getSource()
    {
        return 'source';
    }
}