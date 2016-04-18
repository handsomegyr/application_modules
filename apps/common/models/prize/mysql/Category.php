<?php
namespace App\Common\Models\Mysql\Prize;

use App\Common\Models\Mysql\Base;

class Category extends Base
{

    /**
     * 奖品-奖品类别
     * This model is mapped to the table iprize_category
     */
    public function getSource()
    {
        return 'iprize_category';
    }
}