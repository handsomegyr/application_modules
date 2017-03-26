<?php
namespace App\Common\Models\Bargain\Mysql;

use App\Common\Models\Base\Mysql\Base;

class AlphaUser extends Base
{

    /**
     * 砍价-砍价用户系数表
     * This model is mapped to the table ibargain_alpha_user
     */
    public function getSource()
    {
        return 'ibargain_alpha_user';
    }
}
