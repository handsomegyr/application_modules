<?php
namespace App\Common\Models\Prize\Mysql;

use App\Common\Models\Base\Mysql\Base;

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