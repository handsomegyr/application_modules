<?php
namespace Webcms\Common\Models\Mysql\System;

use Webcms\Common\Models\Mysql\Base;

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
