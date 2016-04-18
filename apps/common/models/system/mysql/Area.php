<?php
namespace App\Common\Models\Mysql\System;

use App\Common\Models\Mysql\Base;

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