<?php
namespace App\Common\Models\Goods\Mysql;

use App\Common\Models\Base\Mysql\Base;

class Images extends Base
{

    /**
     * 商品图片表管理
     * This model is mapped to the table igoods_images
     */
    public function getSource()
    {
        return 'igoods_images';
    }

    public function reorganize(array $data)
    {
        $data = parent::reorganize($data);
        $data['is_default'] = $this->changeToBoolean($data['is_default']);
        return $data;
    }
}