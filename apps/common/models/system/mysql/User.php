<?php
namespace App\Common\Models\Mysql\System;

use App\Common\Models\Mysql\Base;

class User extends Base
{

    /**
     * This model is mapped to the table user
     */
    public function getSource()
    {
        return 'user';
    }
}
