<?php
namespace Webcms\Common\Models\Mysql\Goods;

use Webcms\Common\Models\Mysql\Base;

class Gift extends Base
{

    /**
     * 商品赠品表管理
     * This model is mapped to the table igoods_gift
     */
    public function getSource()
    {
        return 'igoods_gift';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        return $data;
    }
}