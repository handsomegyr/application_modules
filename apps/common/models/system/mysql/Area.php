<?php
namespace Webcms\Common\Models\Mysql\System;

use Webcms\Common\Models\Mysql\Base;

class Area extends Base
{

    /**
     * This model is mapped to the table area
     */
    public function getSource()
    {
        return 'area';
    }
}