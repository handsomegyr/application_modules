<?php
namespace Webcms\Common\Models\Mysql\Goods;

use Webcms\Common\Models\Mysql\Base;

class Combo extends Base
{

    /**
     * 商品推荐组合表管理
     * This model is mapped to the table igoods_combo
     */
    public function getSource()
    {
        return 'igoods_combo';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}